<?php
// arrays tasks 


// first task 

echo "<STRONG>01 ARRAY</STRONG>" . "<br /> <br />";

$colors = array('white', 'green', 'red');

$random_array = array_rand($colors,3);

shuffle($colors);

// foreach ($random_array as $key){
//     echo $colors[$key]."\n";
// }

foreach ($colors as $color) {
    echo $color . " " ;
}
//

echo "<br /> <br />";
// second task

echo "<STRONG>02 ARRAY</STRONG>" . "<br /> <br />";

$cities= array( "Italy"=>"Rome", "Luxembourg"=>"Luxembourg", "Belgium"=> "Brussels",
"Denmark"=>"Copenhagen", "Finland"=>"Helsinki", "France" => "Paris", "Slovakia"=>"Bratislava",
"Slovenia"=>"Ljubljana", "Germany" => "Berlin", "Greece" => "Athens", "Ireland"=>"Dublin",
"Netherlands"=>"Amsterdam", "Portugal"=>"Lisbon", "Spain"=>"Madrid" );

foreach ($cities as $city => $capital){
    echo "The capital of $city is $capital" . "<br /> <br />";
}

//third task 

echo "<STRONG>03 ARRAY</STRONG>" . "<br /> <br />";

$color = array (4 => 'white', 6 => 'green', 11=> 'red');

foreach ($color as $key => $value) {
    echo $value . "<br /> <br />";
}

//FOURTH task 

echo "<STRONG>04 ARRAY</STRONG>" . "<br /> <br />";

$add = [1,2,3,4,5];

$new_item = "$";

array_splice($add, 3, 0, $new_item);

foreach ($add as $item) {
   echo $item . " ";
}

//fifth task 

echo "<br /> <br />"." <STRONG>05 ARRAY</STRONG>" . "<br /> <br />";

$fruits = array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple");

asort($fruits);
foreach ($fruits as $key => $value) {
    echo $key . " = " . $value . "<br /> <br />";
}

//sixth task 

echo " <STRONG>06 ARRAY</STRONG>" . "<br /> <br />";

$temperature = [78, 60, 62, 68, 71, 68, 73, 85, 66, 64, 76, 63, 75, 76, 73, 68, 62, 73, 72, 65, 74, 62, 62,
65, 64, 68, 73, 75, 79, 73];

sort($temperature);


$avg = array_sum($temperature) / count($temperature);

echo "Average Temperature is:" . " " . $avg;

$lowest_temps = array_slice($temperature, 0,5); 

echo "<br /><br />"."List of five highest temperatures: " . implode(", ", $lowest_temps) . ",\n";
$highest_temps = array_slice($temperature, -5);

echo "<br /><br />"."List of five highest temperatures: " . implode(", ", $highest_temps) . ",\n";

//7th task 

echo "<br /><br />"." <STRONG>07 ARRAY</STRONG>" . "<br /> <br />";

$array1 = array("color" => "red", 2, 4);
$array2 = array("a", "b", "color" => "green", "shape" => "trapezoid", 4);

$merged_Array = array_merge($array1,$array2);

echo "<br /><br />";
print_r($merged_Array);

echo "<br /><br />";

//8th task 

echo "<br /><br />"." <STRONG>08 ARRAY</STRONG>" . "<br /> <br />";
$upper_colors = array("red","blue", "white","yellow");

foreach ($upper_colors as $color) {
    $color = strtoupper($color);
    echo $color . "<br />";
} 

// functions 

echo "<br /><br /><br />" . "<big><strong>FUNCTIONS</strong></big>" . "<br /><br /><br />";

//01st task 

echo "<br /><br />"." <STRONG>01 FUNCTIONS</STRONG>" . "<br /> <br />";

$numbers = [2,3,4,5,6,7,8,9,10,11,15,27,20];

foreach ($numbers as $num) {

    $isPrime = true;

    if ($num <= 1){
        $isPrime = false;
    }

    for ($i=2; $i < $num; $i++) { 
        if ($num % $i == 0) {
            $isPrime = false;
            break;
        }
        
        }
        
    if ($isPrime) {
       echo $num . "is prime number <br />";
    } else {
        echo $num . "is not prime number <br />";
    }
}


//02st task 

echo "<br /><br />"." <STRONG>02 FUNCTIONS</STRONG>" . "<br /> <br />";

function reverseString($string){
    return strrev($string);
}

echo reverseString("remove");


//03rd task 

echo "<br /><br />"." <STRONG>03 FUNCTIONS</STRONG>" . "<br /> <br />";


function swapping($first, $second){
  [$first, $second] = [$second, $first];

  echo "First" . " " . $first . "<br />";
  echo "Second" . " " . $second . "<br />";

}

$first = 12;
$second = 10;

swapping($first, $second);

//04th task 

echo "<br /><br />"." <STRONG>04 FUNCTIONS</STRONG>" . "<br /> <br />";

