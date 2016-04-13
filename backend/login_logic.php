<?php
	// This file expects a session to be open
	require_once(__DIR__."/common/sql.php");
	require_once(__DIR__."/common/sql_table_Customers.php");
	require_once(__DIR__."/common/format.php");
	require_once(__DIR__."/common/session.php");

	function format_login_error($errors) {
		return format_error_message("login",$errors);
	}

	function login($email,$pwd) {
		$msg = "";
		$sql = create_connection();
		if ($sql->connect_errno) {
			$msg = format_login_error("<br />MySQL error ".$sql->connect_errno.": ".$sql->connect_error);
		} else {
			global $customersName;
			$table = new MySQLTable($sql,$customersName);
			$noEmail = "<br />No user is registered under the given email address.";
			if ($table->exists()) {
				$email = trim($email);
				$pwd = trim($pwd);
				$emailEncoded = $table->escape($email);
				if ($table->exists("email",$emailEncoded)) {
					$emailEncoded = $table->encodeString($emailEncoded,TRUE);
					$pwd = $table->encodeString($pwd);
					$response = $table->select(NULL,"email=".$emailEncoded." AND password=".$pwd);
					if (mysqli_num_rows($response) !== 0) {
						$msg = TRUE;
						Session::setValue("email",$email);
					} else {
						$msg = format_login_error("<br />Password is incorrect.");
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