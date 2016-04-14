<?php
	/*
		COS30030 Web Application Development - Assignment 1
		Author: Jake Tunaley (Student I.D. 100593584)

		Purpose: Provides both the interface and the logic for the Logout page.
	*/

	require_once('session.php');
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
	<meta charset="utf-8" />
	<title>Cabs Online - Logout</title>
</head>
<body>
	<h1>Cabs Online - Logout</h1>
	<article>
		<nav>
			<p><a href=".">Return to Homepage</a></p>
		</nav>
		<section>
			<?php
				if (Session::is_active()) {
					Session::clear();
					echo '<p>You have successfully logged out.</p>';
				} else {
					echo "<p>You cannot log out because you are not logged in. Were you looking for <a href='login.php'>the login page</a>?</p>";
				}
			?>
		</section>
	</article>
</body>
</head>
</html>
