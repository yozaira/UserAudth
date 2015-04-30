<?php namespace App;

/*
* DB class
* Connects to MySQL db using PDO.
*/
class DB {

    /*
    * private static $_instance
    * @var string stores an instance of db connection
    */
    private static $_instance = null;
    /*
    * private $_pdo
    * @var string stores an instance of the pdo object
    */
    private $_pdo;
    /*
    * private $_query
    * @var string stores the last query
    */
    private $_query;
    /*
    * private $_error
    * @var bool output when the query fails
    */
    private $_error = false;
    /*
    * private $_results
    * @var array result set from query
    */
    private $_results;
    /*
    * private $_count
    * @var array resutl count
    */
    private $_count = 0;


    private function  __construct() {
      # using try/catch to connect to db is particularly important for
      # db pw-protected, bc an uncought exception and expose db
      # username and pw.
      #
      # NOTE the slash before PDO. When doing this you are loading the class
      # for the global namespace. Otherwise it will be loaded from the
      # current namespace.
      # http://stackoverflow.com/questions/13426252/pdo-out-of-scope-php-composer
      try{
        $this->_pdo = new \PDO('mysql:host=' .Config::get('mysql/host'). '; dbname=' .Config::get('mysql/db'),
                               Config::get('mysql/username'),  Config::get('mysql/password')
                              );
      }
      catch(PDOException $e) {
      die($e->getMessage() );
      }
    }


    /*
    * Returns an instance of the class.
    * This ensures that there is only ONE single
    * connection open within the context of a
    * single HTTP request.
    * @return void
    */
    public static function getInstance() {
      # check if in instance of the db exits before
      # creating it.
      if (!isset(self::$_instance))  {
         $class = __CLASS__;
         self::$_instance = new $class;
      }
      return self::$_instance;
    }



