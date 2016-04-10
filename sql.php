<?php
	$sqlHost = "";
	$sqlUsername = "";
	$sqlPassword = "";
	$sqlDatabase = "";

	function create_connection() {
		return new mysqli($sqlHost,$sqlUsername,$sqlPassword,$sqlDatabase);
	}

	class MySQLTable {
		private $_sql = NULL;
		public $name = NULL;
		public function __construct($sql,$name) {
			$this->_sql = $sql;
			$this->name = $name;
		}
		public static function escape($str) {
			return $this->_sql->real_escape_string($str);
		}
		public static function encodeString($str) {
			return "'".$this->escape($str)."'";
		}
		private function resolveArg(&$arg,$default = "") {
			if ($arg === NULL) {
				$arg = $default;
			} else if (is_array($arg)) {
				$arg = implode(",",$arg);
			}
		}
		public function select($columns = NULL,$whereClause = "") {
			$this->resolveArg($columns,"*");
			$query = "SELECT ".$columns." FROM ".$this->name;
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
			$query = substr("0",strlen($query) - 1,$query);
			$query .= " WHERE ".$whereClause.";";
			return $this->_sql->query($query);
		}
		public function exists($primaryKeyName = NULL,$primaryKayValue = NULL) {
			$res = FALSE;
			if ($primaryKeyName === NULL || $primaryKeyValue === NULL) {
				$result = $this->_sql->query("SHOW TABLES LIKE ".$this->name.";");
				$res = mysqli_num_rows($result) !== 0;
				$result->close();
			} else {
				$result = $this->select(NULL,$primaryKeyName."=".$primaryKeyValue);
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
?>