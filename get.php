<?php

use MathPHP\LinearAlgebra\MatrixFactory;

require_once(__DIR__ . '/vendor/autoload.php');
$matrixArr = &$_GET['matrix']; // it will be a two dimenssion array having value as  matrix have
$A = MatrixFactory::create($matrixArr); //buat matrix pada library

function toPlaintext($res)
{
    $korensponden = array(); //untuk menyimpan alfabet
    $i = 0;
    $cipher = array();
    foreach (range('a', 'z') as $char) {
        array_push($korensponden, $char);
        $i++;
    }
    for ($i = 0; $i < count($res); $i++) {
        for ($j = 0; $j < count($res); $j++) {
            $temp = array_search($res[$i][$j], $korensponden);
            array_push($cipher, $temp);
            echo $cipher[$i][$j];
        }
    }
}
function korensponden($plainText)
{
    $lower = strtolower($plainText); //ubah ke lowercase semua
    $plainArr = str_split($lower); //ubah string ke array
    $korensponden = array(); //untuk menyimpan alfabet
    $cipherText = array(); //untuk menyimpan cipertext
    $i = 0;
    foreach (range('a', 'z') as $char) {
        array_push($korensponden, $char);
        $i++;
    }
    foreach ($plainArr as $v) {
        $temp = array_search($v, $korensponden); //mencari huruf di array alfabet
        array_push($cipherText, $temp); //memasukan index huruf ke array jika ditemukan
    }
    return $cipherText;
}

function plaintextArr($cipherText)
{
    global $A;
    $del = 404; //char tambahan yang nanti akan di hapus saat selesai dekripsi ada di index ke-26
    do { //cek apakah jumlah index pada ciphertext bisa dikali dengan key, jika tidak maka akan ditambahkan char tambahan supaya dapat di enkripsi
        array_push($cipherText, $del); //memasukan char tambahan
    } while (count($cipherText) % $A->getM() != 0);
    echo 'cipherText (404 adalah char tambahan) : ';
    for ($i = 0; $i < count($cipherText); $i++) {
        echo $cipherText[$i] . ' ';
    }
    $div = array_chunk($cipherText, $A->getM()); //membagi array menjadi m bagian tergantung key
    $N = $A->getM();
    multiply($A, $div, $res);
    echo '<br>key * cipherText : [';
    for ($i = 0; $i < $N; $i++) {
        echo '[';
        for ($j = 0; $j < $N; $j++) {
            echo ($res[$i][$j] . ' ');
        }
        echo ("]");
    }
    echo ']';
    toPlaintext($res);
}

function multiply(&$mat1, &$mat2, &$res)
{
    global $A;
    $N = $A->getM();
    for ($i = 0; $i < $N; $i++) {
        for ($j = 0; $j < $N; $j++) {
            $res[$i][$j] = 0;
            for ($k = 0; $k < $N; $k++) {
                $res[$i][$j] += $mat2[$i][$k] * $mat1[$j][$k]; //terbaik wkwkwk
                $res[$i][$j] %= 26;
            }
        }
    }
}

if ($A->isInvertible() == false) { //cek apakah matrix bisa di inverse
    header('Location: index.php?error=isSingular&Row=' . $A->getM() . '&Col=' . $A->getN());
} else {
    echo "Matrix key anda : [";
    for ($i = 0; $i < $A->getM(); $i++) {
        echo '[';
        for ($j = 0; $j < $A->getN(); $j++) {
            echo $A[$i][$j] . " ";
        }
        echo "]";
    }
    echo ']<br>';
    plaintextArr(korensponden($_GET['plaintext']));
}
