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

	$mobile = $_GET['mobile'];

	

	

	

        $sql = "SELECT referal_code FROM referal_users WHERE mobile ='$mobile' ORDER BY id DESC LIMIT 1 ";

		$result =mysqli_query($conn,$sql);

		$cntrow = mysqli_num_rows($result);

		if($cntrow > 0){

		   $res = mysqli_fetch_array($result);

		   $outp=$res['referal_code'];

		}

		else{

		    $outp=0;

		}

		

	$outp= json_encode($outp);

		echo($outp);



	$conn->close();	

	

?>