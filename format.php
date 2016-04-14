<?php
	/*
		Formats generic error messages into a readable form.

		@param $task - string - The task being performed when the error occured.
		@param $errors - string - The error string to format.
		@return string - The formatted error message.
	*/
	function format_error_message($task, $errors)
	{
		$errors = '<p>Could not complete '.$task.':<br />'.$errors.'</p>';
		$errors = preg_replace('/\<br \/\>\<br \/\>/', '</p><p>', $errors);
		return $errors;
	}
?>