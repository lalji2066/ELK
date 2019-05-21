<?php
echo $date = "01/06/2018";

$date = DateTime::createFromFormat("m/d/Y" , $date);

echo "\n".$date->format('Y-m-d');

?>
