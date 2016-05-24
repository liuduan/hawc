<?php
header("Access-Control-Allow-Origin: *");







// receiving post input.
$from_input = file_get_contents('php://input'); 
// echo '$from_input'. $from_input;

// echo "{\"BMDS_Service_Number\": \"Hello World\"}";
// exit;


// Get storage number
$BMDS_Service_Number = "Amazon". date('YFd_'). Serial_number(); 

echo "{\"BMDS_Service_Number\": \"". $BMDS_Service_Number."\"}";

$data_file_name = '.\\Temp_BMDS_files\\'. $BMDS_Service_Number. "_data.txt";

file_put_contents($data_file_name, $from_input);

function Serial_number() {
	// This function takes a serial number from ./Serial.txt; 
	// and it will add a number each time of access,
	// after 1000, it will reset to 1.
    $Serial_number = file_get_contents('.\\Serial.txt');
	if ($Serial_number > 3000){$Serial_number = $Serial_number - 1000;}
	file_put_contents('.\\Serial.txt', ($Serial_number + 1));
	return $Serial_number;
}
 
?>