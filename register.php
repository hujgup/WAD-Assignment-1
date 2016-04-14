<?php
	require_once('session.php');
	require_once('redirect.php');

	$message = NULL;
	$redirect = Session::is_active();
	if (!$redirect) {
		require_once('register_logic.php');
		$message = register();
		$redirect = $message === TRUE;
		if ($redirect) {
			require_once('login_logic.php');
			if (!login($_POST['email'], $_POST['pwd']) ) {
				$message = "<p>You are now registered in the system, but a problem occured when trying to automatically log you in. Please manually log in <a href='login.php'>here</a>.</p>";
				$redirect = FALSE;
			}
		}
	}
	if ($redirect) {
		redirect('booking.php');
	}
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
	<meta charset="utf-8" />
	<title>Cabs Online - Registration</title>
</head>
<body>
	<h1>Cabs Online - Registration</h1>
	<article>
		<nav>
			<p><a href=".">Return to Homepage</a></p>
		</nav>
		<section id="request">
			<form id="register" action="register.php" method="POST">
				<p>
					<input type="email" id="email" name="email" placeholder="Email Address" required="required" minlength="1" maxlength="32" /><br />
					<input type="password" id="pwd" name="pwd" placeholder="Password" required="required" minlength="1" maxlength="32" /><br />
					<input type="password" id="pwdConfirm" name="pwdConfirm" placeholder="Confirm Password" required="required" minlength="1" maxlength="32" /><br />
					<input type="text" id="cname" name="cname" placeholder="Full Name" required="required" minlength="1" maxlength="64" /><br />
					<input type="text" id="phone" name="phone" placeholder="Phone Number" required="required" minlength="8" />
				</p>
				<p>
					<input type="submit" value="Register" />
				</p>
			</form>
			<p>Already registered with Cabs Online? <a href="login.php">Login here</a>!</p>
		</section>
		<section id="response">
			<?php
				if ($message !== '') {
					echo $message;
				}
			?>
		</section>
	</article>
</body>
</head>
</html>
