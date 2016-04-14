<?php
	$sqlHost = 'mysql.ict.swin.edu.au';
	$sqlUsername = 's100593584';
	$sqlPassword = '180696';
	$sqlDatabase = 's100593584_db';

	/*
		Creates a new MySQLI connection.

		@return mysqli - A new mysqli object representing a connection.
	*/
	function create_connection()
	{
		global $sqlHost;
		global $sqlUsername;
		global $sqlPassword;
		global $sqlDatabase;
		return new mysqli($sqlHost, $sqlUsername, $sqlPassword, $sqlDatabase);
	}

	/*
		Provides a wrapper around some MySQLI functions.
	*/
	class MySQLTable
	{
		private $_sql = NULL;
		public $name = NULL;
		/*
			Creates a new table interface.

			@param $sql - mysqli - A mysqli object.
			@param $name - string - The name of this table.
		*/
		public function __construct($sql, $name)
		{
			$this->_sql = $sql;
			$this->name = $name;
		}
		/*
			Undos any escaping done to a string by the MySQLTable::escape method.

			@param $str - string - The string to unescape.
			@return string - The unescaped string.
		*/
		public static function unescape($str)
		{
			return stripslashes($str);
		}
		/*
			Resolves an argument to a value understandable by MySQL.

			@param $arg - mixed - The argument to resolve.
			@param $default - string - The value to set $arg to if it is NULL.
			@return void
		*/
		private function resolve_arg(&$arg, $default = '')
		{
			if ($arg === NULL) {
				$arg = $default;
			} else if (is_array($arg)) {
				$arg = implode(',', $arg);
			}
		}
		/*
			Makes a string MySQL safe.

			@param $str - string - The string to escape.
			@return string - The escaped string.
		*/
		public function escape($str)
		{
			// Function pulled from PHP coding standard document, with modifications to use mysqli
			$magicQuotesActive = get_magic_quotes_gpc();
			$newEnoughPHP = function_exists("mysqli_real_escape_string");
			if ($newEnoughPHP) {
				if ($magicQuotesActive) {
					$str = stripslashes($str);
				}
				$str = $this->_sql->real_escape_string($str);
				$str = str_replace('`', '\`',$str);
			} else {
				if (!$magic_quotes_active) {
					$str = addslashes($str);
				}
			}
			return $str;
		}
		/*
			Encodes a string into MySQL string syntax.

			@param $str - string - The string to encode.
			@param $preEscaped - bool - Whether $str was previously sent through MySQLTable::escape.
			@return string - The encoded string.
		*/
		public function encode_string($str, $preEscaped = FALSE)
		{
			return "'".($preEscaped ? $str : $this->escape($str))."'";
		}
		/*
			Performs a SELECT query on the current table.

			@param $columns - mixed - The columns to retrieve from the table.
			@param $whereClause - string - The WHERE clause to use.
			@param $additional - string - Any additional statements to append to the SELECT statement.
			@return mysqli_result - The result of the query.
		*/
		public function select($columns = NULL, $whereClause = '', $additional = '')
		{
			$this->resolve_arg($columns, '*');
			$query = 'SELECT '.$columns.' FROM '.$this->name;
			if ($additional) {
				$query .= ' '.$additional;
			}
			if ($whereClause) {
				$query .= ' WHERE '.$whereClause;
			}
			$query .= ';';
			return $this->_sql->query($query);
		}
		/*
			Performs an INSERT INTO query on the current table.

			@param $values - string - The values to insert.
			@param $columns - mixed - The columns to set the values of.
			@return bool - Whether the query succeeded or not.
		*/
		public function add_row($values, $columns = NULL)
		{
			$this->resolve_arg($columns);
			$query = 'INSERT INTO '.$this->name;
			if ($columns !== '') {
				$query .= ' ('.$columns.')';
			}
			$query .= ' VALUES ('.implode(',', $values).');';
			return $this->_sql->query($query);
		}
		/*
			Performs an UPDATE query on the current table.

			@param $keyValueSet - array - The key/value pairs to set.
			@param $whereClause - string - The WHERE clause to use.
			@return bool - Whether the query succeeded or not.
		*/
		public function update_row($keyValueSet, $whereClause)
		{
			$query = 'UPDATE '.$this->name.' SET ';
			foreach ($keyValueSet as $key => $value) {
				$query .= $key.'='.$value.',';
			}
			$query = substr($query, 0, strlen($query) - 1);
			$query .= ' WHERE '.$whereClause.';';
			return $this->_sql->query($query);
		}
		/*
			If $key and $value are null, determines if this table has been created. Otherwise, determines if this table contains a certain key-value pair.

			@param $key - string - The key to check.
			@param $value - string - The value that should be associated with the key.
			@param $encodeString - bool - Whether $value should be encoded or not.
			@return bool - Depending on arguments, whether or not the table exists, or whether or not the key-value pair exists.
		*/
		public function exists($key = NULL, $value = NULL, $encodeString = TRUE)
		{
			$res = FALSE;
			if ($key === NULL || $value === NULL) {
				$result = $this->_sql->query("SHOW TABLES LIKE '".$this->name."';");
				$res = mysqli_num_rows($result) !== 0;
				$result->close();
			} else {
				$result = $this->select(NULL, $key.'='.($encodeString ? $this->encode_string($value) : $value));
				$res = mysqli_num_rows($result) !== 0;
				$result->close();
			}
			return $res;
		}
		/*
			Drops the current table.

			@return bool - Whether the table was dropped or not.
		*/
		public function drop()
		{
			return $this->_sql->query('DROP TABLE '.$this->name.';');
		}
		/*
			Creates the current table if it does not yet exist.

			@return bool - TRUE if the table was created, FALSE if it was not or if the table was previously created.
		*/
		public function create($entries)
		{
			return $this->exists() ? FALSE : $this->_sql->query('CREATE TABLE '.$this->name.' ('.implode(',', $entries).');');
		}
	}

	/*
		Translates a givem mysqli_result into an array of arrays.

		@param $result - mysqli_result - The result to translate.
		@return array - An array of associative arrays representing the given result.
	*/
	function get_rows($result)
	{
		$res = array();
		while ($row = $result->fetch_assoc()) {
			$out = array();
			foreach ($row as $key => $value) {
				$out[$key] = MySQLTable::unescape($value);
			}
			$res[] = $out;
		}
		return $res;
	}
?>