


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
	//$lead_id = $request->lead_id;
	
	//$country_id = $_GET['country_id'];
		$user_id = $_GET['user_id'];
        $sql="select * from purchased_leads where user_id ='$user_id'";
		
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
            while ($row = mysqli_fetch_array($result)) {
            $leadid[] = $row['lead_id'];
			//echo json_encode($leadid);
			
	}
		foreach($leadid as $key){
			$getleads = "select l.*, p.product, u.uom from leads l, products p, uoms u where l.id = '$key' and l.hsn_id = p.hsn_id and l.uom_id = u.id";
		
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
				}
			}
			
		}
		}
		else{
			$outp=0;
		}
		
	$outp = json_encode($outp);
	echo $outp;
	$conn->close();

?>
