<?php
/* This file is used to test the .post() function as a remote server file
It was located at http://ec2-52-24-231-219.us-west-2.compute.amazonaws.com/ajaxll.php.
The local PC testing file was ./Local_PC_testing_files/Susan_helped-2.html 
It works very well.
     This line in remote PHP file is required to allow post access in Amazon Web Services:
header("Access-Control-Allow-Origin: *");
	The json receiving is working with plain text transfer. After that, signs like %22%2C are replaced by ",
So it will fit into json_decode() in PHP. 

	The output file is in the same folder as the input file, and with the same file nanme, 
	but the extension is different. The output file extention is .OUT;

	Susan Liu (LIU Zhonghong) helped to figure out.
	-- Duan Liu, 2016-Feb 
*/

header("Access-Control-Allow-Origin: *");
// echo "Hello, here is from server:..";
$dir = ".\\Temp_BMDS_files";
$dh  = opendir($dir);
while (false !== ($filename = readdir($dh))) {
    $files[] = $filename;
}

sort($files);
// echo '<pre>';
// print_r($files);
// echo '</pre>';

// echo 'time(): '. time();
// echo '<br>';

for ($i = 0; $i < count($files); ++$i) {
	if ($files[$i] != "." AND  $files[$i] != ".."){
		if ((time() - filemtime('.\\Temp_BMDS_files\\'.$files[$i])) > 86400){		// more than 24 hours old
			unlink('.\\Temp_BMDS_files\\'.$files[$i]);
			}
		// echo "***". date ("F d Y H:i:s.", filemtime('.\\Temp_BMDS_files\\'.$files[$i]));
		}    
}



// echo '<br>=================================*';
$from_input = file_get_contents('php://input'); 

// echo '<br>working: echo $from_input: ';
// echo $from_input;

$from_input = PostString_to_jsonString($from_input);

// echo '<br>###################### working: echo $from_input, should be ready for json_decode(): ';
// echo $from_input;

$obj_from_input = json_decode($from_input, true, $depth = 512);		// true for associated array.
// echo '============================================================print_r($obj_from_input): ';

// print_r($obj_from_input);
// echo '=======--==---***';

$output_ar = array();


foreach ($obj_from_input['models'] as $k => $v) {			// $k has value 0, 1, ....
    // echo $k; // print_r($v);
	$dfile_str = $obj_from_input['models'][$k]['dfile'];
	// echo '### $dfile_str: '. $dfile_str;
	$before = strstr($dfile_str,"\n", true);

	$after = strstr($dfile_str,"\n", false);
	// echo '### $before: '. $before;
	// echo '### $after: '. $after;

	//put the id number into the User Notes.
	$before = $before. "\nBMDS_Model_Run, ID: ". $obj_from_input['models'][$k]['id']. strstr($after,"\n", true);;
	$after = strstr(substr($after, 2),"\n", false); 
	
	//set the input file name
	$serial = Serial_number();
	$input_file_name = "dfile-". $serial. '.(d)';
	$before = $before. "\n". $input_file_name. strstr($after,"\n", true);;
	$after = strstr(substr($after, 2),"\n", false); 
	
	//set the output file name
	$output_file_name = substr($input_file_name, 0, -4). '.out';
	$before = $before. "\n". $output_file_name. strstr($after,"\n", true);;
	$after = strstr(substr($after, 2),"\n", false); 
	
	// echo '###====== $before: '. $before;
	// echo '#### $after: '. $after;
	
	$dfile_str = $before. $after;
	// echo '#### $dfile_str: '. $dfile_str;

	# Save file to disk
	$file = $input_file_name;
	file_put_contents(('.\\Temp_BMDS_files\\'.$file), $dfile_str);
	
	$execution = shell_exec('.\\BMDS2601\\logist .\\Temp_BMDS_files\\'. $input_file_name);
	# The output file is in the same folder as the input file, and with the same file nanme, 
	# but the extension is different. The output file extention is .OUT;
	
	$output_ar['models'][$k]['id'] = $obj_from_input['models'][$k]['id'];
	$output_ar['models'][$k]['output_text'] = file_get_contents('.\\Temp_BMDS_files\\'.$output_file_name);
	$output_ar['models'][$k]['image'] = "";
	
}

// echo '***+++++++--== print_r($output_ar): ';
// print_r($output_ar);

$output_json = json_encode($output_ar);

// echo '***+++++++--======================== echo $output_json;';
echo $output_json;




function PostString_to_jsonString($from_input) {
	// This function take $from_input = file_get_contents('php://input'); 
	// Replaces the characters and maks the string recognizible by json_decode().
    $from_input = str_replace("%22", '"', $from_input);
	$from_input = str_replace("%3A", ':', $from_input);
	$from_input = str_replace("%2C", ',', $from_input);
	$from_input = str_replace("%7D", '}', $from_input);
	$from_input = str_replace("%7B", '{', $from_input);
	$from_input = str_replace("%5C", '\\', $from_input);
	$from_input = str_replace("%5D", ']', $from_input);
	$from_input = str_replace("%5B", '[', $from_input);
	$from_input = substr($from_input, 5, (strlen($from_input)-5));
	return $from_input;
}

function Serial_number() {
	// This function take a serial number from ./Serial.txt; 
	// and it will add a number each time of access,
	// after 1000, it will reset to 1.
    $Serial_number = file_get_contents('.\\Serial.txt');
	if ($Serial_number > 1000){$Serial_number = $Serial_number - 1000;}
	file_put_contents('.\\Serial.txt', ($Serial_number + 1));
	return $Serial_number;
}



 
?>