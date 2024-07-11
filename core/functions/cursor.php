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

		} catch (PDOException $connetionError) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "This API Unable To Connect To Database."
			]);

			exit(0);

		}
	}

	public function select(array $columns, string $table, array $condition=["1" => "1"], string $conditionValue="", string $extra="") {

		$preparedCondition = $this->prepareConditionsArray($condition);

		try {

			$stmt = $this->Database->prepare("SELECT $columns FROM $table WHERE $preparedCondition $extra");

			$stmt->execute($condition);

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Fetch Users."
			]);

			exit(0);

		}

	}

	private function prepareConditionsArray(array $conditions) {

		$preparedConditionsArray = [];

		foreach ($condition as $key => $value) {
			$preparedConditionsArray[$key] = ":" . $key;
		}

		$preparedCondition = "";

		foreach ($preparedConditionsArray as $key => $value) {

			$preparedCondition .= $key . " = " . $value;

			if (array_search($value, $preparedConditionsArray) !== (count($preparedConditionsArray) - 1)) $preparedCondition .= " AND ";

		}

		return $preparedCondition;

	}

}

?>