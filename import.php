<?php
require_once 'config.php';
require_once 'autoload.php';

use Inline\Database;
use Inline\ApiClient;
use Inline\DataImporter;

$config = require 'config.php';
    
$db = new Database(
    $config['database']['host'],
    $config['database']['user'],
    $config['database']['pass'],
    $config['database']['name'],
    $config['database']['charset']
);
    
$apiClient = new ApiClient(
    $config['api']['posts_url'],
    $config['api']['comments_url']
);
    
$importer = new DataImporter($db, $apiClient);
    
$result = $importer->import();

echo "Загружено {$result['articles']} записей и {$result['comments']} комментариев\n";
