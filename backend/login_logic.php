<?php
	// This file expects a session to be open
	require_once(__DIR__."/common/sql.php");
	require_once(__DIR__."/common/sql_table_Customers.php");

	function format_error_message($errors) {
		$errors = "<p>Could not complete login:<br />".$errors."</p>";
		$errors = preg_replace("\<br \/\>\<br \/\>","</p><p>",$errors);
		return $errors;
	}

	function login($email,$pwd) {
		$msg = "";
		$sql = create_connection();
		if ($sql->connect_errno) {
			$msg = format_error_message("<br />MySQL error ".$sql->connect_errno.": ".$sql->connect_error);
		} else {
			$table = new MySQLTable($sql,$customersName);
			$noEmail = "<br />No user is registered under the given email address.";
			if ($table->exists()) {
				$email = trim($email);
				$pwd = trim($pwd);
				$emailEncoded = MySQLTable::escape($email);
				if ($table->exists("email",$emailEncoded)) {
					$emailEncoded = MySQLTable::encodeString($emailEncoded,TRUE);
					$pwd = MySQLTable::encodeString($pwd);
					$response = $table->select(NULL,"email=".$emailEncoded.", password=".$pwd);
					$res = mysqli_num_rows($response) !== 0;
					if (mysqli_num_rows($response) !== 0) {
						$msg = TRUE;
						$_SESSION["email"] = $email;
					} else {
						$msg = format_error_message("<br />Password is incorrect.");
					}
					$response->close();
				} else {
					$msg = format_error_message($noEmail);
				}
			} else {
				$msg = format_error_message($noEmail);
			}
			$sql->close();
		}
		return $msg;
	}
?>