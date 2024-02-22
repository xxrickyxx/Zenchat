<?php

$startTime = microtime(true);

//$numbers = [88, 47, 63, 27, 39, 52, 71, 93, 16, 58];
$numbers = [];
for ($i = 0; $i < 100000; $i++) {
    $numbers[] = rand(1, 1000000);
}

sort($numbers);

echo "Numeri ordinati: " . implode(', ', $numbers) . "<br>";

$maxGap = 0;
for ($i = 0; $i < count($numbers) - 1; $i++) {
    $currentGap = $numbers[$i + 1] - $numbers[$i];
    if ($currentGap > $maxGap) {
        $maxGap = $currentGap;
    }
}

echo "Massimo gap: " . $maxGap . "<br><br>";

$endTime = microtime(true);
$executionTime = ($endTime - $startTime) * 1000; // in millisecondi
echo "Tempo di esecuzione: " . $executionTime . " ms<br>";

echo "Memoria utilizzata: " . memory_get_usage() . " bytes<br>";
echo "Picco massimo di memoria: " . memory_get_peak_usage() . " bytes<br>";
