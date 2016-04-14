<?php
	require_once("session.php");

	$message = "";
	if (Session::isActive()) {
		require_once("booking_logic.php");
		$message = book();
	} else {
		require_once("redirect.php");
		redirect("login.php");
	}
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
	<meta charset="utf-8" />
	<title>Cabs Online - Bookings</title>
	<link rel="stylesheet" href="booking.css" />
</head>
<body>
	<h1>Cabs Online - Bookings</h1>
	<article>
		<nav>
			<p><a href=".">Return to Homepage</a></p>
		</nav>
		<section>
			<form id="book" action="booking.php" method="POST">
				<legend>Please fill in the fields below to book a taxi.</legend>
				<p><input type="text" name="pname" placeholder="Passenger name" required="required" /></p>
				<p><input type="text" name="phone" placeholder="Passenger contact number" required="required" /></p>
				<fieldset class="small-field">
					<legend>Pickup address</legend>
					<p><input type="number" name="unit" placeholder="Unit number" min="1" /></p>
					<p><input type="number" name="streetNum" placeholder="Street number" min="1" required="required" /></p>
					<p><input type="text" name="streetName" placeholder="Street name" required="required" /></p>
					<p><input type="text" name="suburb" placeholder="Suburb" required="required" /></p>
				</fieldset>
				<p><input type="text" name="dest" placeholder="Destination suburb" required="required" /></p>
				<p><input type="text" name="pickupDate" placeholder="Pickup date (YYYY-MM-DD)" required="required" /></p>
				<p><input type="text" name="pickupTime" placeholder="Pickup time (HH:MM, 24h time)" required="required" /></p>
				<p><input type="submit" value="Book" />
			</form>
		</section>
		<section id="response">
			<?php
				if (is_array($message)) {
					echo "<p>Thank you! Your booking reference number is ".$message["ref"].". We will pick up the passengers in front of your provided address at ".$message["time"]." on ".$message["date"].".</p>";
				} elseif ($message !== "") {
					echo $message;
				}
			?>
		</section>
	</article>
</body>
</head>
</html>
