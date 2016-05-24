<?php
header("Access-Control-Allow-Origin: *");
/*	The purpose of this file is to run BMDS in a Windows server. The .(D) files are received from the .post() function on a client web page.
	The client side PC testing file is ./Local_PC_testing_files/Susan_helped-2.html.
     This line in remote PHP file is required to allow post access in Amazon Web Services:
header("Access-Control-Allow-Origin: *");
	The json receiving is working with plain text transfer. After that, signs like %22%2C are replaced by (",), So it will fit into json_decode() in PHP. 

	The output file is in the same folder as the input file, and with the same file nanme, 
	but the extension is different. The output file extention is .OUT;

	Susan Liu (LIU Zhonghong) helped to figure out json post.
	==========================
    During the communication between the HAWC web site and a Windows server, multiple models can be run at the same time, and each requires a different .(D) file format.
	The .(D) files were sent in Json serial text. $.ajax() function of jQuery was used to submit. In contrast to $.post() function, $ajax() has more control, e.g. submitting and receiving data type.
	After the Windows server received the post, some characters in the json text serial was modified, e.g. double quotation (“) was changed to %22, and ({) was changed to %7B. A PHP function was created to change the characters back. The json text serial is then converted into a PHP array. The .(D) files were saved to a files, and each .(D) file are saved with different file names. A serial number is generated and saved on the Windows server, so the file name will look like dfile-258.(d), and the numeric portion will range from 0 – 3000. PHP program will execute a shell command to run BMDS. All the .(D) files and the resulting files (.OUT files and .002 files) are kept in ./Temp_BMDS_files/ folder, and they will be deleted when it is more than 24 hours old in current setting. The .002 files are processed to produce .alt file and later to .emf fies.
	The resulting files are all in the same folder as the .(D) file in ./Temp_BMDS_files/ folder. These files are sent back to HAWC site in json format, which is decoded in JavaScript with the function var obj = JSON.parse(data). The results of decoding is now a JavaScript array, and the .OUT file can be accessed in commands similar to obj.models[0].OUT_file_str.toString().
    The .emf file is base64 coded.
    Cross-Site Request Forgery (CSRF) affect post function. For $.ajax() post to work CSRF need to be confiured, and the setup of CSRF is in ./project/static/js/hawc.js lines 294-322. 
	Similarly, a line in remote PHP file is required to allow post access in Amazon Web Services:
header("Access-Control-Allow-Origin: *");
    For HAWC site, the changes are embedded in the ./project/templates/bmd/bmd_session_form.html, and /Local_PC_testing_files/Susan_helped_POST.html is for POST testing. The Windows server file is /Windows_server/BMDS_execution.php.  


	-- Duan Liu, 2016-Feb 
*/

$BMDS_Service_Number = file_get_contents('php://input'); 
// echo '$BMDS_Service_Number: '. $BMDS_Service_Number. '<br>';

$storage_file = $BMDS_Service_Number. "_rsults.txt";
$storage_file = '.\\Temp_BMDS_files\\'.$storage_file;
$storage_file_size = filesize($storage_file);

if ($storage_file_size > 2000) {
	$results = file_get_contents($storage_file);
	echo $results;

}else {
    echo "Not ready. ";
  	echo $storage_file . ': ' . $storage_file_size . ' bytes';
}


exit;




flush();
echo 'BMDS started. <br>bsn = '. $_GET["bsn"]. '<br>';
for ($x = 0; $x <= 800000; $x++) {
    echo "                                                    ";
} 
echo '<br>Please do not close this window.';

// file operation.
$dir = ".\\Temp_BMDS_files";
$dh  = opendir($dir);
while (false !== ($filename = readdir($dh))) {
    $files[] = $filename;			// read files into an array.
}

sort($files);
// echo '<pre>';
// print_r($files);
// echo '</pre>';

// echo 'time(): '. time();
// echo '<br>';

// delete the files after 24 hours.
for ($i = 0; $i < count($files); ++$i) {
	if ($files[$i] != "." AND  $files[$i] != ".."){
		if ((time() - filemtime('.\\Temp_BMDS_files\\'.$files[$i])) > 86400){		// more than 24 hours old
			unlink('.\\Temp_BMDS_files\\'.$files[$i]);
			}
		}    
}


//
$input_data = '.\\Temp_BMDS_files\\'. $_GET["bsn"]. "_data.txt";
$from_input = file_get_contents($input_data); 

