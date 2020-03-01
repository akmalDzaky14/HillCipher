<?php
if (isset($_GET['Row']) && isset($_GET['Col'])) {
    $row = &$_GET['Row'];
    $col = &$_GET['Col'];
    if ($row != $col) {
        header('Location: index.php?error=isSquare');
    }
    if (isset($_GET['error'])) {
        $error = &$_GET['error'];
        if ($error == 'isSingular') {
            echo '<a style="color: red;">Matrix anda tidak bisa di inverse</a> <br>';
        } elseif ($error == 'plaintext') {
            echo '<a style="color: red;">Plaintext mengandung angka</a> <br>';
        }
    }
?>
    <a> nilai kunci (Harus bisa di inverse):</a>
    <form action="get.php" method="get">
        <table>
            <?php
            for ($i = 0; $i < $row; $i++) {
                echo '<tr>';
                for ($j = 0; $j < $col; $j++) {
                    echo '<td><input type="number" name="matrix[' . $i . '][]"> </td>';
                }
                echo '</tr>';
            } ?>
            <img src="segitigaPascal.png"><br>
            <a>Gunakan segitiga pascal sebagai acuan</a>
        </table>
        Masukkan plaintext : <input type="text" name="plaintext"><br>
        <input type="submit" value="Submit">
        <button><a href="index.php" style="text-decoration: none;">Kembali</a></button>
    </form>
<?php } else {
    if (isset($_GET['error'])) {
        $error = &$_GET['error'];
        if ($error == 'isSquare') {
            echo '<a style="color: red;">Matrix anda bukan m×m</a> <br>';
        }
    } ?>
    Buat matrix kunci <br>
    masukkan ukuran kunci (harus m×m):
    <form action="index.php" method="get">
        <input type="number" name="Row">
        <input type="number" name="Col">
        <input type="submit" value="Submit">
    </form>
<?php } ?>