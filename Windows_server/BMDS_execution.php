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
echo "Hello, world! ,!.pqrs";


echo $HTTP_RAW_POST_DATA;
$json = file_get_contents("php://input"); 
$obj = json_decode($json, false, $depth = 512);
echo '<p>$obj: '.$obj;
var_dump($obj);


$json = file_get_contents("php://input"); 
$obj = json_decode($HTTP_RAW_POST_DATA, false, $depth = 512);
var_dump($obj);

echo '<p>'."operation 1";


$json = file_get_contents('php://input'); 
$obj = json_decode($json, false, $depth = 512);
var_dump($obj);

echo '<p>'."operation 5";

echo file_get_contents('php://input');
$data = json_decode(file_get_contents('php://input'), false, $depth = 512);
print_r($data);
echo '<p>'."operation";
var_dump($data);
echo $data["operation"];


echo '<br>'. "1st: ";

echo $_POST["submission"];

echo '<br>'. "1st 2: ";
print_r($_POST["submission"]);





echo '<br>'. "2nd: ";
var_dump(json_decode($_POST, false, $depth = 512));
json_decode($_POST, false, $depth = 512);


// A valid json string
$json[] = '{"Organization": "PHP Documentation Team"}';

// An invalid json string which will cause an syntax 
// error, in this case we used ' instead of " for quotation
$json[] = "{'Organization': 'PHP Documentation Team'}";

$json = $_POST;

echo '<br>Decoding: ';
foreach ($obj as $string) {
    echo '<br>Decoding: ' . $string;
    json_decode($string);
	
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }
}
echo '<br> --- decoding.';

$json_obj = json_decode($_POST, false, $depth = 512);
echo '<br> --- decoding.';
print $json_obj->{'models'};

echo '<br> --- decoding.';
echo '<br> var_dump(get_object_vars($_POST)): ';
var_dump(get_object_vars($_POST));














echo '<br>'. "3nd: ";
print_r(json_decode($_POST,false, $depth = 512));

echo '<br>';
foreach ($_POST as $key => $value) {
  echo '<p>'.$key.'</p>';
  foreach($value as $k => $v)
  {
  echo '<p>'.$k.'</p>';
  echo '<p>'.$v.'</p>';
  echo '<hr />';
  }
}


$json = '{"a":1,"b":2,"c":3,"d":4,"e":{"e1":"aa", "e2":"bb"}}';

var_dump(json_decode($json,false, $depth = 512));
echo '<br> True: ';
var_dump(json_decode($json, true));













 
?>