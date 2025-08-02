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

	public function searchComments($searchText) {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id AS post_id,
                a.title AS post_title,
                c.id AS comment_id, 
                c.body AS comment_text
            FROM comments c
            JOIN articles a ON c.postId = a.id
            WHERE c.body LIKE :search
        ");
        $stmt->execute(['search' => "%$searchText%"]);
       	return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}
