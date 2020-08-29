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
	$id = $request->id;
	$userid = $request->user_id;
	$frtid = $request->frt_id;
	$commission =$request->commission;
	$status = "1";
    	$sql="update franchise_request set status='$status',commission='$commission' where id='$id'";
    	//echo $sql;
		$res = mysqli_query($conn,$sql);
			if($res){
               //$outp="Subscription Package Added Sucessfully";
                 $upd="update users set isfranchise='1', franchise_type_id = '$frtid' where id='$userid'";
                 //echo $upd;
                 $result = mysqli_query($conn,$upd);
		        $outp=1;
			} 
			else{
				$outp=$sql;
			}
	$outp = json_encode($outp);
	echo $outp;
	
	$conn->close();
?>
