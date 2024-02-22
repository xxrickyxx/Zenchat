

<?php

$startTime = microtime(true);

function customSort(&$numbers, &$maxGap) {
    $maxGap = 0; // Inizializza il massimo gap a 0

    // Algoritmo di ordinamento personalizzato (Bubble Sort)
    $n = count($numbers);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            // Calcola la somma dei primi tre bit dei numeri in binario
            $sumA = getSumOfFirstThreeBits($numbers[$j]);
            $sumB = getSumOfFirstThreeBits($numbers[$j + 1]);         

            // Scambia gli elementi se sono fuori ordine
            if ($sumA > $sumB || ($sumA == $sumB && $numbers[$j] > $numbers[$j + 1])) {
                swap($numbers, $j, $j + 1);
            }
        }


            // Calcola il gap tra gli elementi
            $currentGap = $numbers[$j + 1] - $numbers[$j];

            // Aggiornamento del massimo gap durante l'ordinamento
            if ($currentGap > $maxGap) {
                $maxGap = $currentGap;
            }


    }
}

// Funzione ausiliaria per ottenere la somma dei primi quattro bit di un numero in binario
function getSumOfFirstThreeBits($number) {


    return ($number & 0b100) >> 3 + ($number & 0b100) >> 2 + (($number & 0b010) >> 1) + ($number & 0b001);
}

// Funzione ausiliaria per scambiare due elementi in un array
function swap(&$array, $index1, $index2) {
    $temp = $array[$index1];
    $array[$index1] = $array[$index2];
    $array[$index2] = $temp;
}

// Esempio di utilizzo
//$numbers = [37, 19, 42, 11, 33, 8, 56, 72, 5, 21];
//$numbers = [88, 47, 63, 27, 39, 52, 71, 93, 16, 58];
$numbers = [];
for ($i = 0; $i < 100000; $i++) {
    $numbers[] = rand(1, 1000000);
}

$maxGap = 0;
customSort($numbers, $maxGap);

// Stampa dei numeri ordinati e del massimo gap
echo "Numeri ordinati: " . implode(', ', $numbers) . "<br>";
echo "Massimo gap: " . ($maxGap);

$endTime = microtime(true);
$executionTime = ($endTime - $startTime) * 1000; // in millisecondi
echo "<br><br>Tempo di esecuzione: " . $executionTime . " ms<br>";


echo "Memoria utilizzata: " . memory_get_usage() . " bytes<br>";
echo "Picco massimo di memoria: " . memory_get_peak_usage() . " bytes<br>";


?>