// receiving post input.
// $from_input = file_get_contents('php://input'); 
// echo '$from_input'. $from_input;

// Get storage number

$storage_file = $_GET["bsn"]. "_rsults.txt";

// Transfer post string into jSon string, change %22, %3A signs.
$from_input = PostString_to_jsonString($from_input);
// echo '$from_input = '. $from_input;


// Transfer json string into PHP object.
$obj_from_input = json_decode($from_input, true, $depth = 512);		// true for associated array.
// print_r($obj_from_input);

$BMDS_version = $obj_from_input['options']['bmds_version'];
// print_r($obj_from_input);
// echo '$BMDS_version: '. $BMDS_version. '<br>';
// echo '$obj_from_input[options][emf_YN]: '. $obj_from_input['options']['emf_YN']. '<br>';

$output_ar = array();
// echo '<br>$obj_from_input[id] = '. $obj_from_input['id']. '<br>';

foreach ($obj_from_input['runs'] as $k => $v) {			// cycle the content of [0] and [1]

	// echo '<br>888888888888888888888888888888888888888888888888<br>$v: '. $v;
	// print_r($v);
	$dfile_str = $v["dfile"];
	// echo '<br><br>$dfile_str: '. $dfile_str;


	$before = strstr($dfile_str,"\n", true);

	$after = strstr($dfile_str,"\n", false);
		// echo '### $before: '. $before;
		// echo '### $after: '. $after;

		//put the id number into the User Notes.
	$before = $before. "\nBMDS_Model_Run, ID: ". $obj_from_input['id']. strstr($after,"\n", true);
	$after = strstr(substr($after, 2),"\n", false); 
	
		//set the input file name
	$serial = Serial_number();
	$input_file_name = $v["model_app_name"]. '-'. $serial. '.(d)';
	$before = $before. "\n". $input_file_name. strstr($after,"\n", true);;
	$after = strstr(substr($after, 2),"\n", false); 
	
		//set the output file name
	if (substr($v["model_app_name"], 0, -3) == 'exponential'){
		$output_file_name = substr($v["model_app_name"], -2, 2). substr($input_file_name, 0, -4). '.out';
		// echo '<br>$output_file_name = '. $output_file_name;
		}
		else{$output_file_name = substr($input_file_name, 0, -4). '.out';}		// Out put file name.
	
	$before = $before. "\n". $output_file_name. strstr($after,"\n", true);;
	$after = strstr(substr($after, 2),"\n", false); 
	
		// echo '###====== $before: '. $before;
		// echo '#### $after: '. $after;
	
	$dfile_str = $before. $after;
		// echo '#### $dfile_str: '. $dfile_str;

		# Save file to disk
	$file = '.\\Temp_BMDS_files\\'.$input_file_name;
		
	file_put_contents($file, $dfile_str);


	if (substr($v["model_app_name"], 0, -3) == 'exponential'){
		
		$execu_model_name = 'exponential';
		// echo '<br>$execu_model_name = '. $execu_model_name;
		}
		else{$execu_model_name = $v["model_app_name"];}

	$execution = shell_exec('.\\'. $BMDS_version. '\\'. $execu_model_name. ' .\\Temp_BMDS_files\\'. $input_file_name);
	// echo '<br>command = '. './'. $BMDS_version. '/'. $execu_model_name. ' ./Temp_BMDS_files/'. $input_file_name;

	$output_ar[$k]['id'] = $v["id"];
	$output_ar[$k]["model_app_name"] = $v["model_app_name"];
	$output_ar[$k]['OUT_file_str'] = file_get_contents('.\\Temp_BMDS_files\\'.$output_file_name);

	if ( $obj_from_input['options']['emf_YN'] == 'True' ){
		
		$the_002_file = substr($output_file_name, 0, -4). '.002';
		
		// echo '<br>$the_002_file = '. $the_002_file. '<br>';
		$base64_emf_str = Plot_2_base64($the_002_file, $execu_model_name);
		
		$output_ar[$k]['base64_emf_str'] = $base64_emf_str;
		$output_ar[$k]['emf_link'] = 
			'52.24.231.219/Temp_BMDS_files/'. substr($the_002_file, 0, -4). '_emf.EMF';
			
					
	} else {
    	$output_ar[$k]['base64_emf_str'] = "";
		$output_ar[$k]['emf_link'] = "";
	}
	
	
	
}


