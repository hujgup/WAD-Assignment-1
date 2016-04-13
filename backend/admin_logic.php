<?php
	require_once(__DIR__."/common/sql.php");
	require_once(__DIR__."/common/sql_table_Customers.php");
	require_once(__DIR__."/common/sql_table_Bookings.php");
	require_once(__DIR__."/common/timing.php");

	function write_td($value) {
		echo "<td>".$value."</td>";
	}
	function print_entries() {
		$sql = create_connection();
		if ($sql->connect_errno) {
			echo "<p>MySQL error ".$sql->connect_errno.": ".$sql->connect_error."</p>";
		} else {
			global $bookingsName;
			global $customersName;
			$bookings = new MySQLTable($sql,$bookingsName);
			$rows = get_rows($bookings->select(NULL,NULL," AS t1 INNER JOIN ".$customersName." AS t2 ON t1.email = t2.email"));
			$sql->close();
			$rowsToPrint = array();
			foreach ($rows as $row) {
				if (seconds_until($row["pickupDate"],$row["pickupTime"]) <= 7200 && $row["status"] === "unassigned") { // Number of seconds in an hour (2*60^2)
					$rowsToPrint[] = $row;
				}
			}
			if (count($rowsToPrint) !== 0) {
				echo "<table class='unassigned'>";
				echo "<tr>";
				echo "<th>Reference #</th>";
				echo "<th>Customer Name</th>";
				echo "<th>Passenger Name</th>";
				echo "<th>Passenger Contact Phone</th>";
				echo "<th>Pick-Up Address</th>";
				echo "<th>Destination Suburb</th>";
				echo "<th>Pick-Up Time</th>";
				echo "</tr>";
				foreach ($rowsToPrint as $row) {
					if (seconds_until($row["pickupDate"],$row["pickupTime"]) <= 7200) { // Number of seconds in an hour (2*60^2)
						echo "<tr>";
						write_td($row["referenceNumber"]);
						write_td($row["name"]);
						write_td($row["name"]);
						write_td($row["phone"]);
						write_td(($row["unit"] !== NULL ? $row["unit"]."/" : "").$row["streetNum"]." ".$row["streetName"].", ".$row["suburb"]);
						write_td($row["destination"]);
						write_td($row["pickupDate"]." ".$row["pickupTime"]);
						echo "</tr>";
					}
				}
				echo "</table>";
			} else {
				echo "<p>No bookings due in the next two hours exist.</p>";
			}
		}
	}
	function print_assign($ref) {
		$sql = create_connection();
		if ($sql->connect_errno) {
			echo "<p>MySQL error ".$sql->connect_errno.": ".$sql->connect_error."</p>";
		} else {
			global $bookingsName;
			$bookings = new MySQLTable($sql,$bookingsName);
			if ($bookings->exists("referenceNumber",$ref)) {
				$where = "referenceNumber=".$ref;
				$result = $bookings->select("status",$where." AND status='unassigned'");
				if ($result->num_rows === 1) {
					$bookings->updateRow(array("status" => "'assigned'"),$where);
					echo "<p>The booking request ".$ref." has been properly assigned.</p>";
				} else {
					echo "<p>That booking request has already been assigned.</p>";
				}
				$result->close();
			} else {
				echo "<p>An entry with that reference number does not exist.</p>";
			}
			$sql->close();
		}
	}
?>