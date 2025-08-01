<?php
namespace Inline;

class DataImporter {
    private $db;
    private $apiClient;
    
    public function __construct(Database $db, ApiClient $apiClient) {
        $this->db = $db;
        $this->apiClient = $apiClient;
    }
    
    public function import() {
        $pdo = $this->db->getConnection();
        
        $posts = $this->apiClient->fetchPosts();
        $stmtArticle = $pdo->prepare("INSERT INTO articles (id, userId, title, body) VALUES (?, ?, ?, ?)");
        
        $articlesCount = 0;
        foreach ($posts as $post) {
            $stmtArticle->execute([$post['id'], $post['userId'], $post['title'], $post['body']]);
            $articlesCount++;
        }
        
        $comments = $this->apiClient->fetchComments();
        $stmtComment = $pdo->prepare("INSERT INTO comments (id, postId, name, email, body) VALUES (?, ?, ?, ?, ?)");
        
        $commentsCount = 0;
        foreach ($comments as $comment) {
            $stmtComment->execute([$comment['id'], $comment['postId'], $comment['name'], $comment['email'], $comment['body']]);
            $commentsCount++;
        }
        
        return [
            'articles' => $articlesCount,
            'comments' => $commentsCount
        ];
    }
}