// echo '***+++++++--== print_r($output_ar): <br><br>';
// print_r($output_ar);
/// echo '<br><br>';

$output_json = json_encode($output_ar);

// echo '***+++++++--======================== echo $output_json;';



// echo $output_json;

// echo '<br>$storage_file = '. $storage_file. '<br>';

$storage_str = "{\"". $storage_file. "\": ". $output_json. "}"; 
$storage_str = "{\"BMDS_Service_Number\": \"". $BMDS_Service_Number. "\", \"BMDS_Results\": ". $output_json. "}"; 
// $storage_str = $output_json; 
		# Save file to disk
$storage_file = '.\\Temp_BMDS_files\\'.$storage_file;
		
file_put_contents($storage_file, $storage_str);
// $storage_file = $serial

echo '<br> BMDS Job Finished.';
// echo '<script type = "text/javascript"> window.close();</script>';

// =====================================================================================================

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
	$from_input = str_replace("+", ' ', $from_input);
	$from_input = substr($from_input, 5, (strlen($from_input)-5));
	return $from_input;
}

function Serial_number() {
	// This function takes a serial number from ./Serial.txt; 
	// and it will add a number each time of access,
	// after 1000, it will reset to 1.
    $Serial_number = file_get_contents('.\\Serial.txt');
	if ($Serial_number > 3000){$Serial_number = $Serial_number - 1000;}
	file_put_contents('.\\Serial.txt', ($Serial_number + 1));
	return $Serial_number;
}

function Plot_2_base64($the_002_file, $execu_model_name) {
	// This function takes $a_002_file and $model_app_name; 
	// and make the plot,
	global $BMDS_version;
	switch ($execu_model_name) {
    	case "exponential":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\00expo .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "hill":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\00Hill .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "poly":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\00poly .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "power":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\00power .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "nctr":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\05nctr .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "nlogist":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\05Nlogist .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "raivr":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\05raivr .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "cancer":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10cancer .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "cancer_bg_dose":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10cancer_bg .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "DichoHill":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10DichoHill .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "Gamma_bgdose":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10gamma_bg .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "logist":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10logist .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "logist_bg_response":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10logist_bg .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "multistage":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10multista .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "multistage_bg_dose":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10multista_bg .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "probit":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10probit .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "probit_bg_response":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10probit_bg .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "weibull":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10weibull .\\Temp_BMDS_files\\'. $the_002_file);
        	break;
    	case "weibull_bg_response":
        	$execution = shell_exec('.\\'. $BMDS_version. '\\10weibull_bg .\\Temp_BMDS_files\\'. $the_002_file);
			// echo '<br>The command = '. ".\\". $BMDS_version. '\\10weibull_bg .\\Temp_BMDS_files\\'. $the_002_file. '<br>';
        	break;
		
		}
	$plot_file_name = substr($the_002_file, 0, -4). '.PLT';
	$PLT_file_str = file_get_contents('.\\Temp_BMDS_files\\'.$plot_file_name);

	$plot_emf_file_name = substr($the_002_file, 0, -4). '_emf.PLT';	
	$emf_file_name = substr($plot_emf_file_name, 0, -4). '.emf';
	$new_str_lines = "reset \nset terminal emf \nset output '.\\Temp_BMDS_files\\". $emf_file_name. "' ";
	$PLT_emf_str = str_replace("reset", $new_str_lines, $PLT_file_str);
	file_put_contents(".\\Temp_BMDS_files\\". $plot_emf_file_name, $PLT_emf_str);
	
	// echo 'makeing emf command: '. '.\\BMDS2601\\gnuplot\\wgnuplot .\\Temp_BMDS_files\\'. $plot_emf_file_name. '<br>';
	$execution = shell_exec('.\\BMDS2601\\gnuplot\\wgnuplot .\\Temp_BMDS_files\\'. $plot_emf_file_name);
	
	$emf_file_str = file_get_contents('.\\Temp_BMDS_files\\'. $emf_file_name);
	// echo '$plot = 66666666666666666666666666666666666666666'. $plot;
	$base64_emf_str = base64_encode ( $emf_file_str );
	
	// echo 'substr($base64_plt_str, 0, 300) = 444444444444444444444444444444'. substr($base64_plt_str, 0, 300);
	
	return $base64_emf_str;
}



 
?>