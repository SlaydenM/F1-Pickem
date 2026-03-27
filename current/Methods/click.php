<?php
require_once "Session.php";
require_once "DBControl.php";

// function getRacesFromWeb1($url) {
//     echo "Start";
    
//     // Initialize cURL
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification if needed
//     $html = curl_exec($ch);
//     curl_close($ch);
    
//     if (!$html) {
//         echo "FAIL";
//         return [];
//     }

//     // Load HTML into DOMDocument
//     $dom = new DOMDocument();
//     libxml_use_internal_errors(true); // Suppress HTML parsing errors
//     $dom->loadHTML($html);
//     libxml_clear_errors();

//     // Use XPath to find elements with class "f1-table-with-data"
//     $xpath = new DOMXPath($dom);
//     $tableNodes = $xpath->query('//table[contains(@class, "f1-table-with-data")]');
//     print_r($tableNodes);
//     $links = [];
    
//     foreach ($tableNodes as $table) {
//         $anchorTags = $table->getElementsByTagName('a');
//         foreach ($anchorTags as $a) {
//             $links[] = $a->getAttribute('href');
//         }
//     }
//     echo "end";
//     print_r($links);
//     return $links;
// }

// function getRaceResults1() {
//     $url = "https://www.formula1.com/en/results/2025/races/1254/australia/race-result";

//     // Initialize cURL
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
//     ]);
//     $html = curl_exec($ch);
//     curl_close($ch);
    
//     if (!$html) {
//         return [];
//     }
    
//     // Load HTML into DOMDocument
//     $dom = new DOMDocument();
//     libxml_use_internal_errors(true);
//     $dom->loadHTML($html);
//     libxml_clear_errors();
    
//     // Use XPath to find the table with class "f1-table-with-data"
//     $xpath = new DOMXPath($dom);
//     $tableNodes = $xpath->query("//*[contains(@class, 'f1-table-with-data')]");
    
//     $results = [];
    
//     foreach ($tableNodes as $table) {
//         $rows = $table->getElementsByTagName('tr');
        
//         foreach ($rows as $row) {
//             $cols = $row->getElementsByTagName('td');
//             $entry = [];
            
//             foreach ($cols as $col) {
//                 $entry[] = trim($col->textContent);
//             }
            
//             if (!empty($entry)) {
//                 $results[] = $entry;
//             }
//         }
//     }
    
//     return $results;
// }

// Usage Example

// $raceResults = getRaceResults1();
// print_r($raceResults);



// Usage Example
// echo "<pre>";
// $links = getFormula1Links();
// echo "</pre>";
// foreach ($links as $link) {
//     echo $link->getAttribute('href') . "\n"; // Print href attribute of each link
// }
// $drivers = getWebDrivers(2001);
// foreach($drivers as $driver) {
//     print_r($driver->getNumber() ."   ". $driver->getName())  ."<br>";
// }

echo "<pre>";
$conn = getDatabaseConnection();
$timeNow = new DateTime('now', $timezone);
$timeNow->add(new DateInterval("PT4H"));
print_r($timeNow);
echo getSessionKey($conn, $DB_NAME);
echo "</pre>";