    /*
    * Execute a query to find one or more items on a table
    * @param string $sql query string
    * @param array $params item/s to be found
    * @return array with values found as objects
    */
    public function query($sql, $params = array()) {
      $this->_error = false;
      # prepare query
      if ($this->_query = $this->_pdo->prepare($sql)) {
          // echo "Query prepared successful <br/>";
          # if param array has items
          if(count($params)) {
            // var_dump($params);
            #create a counter to determine the position
            # of array items and bind them as values
            $x = 1;
            foreach ($params as $param) {
              $this->_query->bindValue($x, $param);
              $x++;
            }
            # if query excecutes, return result
            if($this->_query->execute()) {
              # PDOStatement::fetchObject — Fetches the next row and returns
              # it as an object.
              # Read user note warning about the use of this methodology.
              # http://php.net/manual/en/pdostatement.fetchobject.php
              #
              # NOTE the slash before PDO. When doing this you
              # are loading the class for the global namespace.
              # Otherwise it will be loaded from the current namespace.
              # http://stackoverflow.com/questions/13426252/pdo-out-of-scope-php-composer
              $this->_results = $this->_query->fetchAll(\PDO::FETCH_OBJ);
              $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
          }
          # var_dump($this);
          # return the object we are working with
          return $this;
      }
    }


    /**
    * Returns erros if query fails
    * @return boolean
    */
    public function error() {
      return $this->_error;
    }


    /*
    * Executes a query on a specific table to find a specific item
    * @param string $action
    * @param string $table
    * @param array $where with field name, type  of operator and
    * value or the field to be searched
    * @return array with values found as objects
    */
    public function action($action, $table, $where = array()) {
      if(count($where) === 3) {
        # define list of allowed operators
        $operators =  array(
                        '=',
                        '>' ,
                        '<',
                        '>=',
                        '<='
                      );
        $field    = $where[0];  # name of the field in db
        $operator = $where[1];  # type of operator
        $value    = $where[2];  # value of the field
        # operator is in operators array
        if(in_array($operator, $operators)) {
          # param $action is the type of query and ? is the value to be binded
          $sql = " {$action} FROM {$table} WHERE {$field} {$operator} ? ";
          # $value is the value that will be replaced by the question mark.
          # if no errors, return query
          # note the use of query method created above
          if(!$this->query($sql, array($value) )->error()) {
            return $this;
          }
        }
        # NOTE: This makes rule unique on validation return always true
         return false;
      }
    }



    /*
    * Execute a query on specific table to find a specific item
    * Uses action method created above to return result
    * @param string $table
    * @param array $where with field name, type of operator and
    * value or the field to be retrieved
    * @return array with values found as objects
    */
    public function get($table, $where) {
      return $this->action("SELECT * ", $table, $where);
    }


    /*
    * Deletes item from a table
    * Uses action method to execute the query
    * @param string $table
    * @param array $where with field name, type of
    * operator and value or the field to be deleted
    * @return void
    */
    public function delete($table, $where) {
      return $this->action("DELETE", $table, $where);
    }



    /*
    * Retuns a result set from the query method
    * @return array with values found as objects
    */
    public function getResults() {
      return $this->_results;
    }


    /*
    * Retuns a count of items items resulting from
    * query method
    * @return int number of items found
    */
    public function resultCount() {
      return $this->_count;
    }



    /*
    * Retuns objects from result set array
    * It allows to access itmes found in db
    * as objects.
    * @return object of db items
    */
    public function first() {
      return $this->_results[0];
    }



    /*
    * Inseerts data in db
    * @param string $table
    * @param array $fields
    * @return boolean
    */
    public function insert($table, $fields = array() ) {
      # if we have data in our fields array
      if(count($fields)) {
        # find the keys of the array. Each key corresponds
        # to the fields in the db
        $keys = array_keys($fields);
        # keep track questions marks inside the query.
        # ? are placeholders for the values the will
        # be inserted
        $values = '';
        # set a counter to loop over each field
        $x = 1;
        foreach($fields as $field) {
          $values .= ' ? ';
          if($x < count($fields)) {
             # add a comma and space between each ?
            $values .= ' ,  ';
          }
          $x++;
        }
        # implode func will take the keys in the array and create a string with a backtick separator
        # if the backtick are too separated from the sigle quotes, it wont insert data.
        $sql = "INSERT INTO {$table} (`" .implode( '` ,  `' , $keys). "`) VALUES ( {$values} ) ";
         # echo the query to test the insert method and to check if backtics are place correctly.
         // echo $sql;
        if(!$this->query($sql, $fields)->error()) {
          return true;
        }
      }
      return false;
    }



    /*
    * Updates fields in db
    * @param string $table
    * @param string $id
    * @param array $fields
    * @return boolean
    */
    public function update($table, $id, $fields) {
      # define var for field name
      $set = '';
      $x= 1;
      foreach($fields as $name => $value) {
        $set .= " {$name} = ?";
        if($x < count($fields)) {
          # add comma and space to separate each
          # fields if there are more than one
          $set .= ',  ';
        }
        $x++;
      }
      $sql = "UPDATE {$table} SET {$set} WHERE id = {$id} ";
      # test query to see ouput.
      // echo $sql;
      if (!$this->query($sql, $fields)->error()  ){
        return true;
      }
      return false;
    }


    /*
    * alternative method to find one row on a table.
    * @param string $item to be found
    * @param string $tale db table where item is
    * @param string $field
    * @param string $param
    * @return array of values found
    */
    public function findField($item, $table, $field, $param) {
      $this->_error = false;
      $sql = "SELECT `{$item}`
              FROM  `{$table}`
              WHERE `{$field}` = ?";
      $statement = $this->_pdo->prepare($sql);
      # if query excecutes, return result
      if($statement->execute(array($param)) ){
        # PDOStatement::fetchObject — Fetches the next row and returns it as an object.
        # Read user note warning about the use of the methodology.
        # http://php.net/manual/en/pdostatement.fetchobject.php
        #
        # NOTE the slash before PDO. When doing this you
        # are loading the class for the global namespace.
        # Otherwise it will be loaded from the current namespace.
         return $statement->fetchAll(\PDO::FETCH_ASSOC);
      } else {
        $this->_error = true;
      }
    }


    /*
    * alternative method to find all rows on a table.
    * @param string $tale db table
    * @return array of values found
    */
    public function getAll($table) {
      if(!empty($table) ) {
        $sql = "SELECT *
                FROM `{$table}` ";
        $statement = $this->_pdo->prepare($sql) ;
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
      }
      return false;
    }


} # end class

