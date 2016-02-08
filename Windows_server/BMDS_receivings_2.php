

<?php
echo 'Hello: <br>' ;
echo $_POST["Data-to-BMDS_post"] . '!<br>';



# Save file to disk
$file = 'test.(d)';
file_put_contents($file, $_POST["Data-to-BMDS_post"]);

$output = shell_exec('.\\BMDS2601\\logist .\\test.(d)');
# $output = shell_exec('dir .\\BMDS2601');


echo '$output: '. $output;
?>


<form class="form-horizontal" action="http://52.24.231.219/test.out">   
   <button class="btn btn-large btn-primary">See Results</button>
</form>

</body>
</html>