function Armstrong ($num) {
    $digits = str_split((string) $num);
    $ARM = 0;

    foreach ($digits as $digit) {

        $ARM += pow($digit, count($digits));

    }

    return $ARM === $num;
}

$number = 163;

if (Armstrong($number)) {
    echo "$number is an Armstrong number.\n";

} else {
    echo "$number is NOT an Armstrong number.\n";
}

//05th task 

echo "<br /><br />"." <STRONG>05 FUNCTIONS</STRONG>" . "<br /> <br />";

function Palindrome($string){
    return $string === strrev($string);
}

$string = "karak";

if (Palindrome($string)){
    echo $string . " is Palindrome Stirng";
} else {
    echo $string . " is <strong>NOT</strong> Palindrome Stirng!";
}
//06th task 

echo "<br /><br />"." <STRONG>06 FUNCTIONS</STRONG>" . "<br /> <br />";
function removeDuplicates(array $numbers)
{
    $uniq_numbers = array_unique($numbers);

    print_r($uniq_numbers);
}

$uniqe_numbers = [1,1,1,2,5,66,7,8,9,9,8,6];
removeDuplicates($uniqe_numbers);


// Logical Statements and Operators 

echo "<br /><br /><br />" . "<big><strong>Logical Statements and Operators</strong></big>" . "<br /><br /><br />";

//01st task 

echo "<br /><br />"." <STRONG>01 Logical</STRONG>" . "<br /> <br />";
function checkSum($first, $second) {
    if ($first + $second == 30) {
        return $first + $second;
    } else {
        return false;
    }
}

$result = checkSum(10, 10);

var_dump($result);

//02nd task 

echo "<br /><br />"." <STRONG>02 Logical</STRONG>" . "<br /> <br />";
$number_3 = 20;

if ($number_3 % 3 == 0) {
    echo $number_3 . "true";
}
else {
    echo $number_3 . "false";
}

//03rd task 

echo "<br /><br />"." <STRONG>03 Logical</STRONG>" . "<br /> <br />";

$number_4 = 50;

if ($number_4 >= 20 && $number_4 <= 50) {
     echo $number_4 . "true";
}
else {
    echo $number_4 . "false";
}

//04th task 

echo "<br /><br />"." <STRONG>04 Logical</STRONG>" . "<br /> <br />";

$first = 1;
$second = 5;
$third = 9;

if ($first > $second && $first > $third) {
    echo $first;
} elseif ($second > $first && $second > $third) {
    echo $second;
} else {
    echo $third;
}

//05th task 

echo "<br /><br />"." <STRONG>05 Logical</STRONG>" . "<br /> <br />";

$units = 400;
$bill = 0;

if ($units <= 50 ) {
    $bill = $units * 2.50;
}elseif ($units <= 100 && $units >= 50) {
    $bill = $units * 5.00;
}elseif ($units <= 200 && $units >= 100 ) {
    $bill = $units * 6.20;
}else {
    $bill = $units * 7.50;
}


echo " your bill for this mounth {$bill} JD ";
//06th task 

echo "<br /><br />"." <STRONG>06 Logical</STRONG>" . "<br /> <br />";

$first = 10;
$second = 5;
$operator = "+";

if ($operator == "+") {

    echo "Sum : "  . $first + $second;

} elseif ($operator == "-") {

    echo "Subitract : " . $first - $second;

} elseif ($operator == "*") {

    echo "multiplay : " . $first * $second;

} elseif ($operator == "/") {

    if ($second != 0) {
        echo "Devide : " . $first / $second;
    } else {
        echo "Cannot divide by zero";
    }

} else {

    echo "Invalid operator";

}


//07th task 

echo "<br /><br />"." <STRONG>07 Logical</STRONG>" . "<br /> <br />";

$age = 15;

if ($age >= 18) {
    echo "is eligible to vote";
} else {
    echo "is not eligible to vote";
}

//08th task 

echo "<br /><br />"." <STRONG>08 Logical</STRONG>" . "<br /> <br />";
$number = -60;

if ($number > 0) {

    echo "Positive";

} elseif ($number < 0) {

    echo "Negative";

} else {

    echo "Zero";

}


//09th task 

echo "<br /><br />"." <STRONG>09 Logical</STRONG>" . "<br /> <br />";

$scores = [60, 86, 95, 63, 55, 74, 79, 62, 50];

$average = array_sum($scores) / count($scores);

echo "Average: " . $average . "<br>";

if ($average >= 90) {

    echo "Grade: A";

} elseif ($average >= 80) {

    echo "Grade: B";

} elseif ($average >= 70) {

    echo "Grade: C";

} elseif ($average >= 60) {

    echo "Grade: D";

} elseif ($average >= 50) {

    echo "Grade: E";

} else {

    echo "Grade: F";

}


// Loops

