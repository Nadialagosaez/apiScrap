<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../src/Controller/ScraperController.php';

$requestUri = $_SERVER['REQUEST_URI'];

$controller = new ScraperController();
$controller->handleRequest($requestUri);


?>