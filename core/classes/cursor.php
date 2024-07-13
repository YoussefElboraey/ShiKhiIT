<?php

/**
 * Cursor Provied Some Common MySQL Querys As Functions
 */

class Cursor {

	private $Database;

	public int $rowCount;
	
	public function __construct(string $host, string $user, string $password, string $DBName) {

		$this->connect($host, $user, $password, $DBName);

	}

	private function connect(string $host, string $user, string $password, string $DBName) {

		try {

			$this->Database = new PDO("mysql:host=$host;dbname=$DBName" , $user , $password);

			$this->Database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		} catch (PDOException $connetionError) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "This API Unable To Connect To Database."
			]);

			exit(0);

		}
	}

	private function sqlify(string $caller, array $data) {

		if ($caller === "get" || $caller === "update" || $caller === "delete") {

			$sqledConditions = "";

			$index = 0;

			foreach ($data as $key => $value) {

				$index += 1;

				$sqledConditions .= $key . " = " . ":" . $key;

				if ($index !== count($data)) $sqledConditions .= " AND ";

			}

			return $sqledConditions;

		} elseif ($caller === "insert") {

			$columns = "(";
			$values = "(";

			$index = 0;

			foreach ($data as $key => $value) {

				$index += 1;

				$columns .= $key;
				$values .= ":" . $key;

				if ($index !== count($data)) {

					$columns .= ", ";
					$values .= ", ";

				};

			}

			$columns .= ")";
			$values .= ")";

			return ["columns" => $columns, "values" => $values];

		}

	}

	public function insert(string $table, array $records) {

		$sqled = $this->sqlify(__FUNCTION__, $records);

		$sqledColumns = $sqled["columns"];
		$sqledValues = $sqled["values"];

		try {

			$stmt = $this->Database->prepare("INSERT INTO $table $sqledColumns VALUES $sqledValues");

			$stmt->execute($records);

			return $this->Database->lastInsertId();

		} catch (PDOException $Error) {

			die($Error->getMessage());

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Insert Records."
			]);

			exit(0);

		}

	}

	public function get(string $columns, string $table, array $conditions=["true" => "1"], string $target="") {

		$sqledConditions = $this->sqlify(__FUNCTION__, $conditions);

		try {

			$stmt = $this->Database->prepare("SELECT $columns FROM $table WHERE $sqledConditions");

			$stmt->execute($conditions);

			$records = $stmt->fetchAll();

			$this->rowCount = $stmt->rowCount();

			if (!empty($target)) return (isset($records[0][$target])) ? $records[0][$target] : 0 ;

			return $records;

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Fetch Records."
			]);

			exit(0);

		}

	}

	public function update(string $table, array $records, array $conditions=["true" => "1"]) {

		$sqledConditions = $this->sqlify(__FUNCTION__, $conditions);

		$prepareddata = "";

		$index = 0;

		foreach ($records as $key => $value) {

			$index += 1;

			$prepareddata .= $key . " = :" . $key;

			if ($index !== count($records)) $prepareddata .= ", ";

		}

		try {

			$stmt = $this->Database->prepare("UPDATE $table SET $prepareddata WHERE $sqledConditions");

			$stmt->execute(array_merge($records, $conditions));

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Update Records."
			]);

			exit(0);

		}

	}

	public function delete(string $table, array $conditions=["true" => "1"]) {

		$sqledConditions = $this->sqlify(__FUNCTION__, $conditions);

		try {

			$stmt = $this->Database->prepare("DELETE FROM $table WHERE $sqledConditions");

			$stmt->execute($conditions);

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Delete Records."
			]);

			exit(0);

		}

	}

	public function customQuery(string $query, array $values=[], bool $isReturnable=False) {

		try {

			$stmt = $this->Database->prepare($query);

			if (empty($values)) $stmt->execute(); else $stmt->execute($values);

			$this->rowCount = $stmt->rowCount();

			if ($isReturnable) return $stmt->fetchAll();

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Something Went Wrong."
			]);

			exit(0);

		}

	}

	public function __destruct() {

		$this->Database = Null;

	}

}

?>