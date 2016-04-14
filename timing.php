<?php
	/*
		Returns the number of seconds until a given date and time.

		@param $date - A date of format YYYY-MM-DD.
		@param $time - A time of format HH:MM.
		@return float - The number of seconds until the given time.
	*/
	function seconds_until($date, $time)
	{
		$timezone = new DateTimeZone(date_default_timezone_get());
		$now = microtime(true);
		$time = new DateTime($date.'T'.$time.':00', $timezone);
		$time = $time->getTimestamp();
		return $time - $now;
	}
?>