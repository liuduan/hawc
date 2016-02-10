<?php
/* This file is used to test the .post() function as a remote server file
It was located at http://ec2-52-24-231-219.us-west-2.compute.amazonaws.com/ajaxll.php.
The local PC testing file was ./Local_PC_testing_files/Susan_helped-2.html 
It works very well.
This line in remote PHP file is required to allow post access in Amazon Web Services:
header("Access-Control-Allow-Origin: *");

Susan Liu (LIU Zhonghong) helped to figure out.
-- Duan Liu, 2016-Feb */

header("Access-Control-Allow-Origin: *");

$term = strip_tags(substr($_POST['search_term'],0, 100));
//$term = mysql_escape_string($term);

//do anything on the terms, such as fetch records from db, manipulate it as well.
//....

$string = 'Hello '. $term;

echo $string;
 
?>