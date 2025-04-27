<?php

require_once __DIR__ . '/../Service/ScraperService.php';

class ScraperController {
    public function handleRequest($uri) {
     
        if ($uri === '/' || preg_match('/^\/\d+$/', $uri)) {
            $num = 1;
            if ($uri !== '/' && preg_match('/^\/(\d+)$/', $uri, $matches)) {
                $num = (int) $matches[1];
            }

            $service = new ScraperService();
            $data = $service->getData($num);
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
        }
    }
}
?>