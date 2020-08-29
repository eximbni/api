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
	include("fcmpush.php");
		$lead_id =$request->lead_id;
		$leadref_id = $request->leadref_id;
		$lead_posted_by = $request->lead_posted_by;
		$response_posted_by = $request->user_id;
		$req_type = $request->req_type;
		$response_quantity = $request->response_quantity;
		$uom = $request->uom;
		$product_id = $request->product_id;
		$description = $request->description;
		$title = "New Reponse Received ";
		$message = "You have received a new response for your Lead";
		$todaysdate = date('Y-m-d H:i:00');
		// To protect MySQL injection for Security purpose
		$insertresponse="INSERT INTO `responses` (`lead_id`, `lead_posted_by`, `response_posted_by`, `req_type`, `response_quantity`, `uom`, `description`,`leadref_id`,`product_id`) VALUES ('$lead_id', '$lead_posted_by', '$response_posted_by', '$req_type', '$response_quantity', '$uom', '$description','$leadref_id','$product_id')";
	    $chkresponse = mysqli_query($conn,$insertresponse);
	    if($chkresponse){
	        
	        $chkemail="select * from users where id='$response_posted_by'";
    	    $chkresmail = mysqli_query($conn,$chkemail);
    	    $row = mysqli_fetch_array($chkresmail);
    	    $country = $row['country'];
    	    
    	    $message = "Sell Lead requirement submitted successfully";
    	    
    	    $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$response_posted_by', '$country', 'Sell Lead requirement.', '$message', '1', '$todaysdate')";
    		$result_inbox = mysqli_query($conn,$ins_inbox);
    		
    		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$response_posted_by', '$country', 'Sell Lead requirement.', '$message', '1', '$todaysdate')";
    		$result_unotify = mysqli_query($conn,$ins_unotify);
	        
	        
			//FCM Starts
			$sql1 = "select * from users where id='$lead_posted_by'";
						$res1 = mysqli_query($conn,$sql1);
						$count = mysqli_num_rows($res1);
							if($count >0){
							while ($row = mysqli_fetch_assoc($res1)) {
								$id[]=$row['device_id']; 
							}
							
							}
							//print_r($id);
                               	fcm($fmessage,$id,$title);
			
			// FCM Ends;
	        $outp=1;
	    }
	    else{
			$outp=0;
	    }
        $outp = json_encode($outp);
		
		echo($outp);
	
		
		$conn->close();
?> 