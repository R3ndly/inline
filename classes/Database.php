<?php
namespace Inline;

use PDO;

class Database {
	private $pdo;

	public function __construct($host, $user, $password, $dbName, $charset) {
		$this->pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=$charset", $user, $password);
	}

	public function getConnection() {
		return $this->pdo;
	}
}
