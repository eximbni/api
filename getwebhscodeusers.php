<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 
        exit(0);
    }
 	
	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

	include("config.php"); 
	$hscode = $_GET["finalhs"];
	$user_id = $_GET["user_id"];
	$hscode = substr($hscode,0,5);
	$get = "select u.*, s.user_id from users u, subscription_hscodes s where s.user_id=u.id and u.id !='$user_id' and s.hsn_code like '$hscode%' group by u.id";
	$res = mysqli_query($conn,$get);
	if($res){
		while($row=mysqli_fetch_assoc($res)){
			$outp[]=$row;
		}
	}
        $outp = json_encode($outp);
		
		echo($outp);
	
		
		$conn->close();
?> 