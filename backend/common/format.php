<?php
	function format_error_message($task,$errors) {
		$errors = "<p>Could not complete ".$task.":<br />".$errors."</p>";
		$errors = preg_replace("/\<br \/\>\<br \/\>/","</p><p>",$errors);
		return $errors;
	}
?>