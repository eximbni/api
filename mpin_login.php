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

        $mobile =$request->mobile;
	    $mpin =$request->mpin;
	    $todaysdate = date("Y-m-d H:i:00");
		$chekuser = "select * from users where mobile='$mobile' and mpin='$mpin'";
    	$resusr = mysqli_query($conn,$chekuser);
        if(mysqli_num_rows($resusr)>0)
        {
	        $outp = 1;
	    }
	    else
	    {
	        $outp = 0;
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 