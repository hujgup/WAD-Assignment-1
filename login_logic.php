<?php
	require_once(__DIR__.'/sql.php');
	require_once(__DIR__.'/sql_table_Customers.php');
	require_once(__DIR__.'/format.php');
	require_once(__DIR__.'/session.php');

	/*
		Formats login error messages into a readable form.

		@param $errors - string - The error string to format.
		@return string - The formatted error message.
	*/
	function format_login_error($errors)
	{
		return format_error_message('login', $errors);
	}

	/*
		Attempts to log a user into the system.

		@param $email - string - The user's email address.
		@param $pwd - string - The user's password.
		@return mixed - An error message if something went wrong, or TRUE if login was successful.
	*/
	function login($email, $pwd)
	{
		$msg = '';
		$sql = create_connection();
		if ($sql->connect_errno) {
			$msg = format_login_error('<br />MySQL error '.$sql->connect_errno.': '.$sql->connect_error);
		} else {
			$table = new MySQLTable($sql,Customers::NAME);
			$noEmail = '<br />No user is registered under the given email address.';
			if ($table->exists()) {
				$email = trim($email);
				$pwd = trim($pwd);
				$emailEncoded = $table->escape($email);
				if ($table->exists('email', $emailEncoded)) {
					$emailEncoded = $table->encode_string($emailEncoded, TRUE);
					$pwd = $table->encode_string($pwd);
					$response = $table->select(NULL, 'email='.$emailEncoded.' AND password='.$pwd);
					if (mysqli_num_rows($response) !== 0) {
						$msg = TRUE;
						Session::set_value('email', $email);
					} else {
						$msg = format_login_error('<br />Password is incorrect.');
					}
					$response->close();
				} else {
					$msg = format_login_error($noEmail);
				}
			} else {
				$msg = format_login_error($noEmail);
			}
			$sql->close();
		}
		return $msg;
	}
?>