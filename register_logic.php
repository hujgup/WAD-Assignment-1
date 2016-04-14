<?php
	/*
		COS30030 Web Application Development - Assignment 1
		Author: Jake Tunaley (Student I.D. 100593584)

		Purpose: Provides the logic for the Register page.
	*/

	require_once(__DIR__.'/expecting.php');
	require_once(__DIR__.'/sql.php');
	require_once(__DIR__.'/sql_table_Customers.php');
	require_once(__DIR__.'/format.php');

	/*
		Formats registration error messages into a readable form.

		@param $errors - string - The error string to format.
		@return string - The formatted error message.
	*/
	function format_register_error($errors)
	{
		return format_error_message('registration', $errors);
	}

	/*
		Attempts to register a new user.

		@return mixed - An empty string if no inputs were present, an error message if something went wrong, or TRUE if registration was successful.
	*/
	function register()
	{
		$msg = '';
		$email = 'email';
		$pwd = 'pwd';
		$pwdConfirm = 'pwdConfirm';
		$name = 'cname';
		$phone = 'phone';
		if (expecting($_POST, array($email, $pwd, $pwdConfirm, $name, $phone))) {
			$email = trim($_POST[$email]);
			$pwd = trim($_POST[$pwd]);
			$pwdConfirm = trim($_POST[$pwdConfirm]);
			$name = trim($_POST[$name]);
			$phone = preg_replace('/\s/', '', $_POST[$phone]);

			$errors = '';
			if ($pwd !== $pwdConfirm) {
				$errors .= '<br />Passwords do not match.';
			}
			if (preg_match('/[^\d\+][^\d]+/', $phone)) {
				$errors .= '<br />Phone number may only contain letters, whitespace, and optionally a plus symbol at the beginning.';
			}
			if (strlen($email) > 32) {
				$errors .= '<br />Email too long: Cannot exceed 32 characters.';
			}
			if (strlen($pwd) > 32) {
				$errors .= '<br />Password too long: Cannot exceed 32 characters.';
			}
			if (strlen($name) > 64) {
				$errors .= '<br />Name too long: Cannot exceed 64 characters.';
			}
			if (strlen($phone) > 10) {
				$errors .= '<br />Phone number too long: Cannot exceed 10 non-whitespace characters.';
			}

			if ($errors === '') {
				$sql = create_connection();
				if ($sql->connect_errno) {
					$msg = format_register_error('<br />MySQL error '.$sql->connect_errno.': '.$sql->connect_error);
				} else {
					$table = new MySQLTable($sql, Customers::NAME);
					$email = $table->encode_string($email);
					// No point checking if things exist after just creating the table
					if (!$table->create(Customers::$STRUCTURE)) {
						if ($table->exists('email', $email, FALSE)) {
							$errors .= '<br />Email address is already in use.';
						}
					}

					if ($errors === '') {
						$pwd = $table->encode_string($pwd);
						$name = $table->encode_string($name);
						$phone = $table->encode_string($phone);
						$table->add_row(array($email, $pwd, $name, $phone));
						$msg = TRUE;
					} else {
						$msg = format_register_error($errors);
					}
					$sql->close();
				}
			} else {
				$msg = format_register_error($errors);
			}
		}
		return $msg;
	}
?>