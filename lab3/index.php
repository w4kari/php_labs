<?php

$day = date('w');
if ($day == 1 || $day == 3 || $day == 5)
  echo "8:00-12:00";
else 
    echo "Нерабочий день"; 

echo "<br>";

if ($day == 2 || $day == 4 || $day == 6)
  echo "12:00-16:00";
else 
    echo "Нерабочий день";

echo "<br>";

    echo "Цикл for:";

echo "<br>";

$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
   $a += 10;
   $b += 5;

   echo "$i: a = $a";

echo "<br>";

   echo "$i: b = $b";

echo "<br>";
}

   echo "End of the loop: a = $a, b = $b";

echo "<br>";

   echo "Цикл через do-while:";

echo "<br>";

$a = 0;
$b = 0;
$i = 0;

   do {
      $i++;
      $a += 10;
      $b += 5;


   echo "$i: a = $a";

echo "<br>";

   echo "$i: b = $b";

echo "<br>";

} while ($i <= 5);

   echo "End of the loop: a = $a, b = $b";

echo "<br>";

   echo "Цикл через while:";

echo "<br>";

$i = 0;
$a = 0;
$b = 0;

 while ($i <= 5) {

      $i++;
      $a += 10;
      $b += 5;

   echo "$i: a = $a";

   echo "<br>";

   echo "$i: b = $b";

   echo "<br>";

}

echo "End of the loop: a = $a, b = $b";