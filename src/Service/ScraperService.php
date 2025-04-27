<?php

class ScraperService {
    private const CACHEFILE = __DIR__ . '/../cache/cache.json';
    private const TIMECACHE = 100000;
    private const BASEURL = 'https://news.ycombinator.com/?p=';

    public function getData($num) {
        $docCache = [];
        $dataCache = [];
        $numCache = 0;
        $allResults = [];

        // Leer cache
        if (file_exists(self::CACHEFILE) && (time() - filemtime(self::CACHEFILE)) < self::TIMECACHE) {
            $docCache = json_decode(file_get_contents(self::CACHEFILE), true);
            $numCache = $docCache['numCache'];
            $dataCache = $docCache['data'];
        }

        if ($numCache < $num) {
            for ($i = $numCache + 1; $i <= $num; $i++) {
                $html = $this->fetchHtml($i);
                if ($html) {
                    $newData = $this->scrapeHtml($html);
                    $allResults = array_merge($allResults, $newData);
                } else {
                    http_response_code(500);
                    return ['error' => 'Error al consultar la página'];
                }
            }
            // Guardar nuevo cache
            file_put_contents(self::CACHEFILE, json_encode([
                'data' => array_merge($dataCache, $allResults),
                'numCache' => $num
            ]));
            $allResults = array_merge($dataCache, $allResults);
        } else {
            $allResults = array_slice($dataCache, 0, $num * 30);
        }

        return $allResults;
    }

    private function fetchHtml($page) {
        $url = self::BASEURL . $page;
        $html = @file_get_contents($url);
        return $html ?: null;
    }

    private function scrapeHtml($html) {
        $domDoc = new DOMDocument;
        libxml_use_internal_errors(true);
        $domDoc->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($domDoc);
        $classname = 'athing submission';
        $results = $xpath->query("//tr[@class='$classname']");

        $finalResult = [];

        foreach ($results as $result) {
            $titleNode = $xpath->query(".//span[contains(@class, 'titleline')]/a/text()", $result);
            $titleText = $this->validate($titleNode);

            $nextTr = $result->nextSibling;
            while ($nextTr && $nextTr->nodeName !== 'tr') {
                $nextTr = $nextTr->nextSibling;
            }

            if ($nextTr) {
                $pointsNode = $xpath->query(".//span[contains(@class, 'score')]/text()", $nextTr);
                $pointsText = preg_replace('/\D/', '', $this->validate($pointsNode));

                $sentByNode = $xpath->query(".//a[contains(@class, 'hnuser')]/text()", $nextTr);
                $sentByText = $this->validate($sentByNode);

                $ageNode = $xpath->query(".//span[contains(@class, 'age')]/a/text()", $nextTr);
                $ageText = $this->validate($ageNode);

                $commentsNode = $xpath->query(".//span[contains(@class, 'subline')]/a[contains(@href, 'item?id=')][last()]/text()", $nextTr);
                $commentsText = $this->validate($commentsNode);

                $finalResult[] = [
                    'title' => $titleText,
                    'points' => $pointsText,
                    'sent_by' => $sentByText,
                    'published' => $ageText,
                    'comments' => $commentsText,
                ];
            }
        }

        return $finalResult;
    }

    private function validate($node) {
        if (!empty($node) && !is_null($node->item(0))) {
            return trim($node->item(0)->textContent);
        }
        return '';
    }
}
