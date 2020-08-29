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
	$chapter_id=$_GET["chapter_id"];
		$sql = "select h.*,(SELECT count(*) from users where chat_status='online') as online_users  from hsncodes_101 h where h.chapter_id=$chapter_id";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $outp[] = $row; 
}
}
			else{
				$outp=0;
			}
         
$outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);		 

		
		echo($outp);
	$conn->close();	
?>