<?php
$contents = file_get_contents('https://gamepress.gg/sites/default/files/aggregatedjson/raid-boss-list-PoGO.json');
$contents = utf8_encode($contents);
$results = json_decode($contents); 
print_r($results);
?>