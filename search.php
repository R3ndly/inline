<?php
require_once 'config.php';
require_once 'autoload.php';

use Inline\Database;
use InvalidArgumentException;

$config = require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch_search'])) {
    header('Content-Type: application/json');
    
    try {
        $db = new Database(
            $config['database']['host'],
            $config['database']['user'],
            $config['database']['pass'],
            $config['database']['name'],
            $config['database']['charset']
        );

        $searchQuery = $_POST['search'] ?? '';
        
        if (strlen($searchQuery) < 3) {
            throw new InvalidArgumentException('Введите минимум 3 символа для поиска');
        }
        
        $results = $db->searchComments($searchQuery);
        
        echo json_encode([
            'success' => true,
            'query' => htmlspecialchars($searchQuery),
            'results' => array_map(function($item) use ($searchQuery) {
                return [
                    'post_id' => $item['post_id'],
                    'post_title' => htmlspecialchars($item['post_title']),
                    'comment_id' => $item['comment_id'],
                    'comment_text' => htmlspecialchars($item['comment_text']),
                    'highlighted' => preg_replace(
                        "/(" . preg_quote($searchQuery, '/') . ")/i", 
                        '<span class="highlight">$1</span>', 
                        htmlspecialchars($item['comment_text'])
                    )
                ];
            }, $results)
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поиск комментариев</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .search-box { margin: 20px 0; }
        .result-item { margin: 15px 0; padding: 10px; border: 1px solid #eee; }
        .post-title { color: #2c3e50; font-weight: bold; }
        .highlight { background-color: yellow; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Поиск по комментариям</h1>
    
    <div class="search-box">
        <form id="searchForm">
            <input type="text" name="search" id="searchInput"
                   placeholder="Введите минимум 3 символа" 
                   minlength="3" required
                   autocomplete="off">
            <button type="submit">Найти</button>
        </form>
        <div id="loading">Идёт поиск...</div>
    </div>

    <div id="errorContainer" class="error"></div>
    <div id="resultsContainer"></div>

<script>
        document.getElementById('searchForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const searchInput = document.getElementById('searchInput');
            const errorContainer = document.getElementById('errorContainer');
            const resultsContainer = document.getElementById('resultsContainer');
            const loadingIndicator = document.getElementById('loading');
            
            const searchQuery = searchInput.value.trim();
            
            if (searchQuery.length < 3) {
                errorContainer.textContent = 'Введите минимум 3 символа для поиска';
                resultsContainer.innerHTML = '';
                return;
            }
            
            loadingIndicator.style.display = 'block';
            errorContainer.textContent = '';
            resultsContainer.innerHTML = '';
            
            try {
                const response = await fetch('search.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `search=${encodeURIComponent(searchQuery)}&fetch_search=1`
                });
                
                const data = await response.json();
                
                loadingIndicator.style.display = 'none';
                
                if (data.success) {
                    if (data.results.length > 0) {
                        let html = `<h2>Результаты поиска: "${data.query}"</h2>`;
                        html += `<p>Найдено комментариев: ${data.results.length}</p>`;
                        
                        data.results.forEach(result => {
                            html += `
                                <div class="result-item">
                                    <div class="meta-info">
                                        <span class="post-id">ID поста: ${result.post_id}</span>
                                        <span class="comment-id">ID комментария: ${result.comment_id}</span>
                                    </div>
                                    <div class="post-title">${result.post_title}</div>
                                    <div class="comment-text">${result.highlighted}</div>
                                </div>
                            `;
                        });
                        
                        resultsContainer.innerHTML = html;
                    } else {
                        resultsContainer.innerHTML = `
                            <div class="result-item">
                                <p>Ничего не найдено по запросу "${data.query}"</p>
                            </div>
                        `;
                    }
                } else {
                    errorContainer.textContent = data.error;
                }
            } catch (error) {
                loadingIndicator.style.display = 'none';
                errorContainer.textContent = 'Ошибка при выполнении запроса';
                console.error('Fetch error:', error);
            }
        });
    </script>
</body>
</html>
