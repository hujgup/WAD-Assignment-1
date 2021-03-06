<?php
	require_once("backend/common/session.php");
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
				if (Session::isActive()) {
					Session::clear();
					echo "<p>You have successfully logged out.</p>";
				} else {
					echo "<p>You cannot log out because you are not logged in. Were you looking for <a href='login.php'>the login page</a>?</p>";
				}
			?>
		</section>
	</article>
</body>
</head>
</html>
