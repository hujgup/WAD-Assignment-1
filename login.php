<?php
	require_once("session.php");

	$message = NULL;
	$redirect = Session::isActive();
	if (!$redirect) {
		require_once("expecting.php");
		require_once("login_logic.php");

		if (expecting($_POST,array("email","pwd"))) {
			$message = login($_POST["email"],$_POST["pwd"]);
		}
		$redirect = $message === TRUE;
	}
	if ($redirect) {
		require_once("redirect.php");
		redirect("booking.php");
	}
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
	<meta charset="utf-8" />
	<title>Cabs Online - Login</title>
</head>
<body>
	<h1>Cabs Online - Login</h1>
	<article>
		<nav>
			<p><a href=".">Return to Homepage</a></p>
		</nav>
		<section>
			<form id="login" action="login.php" method="POST">
				<p>
					<input type="email" id="email" name="email" placeholder="Email Address" required="required" minlength="1" maxlength="32" /><br />
					<input type="password" id="pwd" name="pwd" placeholder="Password" required="required" minlength="1" maxlength="32" /><br />
				</p>
				<p>
					<input type="submit" value="Login" />
				</p>
			</form>
			<p>Don't yet have a Cabs Online accound? <a href="register.php">Register here</a>!</p>
		</section>
		<section id="response">
			<?php
				if ($message !== "") {
					echo $message;
				}
			?>
		</section>
	</article>
</body>
</head>
</html>
