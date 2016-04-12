<?php
	class Session {
		public static $checkerKey = "email";
		public static function isActive() {
			return self::hasValue(self::$checkerKey);
		}
		public static function hasValue($key) {
			return isset($_SESSION[$key]);
		}
		public static function getValue($key) {
			return $_SESSION[$key];
		}
		public static function setValue($key,$value) {
			$_SESSION[$key] = $value;
		}
		public static function clear() {
			session_unset();
		}
		public static function destroy() {
			session_destroy();
		}
	}
?>