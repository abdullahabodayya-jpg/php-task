 <?php
// String and String Functions
echo "<big><strong>String and String Functions</strong></big>" . "<br /><br /><br />";

//01st task 

echo "<br /><br />"." <STRONG>01 String and String Functions</STRONG>" . "<br /> <br />";

$string = "Twinkle, twinkle, little star.";

$array = explode(",", $string);

var_dump($array);

//02nd task 

echo "<br /><br />"." <STRONG>02 String and String Functions</STRONG>" . "<br /> <br />";

$char = "a";

if ($char == "z") {
    echo "a";
} else {
    echo chr(ord($char) + 1);
}

//03rd task 

echo "<br /><br />"." <STRONG>03 String and String Functions</STRONG>" . "<br /> <br />";

$string_insert = "The brown fox";

$new_string_insert = str_replace("The ", "The quick ", $string_insert);

echo $new_string_insert;

//04th task 

echo "<br /><br />"." <STRONG>04 String and String Functions</STRONG>" . "<br /> <br />";

echo strtok($new_string_insert, " ");
//05th task 

echo "<br /><br />"." <STRONG>05 String and String Functions</STRONG>" . "<br /> <br />";

$num = "0000657022.24";

echo ltrim($num, "0");
//06th task 

echo "<br /><br />"." <STRONG>06 String and String Functions</STRONG>" . "<br /> <br />";
$str_dash = "The quick brown fox jumps over the lazy dog---";

echo rtrim($str_dash, "-");

//07th task 

echo "<br /><br />"." <STRONG>07 String and String Functions</STRONG>" . "<br /> <br />";

$str_5 = "The quick brown fox jumps over the lazy dog";

$words = explode(" ", $str_5);

$firstFive = array_slice($words, 0, 5);

echo implode(" ", $firstFive);

//08th task 

echo "<br /><br />"." <STRONG>08 String and String Functions</STRONG>" . "<br /> <br />";

$str_comma = "2,543.12";

echo str_replace(",", " ", $str_comma);
 ?>