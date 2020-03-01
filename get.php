<?php

use MathPHP\LinearAlgebra\MatrixFactory;

require_once(__DIR__ . '/vendor/autoload.php');

// $korensponden = new ArrayObject(array());
// $i = 0;
// foreach (range('A', 'Z') as $char) {
//     $korensponden->append($char);
//     $i++;
// }

$matrixArr = &$_GET['matrix']; // it will be a two dimenssion array having value as  matrix have
$A = MatrixFactory::create($matrixArr);
for ($i = 0; $i < $A->getM(); $i++) {
    for ($j = 0; $j < $A->getN(); $j++) {
        echo $A[$i][$j] . " ";
    }
    echo "<br>";
}

$inv = $A->inverse();
for ($i = 0; $i < $A->getM(); $i++) {
    for ($j = 0; $j < $A->getN(); $j++) {
        echo $A[$i][$j] . " ";
    }
    echo "<br>";
}