echo "<br /><br /><br />" . "<big><strong>Loops</strong></big>" . "<br /><br /><br />";

//01th task 

echo "<br /><br />"." <STRONG>01 Loops</STRONG>" . "<br /> <br />";

for ($i=1; $i <= 10 ; $i++) { 
    
echo $i;

if ($i<10){
    echo "-";
}
}

//02th task 

echo "<br /><br />"." <STRONG>02 Loops</STRONG>" . "<br /> <br />";


$sum_loop = 0;

for ($i=0; $i < 30; $i++) { 

    $sum_loop += $i;
}

echo "sum = " . $sum_loop;

//03th task 

echo "<br /><br />"." <STRONG>03 Loops</STRONG>" . "<br /> <br />";

$char = ["A","B","C","D"];

for ($i=1; $i <= 5; $i++) { 
    
    for ($j=1; $j <= 5; $j++) { 
        if ($i == 5) {
            echo "D";
        }
        elseif ($j <= 6 - $i) {
            echo "A";
        }
        else {
            echo $char[$i - 2];
        }

    }
    echo "<br /> ";
}
//04th task 

echo "<br /><br />"." <STRONG>04 Loops</STRONG>" . "<br /> <br />";

for ($i=1; $i <= 5; $i++) { 
    
    for ($j=1; $j <= 5; $j++) { 
     if ($i == 5) {
            echo 5;
        }
        elseif ($j <= 6 - $i) {
            echo 1;
        }
        else {
            echo $i;
        }
    }
    echo "<br /> ";
}

//05th task 

echo "<br /><br />"." <STRONG>05 Loops</STRONG>" . "<br /> <br />";

for ($i=1; $i <= 5; $i++) { 
    
    for ($j=1; $j <= 5; $j++) { 
        
        if ($i == $j) {
            echo $i . " ";
        } else {
            echo "0 ";
        }
    }

    echo "<br />";
}


//06th task 

echo "<br /><br />"." <STRONG>06 Loops</STRONG>" . "<br /> <br />";


$num_f = 5;
$factorial = 1;

for ($i=1; $i < $num_f; $i++) { 

    $factorial *= $num_f;
}

echo "facorial :" . $factorial;

// String and String Functions

echo "<br /><br /><br />" . "<big><strong>String and String Functions</strong></big>" . "<br /><br /><br />";

// 01 task
echo "<br /><br />"." <STRONG>01 String and String Functions</STRONG>" . "<br /> <br />";

$string = "hello world";

echo strtoupper($string) . "<br>";
echo strtolower($string) . "<br>";
echo ucfirst($string) . "<br>";
echo ucwords($string);

// 02 task
echo "<br /><br />"." <STRONG>02 String and String Functions</STRONG>" . "<br /> <br />";


$date_str = "085119";
 
for ($i=0; $i < strlen($date_str); $i += 2) { 
    echo substr($date_str,$i ,2);

    if ($i < strlen($date_str) - 2) {
        echo ":";
    }
}

// 03 task
echo "<br /><br />"." <STRONG>03 String and String Functions</STRONG>" . "<br /> <br />";

$find = "I am a full stack developer at orange coding academy";

if (str_contains($find , "orange")){
    echo "Orange";
}

// 04 task
echo "<br /><br />"." <STRONG>04 String and String Functions</STRONG>" . "<br /> <br />";

$url = "www.orange.com/index.php";

echo basename($url);

// 05 task
echo "<br /><br />"." <STRONG>05 String and String Functions</STRONG>" . "<br /> <br />";


$email = "info@orange.com";

$part = explode("@", $email);

echo "username is : " . $part[0];

// 06 task
echo "<br /><br />"." <STRONG>06 String and String Functions</STRONG>" . "<br /> <br />";


echo substr($email, -3);

// 07 task
echo "<br /><br />"." <STRONG>07 String and String Functions</STRONG>" . "<br /> <br />";

$characters = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
$passwordlen = 8;
$password = "";

for ($i=0; $i < $passwordlen; $i++) { 
    $index  = random_int(0, strlen($characters) - 1);
    $password .= $characters[$index];
}

echo "password  : " . $password;

// 08 task
echo "<br /><br />"." <STRONG>08 String and String Functions</STRONG>" . "<br /> <br />";

$replace = "our new trainee is so genius";
echo str_replace("our", "the" , $replace)

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<table cellpadding="3px" cellspacing="0px" border="1">

<?php  
//07th task 

echo "<br /><br />"." <STRONG>07 Loops</STRONG>" . "<br /> <br />";
for ($i=1; $i <=5 ; $i++) { 
    echo "<tr>";

    for ($j=1; $j <= 6 ; $j++) { 
        
        $result = $i*$j;

        echo "<td>" . $i . "*" . $j . "=" . $result . "</td>";

    }

    echo "</tr>";
}
?>
</body>
</html>
