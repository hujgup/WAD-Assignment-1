<?php
	session_start();
	require_once("backend/login_logic.php");
	require_once("backend/common/expecting.php");

	$message = NULL;
	if (expecting($_POST,array("email","pwd"))) {
		$message = login($_POST["email"],$_POST["pwd"]);
	}

	if ($message === TRUE) {
		require_once("backend/common/redirect.php");
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
			<form id="login" target="login.php" action="POST">
				<p>
					<input type="email" id="email" name="email" placeholer="Email Address" required="required" minlength="1" maxlength="32" /><br />
					<input type="password" id="pwd" name="pwd" placeholder="Password" required="required" minlength="1" maxlength="32" /><br />
				</p>
				<p>
					<input type="submit" value="Login" />
				</p>
			</form>
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
