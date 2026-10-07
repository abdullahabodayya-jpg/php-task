<?php
//01st task 

echo "<br /><br />"." <STRONG>01 String and String Functions</STRONG>" . "<br /> <br />";

$first = 0;
$second = 1;

for ($i=0; $i < 9; $i++) { 
    
    echo $first;

    if ($i < 8) {
        echo ",";
    }

    $next = $first + $second;

    $first = $second;
    $second = $next;

}

//02nd task 

echo "<br /><br />"." <STRONG>02 String and String Functions</STRONG>" . "<br /> <br />";

$num = 1;

for ($i=1; $i <= 5; $i++) { 
    
    for ($j=1; $j <= $i; $j++) { 
        echo $num . " ";

        $num++;
    }

    echo "<br />";

}


//03rd task 

echo "<br /><br />"." <STRONG>03 String and String Functions</STRONG>" . "<br /> <br />";

$characters = ["A","B","C","D","E"];

for ($i=0; $i < 5; $i++) { 
    
    for($j = 0; $j <= $i; $j++){
        echo $characters[$j];
    }

    echo "<br />";

}
for ($i = 4; $i >= 1; $i--) { 
    
    for($j = 0; $j < $i; $j++){
        echo $characters[$j];
    }

    echo "<br />";

}
?>