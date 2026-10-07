<?php

// 01
$year = 2013;

if (($year % 400 == 0) || ($year % 4 == 0 && $year % 100 != 0)) {
    echo "This year is a leap year";
} else {
    echo "This year is not a leap year";
}

echo "<br><br>";


// 02
$temperature = 27;

if ($temperature < 20) {
    echo "It is winter!";
} else {
    echo "It is summertime!";
}

echo "<br><br>";


// 03
$firstInteger = 2;
$secondInteger = 2;

if ($firstInteger == $secondInteger) {
    $sum = ($firstInteger + $secondInteger) * 3;
} else {
    $sum = $firstInteger + $secondInteger;
}

echo $sum;

echo "<br><br>";


// 04
for ($i = 200; $i <= 250; $i++) {

    if ($i % 4 == 0) {
        echo $i;

        if ($i < 248) {
            echo ",";
        }
    }
}

echo "<br><br>";


// 05
$numbers = range(11, 20);

shuffle($numbers);

for ($i = 0; $i < 10; $i++) {
    echo $numbers[$i] . " ";
}

?>