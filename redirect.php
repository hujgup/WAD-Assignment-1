<?php
	/*
		COS30030 Web Application Development - Assignment 1
		Author: Jake Tunaley (Student I.D. 100593584)

		Purpose: Provides a function allowing HTTP redirects.
	*/

	/*
		Redirects the browser to a new page.

		@param $href - string - The URI to redirect to.
		@return void
	*/
	function redirect($href)
	{
		header('HTTP/1.1 303 See Other');
		header('Location: '.$href);
		die();
	}
?>