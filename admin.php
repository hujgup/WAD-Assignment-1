<!DOCTYPE html>
<html lang="en-AU">
<head>
	<meta charset="utf-8" />
	<title>Cabs Online - Admin</title>
	<?php
		require_once("backend/common/sql.php");
		require_once("backend/common/sql_table_Customers.php");
		require_once("backend/common/sql_table_Bookings.php");

		$respond = isset($_POST["formID"]);
	?>
</head>
<body>
	<h1>Cabs Online - Admin</h1>
	<article>
		<nav>
			<p><a href=".">Return to Homepage</a></p>
		</nav>
		<section>
			<form id="unassigned" action="admin.php" method="POST">
				<input type="hidden" name="formID" value="unassigned" />
				<h2>1. <label for="list2h">Click the button below to search for all unassignedbooking requests with a pick-up time within 2 hours.</label></h2>
				<input id="list2h" type="submit" value="List All" />
			</form>
		</section>
		<section id="responseUnassigned">
			<?php
				if ($respond && $_POST["formID"] === "unassigned") {
					$sql = create_connection();
					if ($sql->connect_errno) {
						echo "<p>MySQL error ".$sql->connect_errno.": ".$sql->connect_error."</p>";
					} else {
						$bookings = new MySQLTable($sql,$bookingsName);
						var_dump(get_rows($sql,$bookings->select()));
						$sql->close();
					}
				}
			?>
		</section>
		<section>
			<form id="assign" action="admin.php" method="POST">
				<input type="hidden" name="formID" value="unassigned" />
				<h2>2. <label for="ref">Input a reference number below and click &quot;update&quot; to assign a taxi to that request.</label></h2>
				<input id="ref" type="submit" value="List All" />
			</form>
		</section>
		<section id="responseAssign">
			<?php
				if ($respond && $_POST["formID"] === "assigned") {
					$sql = create_connection();
					if ($sql->connect_errno) {
						echo "<p>MySQL error "".$sql->connect_errno.": ".$sql->connect_error."</p>";
					} else {
						$bookings = new MySQLTable($sql,$bookingsName);
						$sql->close();
					}
				}
			?>
		</section>
	</article>
</body>
</head>
</html>
