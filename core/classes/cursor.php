<?php

/**
 * Cursor Provied Some Common Querys As Function
 */

class Cursor {

	private $Database;
	
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

		if ($caller === "get") {

			$sqledConditions = "";

			$index = 0;

			foreach ($data as $key => $value) {

				$index += 1;

				$sqledConditions .= $key . " = " . ":" . $key;

				if ($index !== count($data)) $sqledConditions .= " AND ";

			}

			return $sqledConditions;

		} elseif ($caller === "put") {

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

	public function put(string $table, array $records) {

		$sqled = $this->sqlify(__FUNCTION__, $records);

		$sqledColumns = $sqled["columns"];
		$sqledValues = $sqled["values"];

		try {

			$stmt = $this->Database->prepare("INSERT INTO $table $sqledColumns VALUES $sqledValues");

			$stmt->execute($records);

		} catch (PDOException $Error) {

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

			return (!empty($target)) ? $records[0][$target] : $records ;

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

		$sqledConditions = $this->sqlify($conditions);

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

		$sqledConditions = $this->sqlify($conditions);

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

	public function custom(string $query, array $values=[], bool $isReturnable=false) {

		try {

			$stmt = $this->Database->prepare($query);

			if (empty($values)) $stmt->execute(); else $stmt->execute($values);

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