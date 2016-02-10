<?php
header("Access-Control-Allow-Origin: *");
$response = array(
        'SECUREKEY' => md5($secret . $websitekey),
        'para1'     => date('Y-m-d H:i:s'),
        'para2'     => 'Hello world',
    );
    // Specifying charset isn't strictly necessary but may be useful.
    // Header("Content-Type: application/json;charset=UTF-8");
    $data = json_encode($response);
    $len  = strlen($data);
    // Some browser/proxy/load balancer may get better performance if the
    // length is known beforehand. This also disables Chunked-Encoding, which
    // in some scenarios may give problems.
    // Header("Content-Length: {$len}");
	// var_dump($data);
    //die($data); // Ensure no more output after this..
	exit($data);
	
	
	
	
	
# echo 'Hello' ;
# echo '$_POST['. '!<br>';

# print_r($_POST);
# var_dump(json_decode($_POST));


/*
# Save file to disk
$file = 'test.(d)';
file_put_contents($file, $_POST["Data-to-BMDS_post"]);

$output = shell_exec('.\\BMDS2601\\logist .\\test.(d)');
# $output = shell_exec('dir .\\BMDS2601');


echo '$output: '. $output;
*/
?>


