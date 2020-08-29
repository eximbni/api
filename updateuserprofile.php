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
	    $name =$request->fullname;
	    $business_name =$request->business_name;
	    $todaysdate = date("Y-m-d H:i:00");
	    
	   // $mobile = $_GET['mobile'];
	   // $name = $_GET['name'];
	   // $business_name = $_GET['business_name'];
	    
			$chekmobile = "select * from users where mobile='$mobile'";
        	$reschk = mysqli_query($conn,$chekmobile);
        	$rows = mysqli_fetch_array($reschk);
        	$user_id = $rows['id'];
        	$country = $rows['country_id'];
        	
	       if($reschk){
	           
    	        $updpwd = "update users set name='$name', business_name='$business_name' where mobile ='$mobile'";
    	        $updres = mysqli_query($conn,$updpwd);
    	        
    	        if($updres){
    	            $message = "Your Profile Name : ".$name." and Business Name :".$business_name."  updated Successfully.";
    	            $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Profile Update Successfully', '$message', '1', '$todaysdate')";
            		$result_inbox = mysqli_query($conn,$ins_inbox);
            		
            		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Profile Update Successfully', '$message', '1', '$todaysdate')";
            		
            		$result_unotify = mysqli_query($conn,$ins_unotify);
    	            
                    $outp = 1;
    	       }
    	        
	         }
	        else{
	        
	             $outp = 0;
	        }
	    
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 