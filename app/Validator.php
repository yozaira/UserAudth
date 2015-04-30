<?php  namespace App;

/**
* Validator Class
* @package PHP Form Validator
*/
class Validator {

    // Define regular expression patterns used in validation
    // Define error messages
    // Create property for form data
    // Create property for validation rules
    // Create property for errors
    // Construct the object
    // Sanitize form data
    // Initialize form data property
    // Initialize validation rules property
    // Validate Method
    // Loop through form fields
    // Validate field
    // Throw any errors, and store them
    // Return result
    // Create utility methods for validation, such as:
    // Check email
    // Check alphanumeric
    // Check minimum, maximum length
    // Return form data
    // Return validation errors

    /**
    * Regular expression used to match email addresses.
    * Stored as string using Perl-compatible (PCRE) syntax.
    * Final i represents a case-insensitive pattern
    *
    * @link http://www.regular-expressions.info
    */
    const REGEX_EMAIL  = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}$/i';
    const REGEX_PHONE  = '/^(?:\([2-9]\d{2}\)\ ?|[2-9]\d{2}(?:\-?|\ ?))[2-9]\d{2}[- ]?\d{4}$/';
    const REGEX_ALPHA  = '/^[A-Z.]+$/i';
    const REGEX_ALPHA_NUM = '/^[A-Z0-9._+-]+$/i';

    /**
     * Error message definitions
     */
    const ERROR_REQUIRED   = ' is required.';
    # leave a space at the beginning so
    # it doent collide with the variable on error message
    const ERROR_ALPHA      = ' should contain only alphabetical characters';
    const ERROR_ALPHA_NUM  = ' should contain both letters and numbers';
    const ERROR_EMAIL      = ' is invalid. Please enter a valid email.';
    const ERROR_PHONE      = ' is invalid. Please enter a valid phone number.';
    const ERROR_MIN_LENGTH = ' must have a minimum length of 6 characters.';
    const ERROR_MAX_LENGTH = ' exceeds the 20 character maximum length.';
    const ERROR_NUMERIC    = ' field should hold a numeric value.';
    const ERROR_EXISTS     = ' is already being used.';
    const ERROR_MATCH      = ' field must match ';

    /**
    * Form fields data
    * @var array
    */
    private $fields = array();

    /**
    * Form fields names
    * @var array
    */
    private $_fieldName = '';

    /**
    * Validation rules
    * @var array
    */
    private $rules  = array();

    // private  $rules = array(
      // # General example
      // 'field_name' => array(
      // 'check_name' => 'check_value'
      // ),
      // # A real example
      // 'email' => array (
      // 'required' => true,
      // 'email'   => true
      // ) );

    /**
    * Errors are stored in an array
    * and returned on validation finish
    * @var array
    */
    private $errors = array();

    # As the data is passed to the constructor, the sanitize() method is called.
    # Sanitize fetches the form data, passes it to filter_var_array() and
    # data is sanitized.
    /**
    * Validator Constructor
    *
    * @param array $form_data data submitted via form
    * @param array $rules validation rules configured by user
    */
    function __construct($form_data, $rules_data) {
      $this->fields = $this->sanitize($form_data);
      $this->rules = $rules_data;
    }


    /**
    * Function calls filter_var_array() to filter form data
    *
    * @param type $sanitized_data  data to be sanitized
    * @return mixed    sanitized form data
    */
    private function sanitize($form_data) {
      $sanitized_data = filter_var_array($form_data, FILTER_SANITIZE_STRING);
      // Return the sanitized datas
      return $sanitized_data;
    }



    /**
    * Function handles form data validation
    *
    * @return array
    * an array which includes validation errors
    */
    public function validate() {
      # Validate each form field
      foreach ($this->fields as $field => $value) {
        // var_dump($this->fields);
        // var_dump($value);
        // echo '....'.$this->fields[$field]. '<br/>';

        # If the field value is empty
        if(empty($value)) {
          # If the field is set as required, throw error
          if(isset($this->rules[$field]['required'])) {
            # This will output something like this:  "Name is required"
            $this->errors[$field][] = $this->rules[$field]['fieldName']. self::ERROR_REQUIRED;
            # NOTE --> $this->fieldName does not work here
          }
        }
        # if the field has a value and is declared in Rules
        else if (isset($this->rules[$field])) {
          // var_dump($this->rules[$field] );
          # Remove 'required' from list of callable functions.
          # We already did this check above.
          unset($this->rules[$field]['required']);

          foreach ($this->rules[$field] as $rule => $rule_value) {
            //  var_dump($rule);
            // var_dump($rule_value);

            /**
            * For each rule specified for an element,
            * call a function with the same name, e.g. 'email()' when
            * checking whether a field value is a valid email address.
            *
            * This replaces the previous switch statement, and reduces
            * the need to iterate through each switch case for every
            * rule.
            */
            # http://www.php.net/call_user_func_array
            call_user_func_array(
            # Function is in this instance, named identical to rule
            array($this, $rule),
            # Pass the Field name, Field value, and Rule value
            array($field, $value, $rule_value)
            );
          }
        }
      }
      # Return validation result
      if(empty($this->errors)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }



    # I created on 15/9/2014
    /**
    * Function checks db to see if the $value string is a unique filed.
    * Avoids that a repeated valued is entered in the db.
    *
    * @param string $field string to be checked
    * @param string $value string with value of the $field
    * @return boolean true if string is unique. false otherwise
    */
    private function unique($field, $value) {
      // echo 'Unique field '.$field. '<br/>';
      // echo 'Unique value ' .$value. '<br/>';
      // echo  'Unique rule ' .$this->rules[$field]['unique']. '<br/>';

      $find = DB::getInstance()->findField($field, 'users', $field, $value) ;
      // var_dump($find);
      foreach( $find as $found ) {
        foreach( $found as $key => $fieldFound ) {
          if($fieldFound == $value) {
            # echo $fieldFound. ' already exists'. '<br/>';
            $this->errors[$field][] = $fieldFound . self::ERROR_EXISTS;
          }
        }
      }
    }


    # I created on 16/9/2014
    /**
    * Function uses the field value to add a name to the field being validated.
    *
    * @param string $field string to be checked
    * @return name of the field specified on the rules
    */
    private function fieldName($field) {
      $this->_fieldName = $this->rules[$field]['fieldName'];
      return $this->_fieldName ;
    }



    # I created on 15/9/2014

    /**
    * Function checks if two fields match
    *
    * @param string $field string to be checked
    * @param string $value string with value of the $field
    * @param string $field_match string that must match the $field
    * @return false if the two field field specified on the rules dont match
    */
    private function matches($field, $value, $field_match) {
      // var_dump($field);  # pw-confirm
      // var_dump($this->rules);
      // var_dump($this->rules[$field] );
      // echo $this->rules[$field]['matches'];

      // echo $field_match. '<br/>';
      // echo $field. '<br/>';
      // echo $_POST[$field_match]. '<br/>';        # value of field_match
      // echo $this->fields[$field_match]. '<br/>'; # value of field_match
      // echo $value. '<br/>';                      # value of field

      $field_match =  $this->rules[$field]['matches'];
      if($value !== $this->fields[$field_match]) {
        $this->errors[$field][] =  $this->_fieldName . self::ERROR_MATCH . ucfirst($field_match);
        // $this->fieldName($field) .
      }
    }



    /**
    * Function checks if string is an email address.
    *
    * @param string $string string to be checked
    * @return boolean true if string is email. false otherwise
    */
    private function email($field, $value) {
      if(!preg_match(self::REGEX_EMAIL, $value)) {
        $this->errors[$field][] = $this->_fieldName .self::ERROR_EMAIL;
      }
    }



    /**
    * Function checks if string is a phone number.
    *
    * @param string $string string to be checked
    * @return boolean true if string is phone. false otherwise
    */
    # US Phone Number: This regular expression for US phone numbers conforms to
    # NANP A-digit and D-digit requirments (ANN-DNN-NNNN). Area Codes 001-199
    # are not permitted; Central Office Codes 001-199 are not permitted.
    # Format validation accepts 10-digits without delimiters, optional parens
    # on area code, and optional spaces or dashes between area code, central
    # office code and station code. Acceptable formats include  2225551212,
    # 222 555 1212, 222-555-1212, (222) 555 1212, (222) 555-1212, etc.
    # You can add/remove formatting options to meet your needs
    private function phone($field, $value) {
      if(!preg_match(self::REGEX_PHONE, $value)) {
        $this->errors[$field][] = $this->_fieldName . self::ERROR_PHONE;
      }
    }



    /**
    * Function returns FALSE if the field contains
    * anything other than alphabetical characters.
    *
    * @param type $string
    * @return boolean
    */
    private function alpha($field, $value) {
      if(!preg_match(self::REGEX_ALPHA, $value)) {
        $this->errors[$field][] = $this->_fieldName . self::ERROR_ALPHA;
      }
    }



    /**
    * Function returns FALSE is the field contains
    * anything other than alphanumerical characters,
    * and special characters such as: plus, dash, underscore
    *
    * @param string $string
    * @return boolean
    */
    private function alpha_num($field, $value) {
      if(!preg_match(self::REGEX_ALPHA_NUM, $value)) {
        $this->errors[$field][] = $this->_fieldName . self::ERROR_ALPHA_NUM;
      }
    }



    /**
    * Function checks whether the input
    * holds a numeric vaklue.
    *
    * @param mixed $input the value to check
    * @return
    */
    private function numeric($field, $value) {
      if(!is_numeric($value)) {
        $this->errors[$field][] = $this->_fieldName . self::ERROR_NUMERIC;
      }
    }



    /**
    * Function checks whether the input
    * is longer than a specified minimum length
    * and returns a boolean
    *
    * @param mixed $input  the string or value to check
    * @param int $length   the minimum length required
    * @return boolean
    */
    private function min_length($field, $value, $min_length) {
      $length = strlen($value);
      // Throw error is field length does not meet minimum
      if($length < $min_length) {
        $this->errors[$field][] = $this->_fieldName .self::ERROR_MIN_LENGTH;
      }
    }



    private function max_length($field, $value, $max_length) {
      $length = strlen($value);
      // Throw error is field length does not meet minimum
      if ($length > $max_length) {
        $this->errors[$field][] = $this->_fieldName .self::ERROR_MAX_LENGTH;
      }
    }



    /**
    * Function returns form data
    *
    * @return array form fields
    */
    public function get_fields() {
      return $this->fields;
    }



    /**
    * Function returns errors captured by the validator.
    *
    * @return array form validation errors
    */
    public function get_errors() {
      return $this->errors;
    }



    /**
    * Function returns errors encoded as JSON
    *
    * @return string
    */
    public function get_errors_json() {
      return json_encode($this->errors);
    }


} # end class

?>