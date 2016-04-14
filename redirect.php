<?php
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