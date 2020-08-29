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
	  
	    $user_id =$request->user_id;
	    $subject =$request->subject;
	    $message =$request->message;
	    
	    	$feed = "insert into feedback (user_id, subject, message) values('$user_id', '$subject', '$message')";
		    $result = mysqli_query($conn,$feed);
	       if($result){
	            
	            $outp=1;
	        }
	        
	        else
	        {
	            $outp = 2;
	        }
	    
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 