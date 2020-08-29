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
	$mobile = $request->mobile;
	$start_date = $request->start_date;
	$end_date = $request->end_date;
	
		$sql = "select * from franchise_users where mobile = '$mobile'";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
			while ($row = mysqli_fetch_assoc($result)) {
			$franchise_id[] = $row['id'];
				
			}
			//print_r ($row);
		}	
			foreach($franchise_id as $key){
			$getleads = "select sum(amount) as amount from frachise_accounts where payment_for='Leads' and franchise_id='$key' and payment_date >= '$start_date' and payment_date <= '$end_date' >'0'";
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp=$rows['amount'];
					}
				}
			
		
		else{
			$outp="0";
		}
		}
		
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>