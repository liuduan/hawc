<?php
if(isset($["Data"]))
{
	$method=$_SERVER['REQUEST_METHOD'];
	$data="";
 	if($method=="POST"){
		$data=$_POST["Data"];

		$fakeData=new FakeData();
		$fakeData->Data="Hi remote friend, you tried to POST some mock data: *"+data+"* to me.";
		$fakeData->Time=new DateTime("now");
		}
 		elseif($method=="GET")
			{
			$fakeData=new FakeData();
			$fakeData->Data="Hi remote friend, you tried to passed me data: *"+data+"* through HTTP GET.";
			$fakeData->Time=new DateTime("now");
			}
 		else
			{
			RaiseError();
			}

	header('Content-type: application/json');
	$jsonStr= json_encode($fakeData);
 	echo($jsonStr);
	}
	else
		{
		RaiseError();
		}

function RaiseError()
{
http_send_status(405);
header("Status: 405 Method Not Allowed");
}

/*Classes definition*/
class FakeData
{
public $Data;
public $Time;
}
?>

<?php
 
$term = strip_tags(substr($_POST['search_term'],0, 100));
//$term = mysql_escape_string($term);

//do anything on the terms, such as fetch records from db, manipulate it as well.
//....

$string = 'Hello '. $term;

echo $string;
 
phpinfo();
 
?>