<?php
// header("Access-Control-Allow-Origin: *");


/*
$multiplier = 4;
$size = 1024 * $multiplier;
for($i = 1; $i <= $size; $i++) {
	echo ".";
}
flush();
for($i = 1; $i <= $size; $i++) {
	echo ".";
}
echo '<br>Hello World<br>';
sleep(5);
for($i = 1; $i <= $size; $i++) {
	echo ".";
}
echo '<br>Hello World, spleeped 5 seconds. <br>';
*/







// Turn off output buffering
ini_set('output_buffering', 'off');
// Turn off PHP output compression
ini_set('zlib.output_compression', false);
         
//Flush (send) the output buffer and turn off output buffering
//ob_end_flush();
while (@ob_end_flush());
         
// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);
 
//prevent apache from buffering it for deflate/gzip
header("Content-type: text/plain");
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
 
for($i = 0; $i < 1000; $i++)
{
echo ' ';
}
         
// ob_flush();
flush();
 
/// Now start the program output
 
echo '<br>Program Output<br>';
sleep(5);
echo "Program Output after sleep 5";
 
// ob_flush();
flush();

 
?>