<?php
	function expecting(&$var,$entries) {
		$res = TRUE;
		foreach ($entries as $entry) {
			if (!isset($var[$entry])) {
				$res = FALSE;
				break;
			}
		}
		return $res;
	}
?>