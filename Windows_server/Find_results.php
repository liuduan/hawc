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

// echo "Hello, here is from server:..";






$result_str = file_get_contents('.\\Temp_BMDS_files\\Amazon2016May20_2181.txt');

echo $result_str;
// echo "Hello World.";

exit;

?>