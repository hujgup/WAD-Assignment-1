<?php
	function redirect($href) {
		header("HTTP/1.1 303 See Other");
		header("Location: ".$href);
		die();
	}
?>