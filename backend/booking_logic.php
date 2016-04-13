<?php
	require_once(__DIR__."/common/session.php");
	require_once(__DIR__."/common/expecting.php");
	require_once(__DIR__."/common/sql.php");
	require_once(__DIR__."/common/sql_table_Customers.php");
	require_once(__DIR__."/common/sql_table_Bookings.php");
	require_once(__DIR__."/common/format.php");
	require_once(__DIR__."/common/timing.php");

	function resolve_post_ref(&$var) {
		$var = trim($_POST[$var]);
	}
	function format_booking_error($errors) {
		return format_error_message("booking",$errors);
	}
	function date_invalid($date) {
		$res = TRUE;
		if (preg_match("/\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])/",$date)) {
			list($year,$month,$day) = explode("-",$date);
			$year = intval($year);	
			$month = intval($month);
			$day = intval($day);
			if ($month === 2) { // February
/*
				Gregorian calendar leap-year rules:
					If the year is a multiple of 400 then it is a leap year
					Else if the year is a multiple of 100 then it isn't a leap year
					Else if the year is a multiple of 4 then it is a leap year
*/
				$res = $day > (($year%400 === 0 || ($year%100 !== 0 && $year%4 === 0)) ? 29 : 28);
			} else {
				$months30Days = array(
					4, // April
					6, // June
					9, // September
					11 // November
				);
				$res = $day > (in_array($month,$months30Days) ? 30 : 31);
			}
		}
		return $res;
	}
	function time_in_past($date,$time) {
		return seconds_until($date,$time) < 0;
	}

	function book() {
		$msg = "";
		$passengerName = "pname";
		$phone = "phone";
		$unit = "unit";
		$streetNum = "streetNum";
		$streetName = "streetName";
		$suburb = "suburb";
		$destination = "dest";
		$pickupDate = "pickupDate";
		$pickupTime = "pickupTime";
		if (expecting($_POST,array($passengerName,$phone,$streetNum,$streetName,$suburb,$destination,$pickupDate,$pickupTime))) {
			resolve_post_ref($passengerName);
			resolve_post_ref($phone);
			resolve_post_ref($streetNum);
			resolve_post_ref($streetName);
			resolve_post_ref($suburb);
			resolve_post_ref($destination);
			resolve_post_ref($pickupDate);
			resolve_post_ref($pickupTime);
			$unitExists = isset($_POST[$unit]) && $_POST["unit"] !== "";
			if ($unitExists) {
				resolve_post_ref($unit);
			}
			$phone = preg_replace("/\s/","",$phone);
			$email = Session::getValue("email");

			$errors = "";
			if (preg_match("/[^\d\+][^\d]+/",$phone)) {
				$errors .= "<br />Phone number may only contain letters, whitespace, and optionally a plus symbol at the beginning.";
			}
			if (strlen($passengerName) > 64) {
				$errors .= "<br />Passenger name too long: Cannot exceed 64 characters.";
			}
			if (strlen($phone) > 10) {
				$errors .= "<br />Phone number too long: Cannot exceed 10 non-whitespace characters.";
			}
			$streetNumInt = intval($streetNum);
			if (floatval($streetNum) != $streetNumInt) {
				$errors .= "<br />Street number invalid: Only integers are allowed.";
			}
			if ($streetNumInt <= 0) {
				$errors .= "<br />Street number invalid: Only positive values are allowed.";
			}
			if (strlen($streetName) > 32) {
				$errors .= "<br />Street name too long: Cannot exceed 32 characters.";
			}
			if (strlen($suburb) > 32) {
				$errors .= "<br />Suburb too long: Cannot exceed 32 characters.";
			}
			if (strlen($destination) > 32) {
				$errors .= "<br />Destination too long: Cannot exceed 32 characters.";
			}
			if (date_invalid($pickupDate)) {
				$errors .= "<br />Pickup date invalid: Must be in YYYY-MM-DD format, and be a date that actually exists.";
			}
			if (!preg_match("/([01]\d|2[0-3])\:[0-5]\d/",$pickupTime)) {
				$errors .= "<br />Pickup time invalid: Must be in HH:MM format.";
			}
			if ($unitExists) {
				$unitInt = intval($unit);
				if (floatval($unit) != $unitInt) {
					$errors .= "<br />Unit number invalid: Only integers are allowed.";
				}
				if ($unitInt <= 0) {
					$errors .= "<br />Unit number invalid: Only positive values are allowed.";
				}
			}
			if ($errors === "") {
				if (time_in_past($pickupDate,$pickupTime)) {
					$errors .= "<br />Pickup date and time invalid: Cannot be in the past.";
				}
			}
			
			if ($errors === "") {
				$sql = create_connection();
				if ($sql->connect_errno) {
					$msg = format_booking_error("<br />MySQL error ".$sql->connect_errno.": ".$sql->connect_error);
				} else {
					global $customersName;
					$customersTable = new MySQLTable($sql,$customersName);
					if (!$customersTable->exists("email",$email)) {
						$msg = format_booking_error("<br />It appears that you are not registered or logged in.");
					} else {
						global $bookingsName;
						global $bookingsStructure;
						$table = new MySQLTable($sql,$bookingsName);
						$noEmail = "<br />No user is registered under the given email address.";
						$table->create($bookingsStructure);
						$entries = array(
							$table->encodeString($passengerName),
							$table->encodeString($email),
							$table->encodeString($phone),
							$table->encodeString($streetName),
							$table->encodeString($suburb),
							$table->encodeString($destination),
							$table->encodeString($pickupDate),
							$table->encodeString($pickupTime),
							$streetNum
						);
						$columns = array(
							"name",
							"email",
							"phone",
							"streetName",
							"suburb",
							"destination",
							"pickupDate",
							"pickupTime",
							"streetNum"
						);
						if ($unitExists) {
							$entries[] = $unit;
							$columns[] = "unit";
						}
						$table->addRow($entries,$columns);
						$msg = array(
							"date" => $pickupDate,
							"time" => $pickupTime,
							"ref" => $sql->insert_id
						);
					}
					$sql->close();
				}
			} else {
				$msg = format_booking_error($errors);
			}
		}
		return $msg;
	}
?>