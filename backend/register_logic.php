<?php
	require_once(__DIR__."/common/expecting.php");
	require_once(__DIR__."/common/sql.php");
	require_once(__DIR__."/common/sql_table_Customers.php");
	require_once(__DIR__."/common/format.php");

	function format_register_error($errors) {
		return format_error_message("registration",$errors);
	}

	function register() {
		$msg = "";
		$email = "email";
		$pwd = "pwd";
		$pwdConfirm = "pwdConfirm";
		$name = "cname";
		$phone = "phone";
		if (expecting($_POST,array($email,$pwd,$pwdConfirm,$name,$phone))) {
			$email = trim($_POST[$email]);
			$pwd = trim($_POST[$pwd]);
			$pwdConfirm = trim($_POST[$pwdConfirm]);
			$name = trim($_POST[$name]);
			$phone = preg_replace("/\s/","",$_POST[$phone]);

			$errors = "";
			if ($pwd !== $pwdConfirm) {
				$errors .= "<br />Passwords do not match.";
			}
			if (preg_match("/[^\d\+][^\d]+/",$phone)) {
				$errors .= "<br />Phone number may only contain letters, whitespace, and optionally a plus symbol at the beginning.";
			}
			if (strlen($email) > 32) {
				$errors .= "<br />Email too long: Cannot exceed 32 characters.";
			}
			if (strlen($pwd) > 32) {
				$errors .= "<br />Password too long: Cannot exceed 32 characters.";
			}
			if (strlen($name) > 64) {
				$errors .= "<br />Name too long: Cannot exceed 64 characters.";
			}
			if (strlen($phone) > 10) {
				$errors .= "<br />Phone number too long: Cannot exceed 10 non-whitespace characters.";
			}

			if ($errors === "") {
				$sql = create_connection();
				if ($sql->connect_errno) {
					$msg = format_register_error("<br />MySQL error ".$sql->connect_errno.": ".$sql->connect_error);
				} else {
					global $customersName;
					global $customersStructure;
					$table = new MySQLTable($sql,$customersName);
					$email = $table->encodeString($email);
					if (!$table->create($customersStructure)) { // No point checking if things exist after just creating the table
						if ($table->exists("email",$email)) {
							$errors .= "<br />Email address is already in use.";
						}
					}

					if ($errors === "") {
						$pwd = $table->encodeString($pwd);
						$name = $table->encodeString($name);
						$phone = $table->encodeString($phone);
						$table->addRow(array($email,$pwd,$name,$phone));
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