<?php 
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
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
    $user_id = $request->user_id;
    $other_id = $request->other_id;
    $chatroom = $request->group_name;


        foreach($other_id as $val){
        
        $create = "insert into group_chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$val','$user_id','1')";
        $rescreate = mysqli_query($conn,$create);
        if($rescreate){
            
              $outp=1;
              
               
            }
            else{
                $outp = 0;
            }
        }
        //$outp = $create;
	
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
