<?php
	/*
		Determines whether all of the given keys are present in the given variable.

		@param $var - array - The variable that should contain the keys.
		@param $entries - array - The set of keys to verify the existance of.
		$return bool - TRUE if all keys are present, FALSE otherwise.
	*/
	function expecting(&$var, $entries)
	{
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