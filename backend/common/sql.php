<?php
	$sqlHost = "mysql.ict.swin.edu.au";
	$sqlUsername = "s100593584";
	$sqlPassword = "180696";
	$sqlDatabase = "s100593584_db";

	function create_connection() {
		global $sqlHost;
		global $sqlUsername;
		global $sqlPassword;
		global $sqlDatabase;
		return new mysqli($sqlHost,$sqlUsername,$sqlPassword,$sqlDatabase);
	}

	class MySQLTable {
		private $_sql = NULL;
		public $name = NULL;
		public function __construct($sql,$name) {
			$this->_sql = $sql;
			$this->name = $name;
		}
		public static function unescape($str) {
			return stripslashes($str);
		}
		public function escape($str) {
			return $this->_sql->real_escape_string($str);
		}
		public function encodeString($str,$preEscaped = FALSE) {
			return "'".($preEscaped ? $str : $this->escape($str))."'";
		}
		private function resolveArg(&$arg,$default = "") {
			if ($arg === NULL) {
				$arg = $default;
			} else if (is_array($arg)) {
				$arg = implode(",",$arg);
			}
		}
		public function select($columns = NULL,$whereClause = "",$additional = "") {
			$this->resolveArg($columns,"*");
			$query = "SELECT ".$columns." FROM ".$this->name;
			if ($additional) {
				$query .= " ".$additional;
			}
			if ($whereClause) {
				$query .= " WHERE ".$whereClause;
			}
			$query .= ";";
			return $this->_sql->query($query);
		}
		public function addRow($values,$columns = NULL) {
			$this->resolveArg($columns);
			$query = "INSERT INTO ".$this->name;
			if ($columns !== "") {
				$query .= " (".$columns.")";
			}
			$query .= " VALUES (".implode(",",$values).");";
			return $this->_sql->query($query);
		}
		public function updateRow($keyValueSet,$whereClause) {
			$query = "UPDATE ".$this->name." SET ";
			foreach ($keyValueSet as $key => $value) {
				$query .= $key."=".$value.",";
			}
			$query = substr($query,0,strlen($query) - 1);
			$query .= " WHERE ".$whereClause.";";
			return $this->_sql->query($query);
		}
		public function exists($primaryKeyName = NULL,$primaryKeyValue = NULL,$encodeString = TRUE) {
			$res = FALSE;
			if ($primaryKeyName === NULL || $primaryKeyValue === NULL) {
				$result = $this->_sql->query("SHOW TABLES LIKE '".$this->name."';");
				$res = mysqli_num_rows($result) !== 0;
				$result->close();
			} else {
				$result = $this->select(NULL,$primaryKeyName."=".($encodeString ? $this->encodeString($primaryKeyValue) : $primaryKeyValue));
				$res = mysqli_num_rows($result) !== 0;
				$result->close();
			}
			return $res;
		}
		public function drop() {
			return $this->_sql->query("DROP TABLE ".$this->name.";");
		}
		public function describe() {
			return $this->_sql->query("DESCRIBE ".$this->name.";");
		}
		public function create($entries) {
			return $this->exists() ? FALSE : $this->_sql->query("CREATE TABLE ".$this->name." (".implode(",",$entries).");");
		}
	}

	function get_rows($result) {
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