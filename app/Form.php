<?php namespace App;

/*
* Form class
* Generates form input fields.
*/
class Form {

  /*
  * Generates labels and form input fields
  * @param string $label
  * @param string $type
  * @param string $name
  * @param string $id
  * @param string $value
  * @param array $errors
  * @param string $placeholder
  */
	public static function create_form_input($label='', $type, $name, $id='', $value ='', $errors=null, $placeholder='') {
		# Assume no value already exists:
		$value = false;
		$errors = array();

		# Create label
		echo '<label for="' .$name .'">' . $label .'</label><br/>';
		# Check for a value in POST:
		if (isset($_POST[$name])) $value = $_POST[$name];

		# Conditional to determine what kind of element to create:
		if( ($type == 'text') || ($type == 'password') || ($type == 'email')
			|| ($type == 'url') || ($type == 'file') || ($type == 'hidden') ) {
		  # Create text or password inputs.
			# Start creating the input:
			echo '<input class="form-control" type="' .$type. '" name="' .$name. '"  id= "' . $id. '" ';
			# Add the value to the input:
			if($value) echo ' value="' .htmlspecialchars($value). '" ';

			#  Check for an error: Create the error argument.
			if(array_key_exists($name, $errors)) {
			   echo 'class="error"/><span class="error">' .$errors[$name]. '</span>';
			} else {
				  # Placeholder is last argument
					echo ' placeholder="' .$placeholder . '"/>';
			}
		}
		# Create a TEXTAREA.
		elseif ($type == 'textarea') {
		# Display the error first:
		if(array_key_exists($name, $errors)) echo '<span class="error">' . $errors[$name] . '</span>';
		  # Start creating the textarea:
		  echo '<textarea class="span4" name="' . $name . '" id="' . $name . '" rows="8" cols="75" ';
		  # Add the error class, if applicable:
			if(array_key_exists($name, $errors)) {
				 echo ' class="error">';
			} else {
				 echo '>';
			}
			# Add the value to the textarea:
			if ($value) echo $value;
			# Complete the textarea:
		  echo '</textarea>';
		}
		# end of primary IF-ELSE.

	}

} # end class



