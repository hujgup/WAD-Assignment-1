<?php
	/*
		Provides a wrapper around the $_SESSION superglobal.
	*/
	class Session
	{
		public static $checkerKey = 'email';
		/*
			Checks whether a session is active or not.

			@return bool - TRUE if session is active, FALSE otherwise.
		*/
		public static function is_active()
		{
			return self::has_value(self::$checkerKey);
		}
		/*
			Checks whether a given session key is set.

			@param $key - mixed - The key to check.
			@return bool - TRUE if the key has been set, FALSE otherwise.
		*/
		public static function has_value($key)
		{
			return isset($_SESSION[$key]);
		}
		/*
			Gets the value of the session variable at the given key.

			@param $key - mixed - The key to get the value of.
			@return mixed - The value of the session variable if it exists, NULL otherwise.
		*/
		public static function get_value($key)
		{
			return self::has_value($key) ? $_SESSION[$key] : NULL;
		}
		/*
			Sets the value of the session variable at the given key.

			@param $key - mixed - The key to set the value of.
			@param $value - mixed - The value to set the key to.
			@return void
		*/
		public static function set_value($key, $value)
		{
			$_SESSION[$key] = $value;
		}
		/*
			Unsets all session variables.

			@return void
		*/
		public static function clear()
		{
			session_unset();
		}
		/*
			Destroys the current session.

			@return void
		*/
		public static function destroy()
		{
			session_destroy();
		}
		/*
			Dumps the current value of the $_SESSION superglobal to the screen.
		*/
		public static function dump()
		{
			var_dump($_SESSION);
		}
	}
	session_start();
?>