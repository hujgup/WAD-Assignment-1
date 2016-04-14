<!--
	COS30030 Web Application Development - Assignment 1
	Author: Jake Tunaley (Student I.D. 100593584)

	Purpose: Provides the interface for the Admin page.
-->
<!DOCTYPE html>
<html lang="en-AU">
<head>
	<meta charset="utf-8" />
	<title>Cabs Online - Admin</title>
	<link rel="stylesheet" href="admin.css" />
	<?php
		require_once('admin_logic.php');

		$respond = isset($_POST['formID']);
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
				<h2>1. <label for="list2h">Click the button below to search for all unassigned booking requests with a pick-up time within 2 hours.</label></h2>
				<input id="list2h" type="submit" value="List All" />
			</form>
		</section>
		<section id="responseUnassigned">
			<?php
				if ($respond && $_POST['formID'] === 'unassigned') {
					echo print_entries();
				}
			?>
		</section>
		<section>
			<form id="assign" action="admin.php" method="POST">
				<input type="hidden" name="formID" value="assigned" />
				<h2>2. <label for="ref">Input a reference number below and click &quot;update&quot; to assign a taxi to that request.</label></h2>
				<p>
					<input type="number" name="reference" placeholder="Reference Number" min="1" />
					<input id="ref" type="submit" value="Update" />
				</p>
			</form>
		</section>
		<section id="responseAssign">
			<?php
				if ($respond && $_POST['formID'] === 'assigned') {
					if (isset($_POST['reference']) && $_POST['reference'] !== "") {
						echo print_assign($_POST['reference']);
					} else {
						echo '<p>No reference number specified!</p>';
					}
				}
			?>
		</section>
	</article>
</body>
</head>
</html>
