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
	
		$email=$request->email;
		$password = $request->password;
		$deviceid = $request->deviceid;
		$password = md5($password);
	//	echo $username;
	//	echo$password;
		
		// To protect MySQL injection for Security purpose
		$chkemail="select * from users where username='$email'";
	    $chkresmail = mysqli_query($conn,$chkemail);
	    $mcount = mysqli_num_rows($chkresmail);
	   if($mcount<=0){
	        $outp="email does not exists Please Signup";
	    }
	    else{
		// $query="SELECT * FROM users where username='$email' and password='$password'"; // commented by ganesh as per kalayan sir requirement.
		$query="SELECT u.*, s.plan_type FROM `users` u , subscriptions s WHERE u.subscription_id = s.id AND u.username='$email' and u.password='$password'";
		$result = mysqli_query($conn,$query);
		$count = mysqli_num_rows($result);
		if($count>0){
		    $last_active= date('Y-m-d H:i:s');
		    $upduser = "update users set last_active='$last_active', is_online=1 where email='$email'";
		    $resupd = mysqli_query($conn,$upduser);
		    while ($row = mysqli_fetch_assoc($result)) {
                $outp[] = $row;
            }
   
		
		}
		else{
			$outp="Password not Matching with Username";
		}
	    }
        $outp = json_encode($outp);
		
		echo($outp);
	

		$conn->close();
?> 