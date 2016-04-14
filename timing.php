<?php
	function seconds_until($date,$time) {
		$timezone = new DateTimeZone(date_default_timezone_get());
		$now = microtime(true);
		$time = new DateTime($date."T".$time.":00",$timezone);
		$time = $time->getTimestamp();
		return $time - $now;
	}
?>