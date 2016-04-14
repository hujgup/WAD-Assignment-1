<?php
	require_once(__DIR__.'/sql.php');
	require_once(__DIR__.'/sql_table_Customers.php');
	require_once(__DIR__.'/sql_table_Bookings.php');
	require_once(__DIR__.'/timing.php');

	/*
		Puts some given data inside a td element.

		@param $value - mixed - The value inside the td.
		@return void
	*/
	function write_td($value)
	{
		return '<td>'.$value.'</td>';
	}
	/*
		Attempts to return the entries in the Bookings table that have pickup times less than two hours from now.

		@return void
	*/
	function print_entries()
	{
		$res = '';
		$sql = create_connection();
		if ($sql->connect_errno) {
			$res .= '<p>MySQL error '.$sql->connect_errno.': '.$sql->connect_error.'</p>';
		} else {
			$bookings = new MySQLTable($sql,Bookings::NAME);
			$rows = $bookings->select(array(
				'referenceNumber',
				't1.name as pName',
				't2.name as cName',
				't1.phone',
				'unit',
				'streetNum',
				'streetName',
				'suburb',
				'destination',
				'pickupDate',
				'pickupTime',
				'status'
			), NULL, ' AS t1 INNER JOIN '.Customers::NAME.' AS t2 ON t1.email = t2.email');
			$rows = get_rows($rows);
			$sql->close();
			$rowsToPrint = array();
			foreach ($rows as $row) {
				// Number of seconds in an hour = 2*60^2 = 7200
				if (seconds_until($row['pickupDate'], $row['pickupTime']) <= 7200 && $row['status'] === 'unassigned') {
					$rowsToPrint[] = $row;
				}
			}
			if (count($rowsToPrint) !== 0) {
				$res .= "<table class='unassigned'>";
				$res .= '<tr>';
				$res .= '<th>Reference #</th>';
				$res .= '<th>Customer Name</th>';
				$res .= '<th>Passenger Name</th>';
				$res .= '<th>Passenger Contact Phone</th>';
				$res .= '<th>Pick-Up Address</th>';
				$res .= '<th>Destination Suburb</th>';
				$res .= '<th>Pick-Up Time</th>';
				$res .= '</tr>';
				foreach ($rowsToPrint as $row) {
					$res .= '<tr>';
					$res .= write_td($row['referenceNumber']);
					$res .= write_td($row['cName']);
					$res .= write_td($row['pName']);
					$res .= write_td($row['phone']);
					$res .= write_td(($row['unit'] !== NULL && $row['unit'] !== '' ? $row['unit'].'/' : '').$row['streetNum'].' '.$row['streetName'].', '.$row['suburb']);
					$res .= write_td($row['destination']);
					$res .= write_td($row['pickupDate'].' '.$row['pickupTime']);
					$res .= '</tr>';
				}
				$res .= '</table>';
			} else {
				$res .= '<p>No bookings due in the next two hours exist.</p>';
			}
		}
		return $res;
	}
	/*
		Attempts to assign a taxi to a given booking.

		@param $ref - int - The reference number of the booking to assign.
		@return void
	*/
	function print_assign($ref)
	{
		$res = '';
		$sql = create_connection();
		if ($sql->connect_errno) {
			$res .= '<p>MySQL error '.$sql->connect_errno.': '.$sql->connect_error.'</p>';
		} else {
			$bookings = new MySQLTable($sql, Bookings::NAME);
			if ($bookings->exists('referenceNumber', $ref)) {
				$where = 'referenceNumber='.$ref;
				$result = $bookings->select('status', $where." AND status='unassigned'");
				if ($result->num_rows === 1) {
					$bookings->update_row(array('status' => "'assigned'"), $where);
					$res .= '<p>The booking request '.$ref.' has been properly assigned.</p>';
				} else {
					$res .= '<p>That booking request has already been assigned.</p>';
				}
				$result->close();
			} else {
				$res .= '<p>An entry with that reference number does not exist.</p>';
			}
			$sql->close();
		}
		return $res;
	}
?>