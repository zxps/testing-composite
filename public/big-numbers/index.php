<?php

function sum ($argA, $argB) {

    $base = 10;

    $argA = trim($argA);
    $argB = trim($argB);

    $lenA = strlen($argA);
    $lenB = strlen($argB);

    if ($lenA != $lenB) {
        $delta = $lenA - $lenB;
        if ($delta > 0) {
            $argB = str_repeat('0', $delta) . $argB;
            $lenB += $delta;
        } else {
            $argA = str_repeat('0', ($delta * -1)) . $argA;
            $lenA += $delta;
        }
        $lenA = max($lenA, $lenB);
    }

    $result = '';
    $isCarry = false;
    for ( $i = ($lenA - 1); $i >= 0; --$i ) {
        $a = (int) $argA[$i];
        $b = (int) $argB[$i];

        $sum = $a + $b + ($isCarry ? 1 : 0);
        $isCarry = false;

        if ($sum > ($base - 1)) {
            $isCarry = true;
            $sum = $sum - $base;
        }

        $result = $sum . $result;
    }

    if ($isCarry) {
        $result = '1' . $result;
    }

    return $result;
}


$result = '';
if (isset($_GET['a']) && isset($_GET['b'])) {
    $result = sum($_GET['a'], $_GET['b']);
}

?>
<html>
<head>
    <title>Calculate big numbers</title>
</head>
<?php include __DIR__ . '/../header.php' ?>
<div class="container">
    <h3>Calculating big numbers</h3>
    <?php if (isset($result)): ?>
    <p>Result is: <?php echo $result ?></p>
    <?php endif ?>

    <form action="">
        <input style="width: 100%;" type="text" name="a" placeholder="first number"/>
        <input style="width: 100%;" type="text" name="b" placeholder="second number"/>
        <br/>
        <input type="submit" value="Sum"/>
    </form>
</div>
</body>
</html>