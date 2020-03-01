<?php
if (isset($_GET['Row']) && isset($_GET['Column'])) {
    $row = &$_GET['Row'];
    $col = &$_GET['Column'];
?>
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
        </table>
        <input type="submit" value="Submit">
        <button><a href="index.php" style="text-decoration: none;">Kembali</a></button>
    </form>
<?php } else { ?>
    <form action="index.php" method="get">
        <input type="number" name="Row">
        <input type="number" name="Column">
        <input type="submit" value="Submit">
    </form>
<?php } ?>