<?php
header("Access-Control-Allow-Origin: *");

$term = strip_tags(substr($_POST['search_term'],0, 100));
//$term = mysql_escape_string($term);

//do anything on the terms, such as fetch records from db, manipulate it as well.
//....

$string = 'Hello '. $term;

echo $string;
 
?>