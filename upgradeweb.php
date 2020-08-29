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
	
		$userid =$request->user_id;// '74';
		$subscription_id = $request->pack_id;//'45';
		$duration =$request->duration;// '30';
		$country_id =$request->country_id; //'99';
		$state_id = $request->state_id;//'1476';
		$credits = $request->credits;
		
		if($duration==30){
		    $credits = $credits/12;
		}
		else{
		    $credits=$credits;
		}		
		$subscription_start = date("d-m-Y");
		$txn_date = date("d-m-Y");
		$subscription_end = date('d-m-Y', strtotime("+".$duration." days"));
 		$chapter_id =$request->chapters;
 		$hscodes_id =$request->hscode;

	    $query="update users set subscription_id='$subscription_id', subscription_start='$subscription_start', subscription_end='$subscription_end' where id='$userid'";
		$result = mysqli_query($conn,$query);
		if($result){
		    //echo"success update <br>";
		
		// delete old Chapters
		$delchapter = "delete from subscription_chapter where user_id='$userid'";
		$resdel = mysqli_query($conn,$delchapter);
		
		// delete hscodes
		$delhscodes = "delete from subscription_hscodes where user_id='$userid'";
		$reshs = mysqli_query($conn,$delhscodes);
		//update chapters for user
		$chapters =$request->chapter_id;//'[{"id":"2","chapter_name":"Chapter 02"},{"id":"4","chapter_name":"Chapter 04"},{"id":"5","chapter_name":"Chapter 05"}]';
		//$chapters = json_decode($chapters);
        for($chi = 0; $chi < count($chapter_id); $chi++){
        	$val = $chapter_id[$chi];

			$sqlc = "insert into subscription_chapter (user_id,chapter_id,status) values ('$userid','$val','1')";
			$resc = mysqli_query($conn,$sqlc);
			
			if($resc){
	        	$outp =1;
	    
			}else{
		   	 	$outp=0;
			}
        }
		
			//update Hscodes for user
			$hscodes =$request->hscode;    //'[{"id":"2","chapter_name":"Chapter 02"},{"id":"4","chapter_name":"Chapter 04"},{"id":"5","chapter_name":"Chapter 05"}]';
											//$chapters = json_decode($chapters);
			foreach($hscodes as $key => $value)
			{
				$hscodes[$key] = $value->hsncode;
			}
			
			foreach($hscodes as $val){	
	
				$sqlc = "insert into subscription_hscodes (user_id,hsn_code,status) values ('$userid','$val','1')";
				$resc = mysqli_query($conn,$sqlc);
				if($resc){
					$outp =1;
				}else{
					$outp=0;
				}
			}
		
		//get subscription amount
		$get_sub_amt = "select plan_cost from subscriptions where id='$subscription_id'";
		$res_sub_amt = mysqli_query($conn, $get_sub_amt);
		$row_sub_amt = mysqli_fetch_array($res_sub_amt);
		$sub_amount = $row_sub_amt['plan_cost'];
		
		//update rfq credits
		$subcredits ="select * from subscriptions where id = '$subscription_id'";
		$res_credit = mysqli_query($conn,$subcredits);
		
		if($res_credit){
			while($cred_row= mysqli_fetch_assoc($res_credit)){
				$packcredits =$cred_row["credits"];
				$packagename = $row_sub_amt['plan_name'];
				$rfq = $cred_row['rfq'];
				if($rfq == 'NO'){
				   $rfq_credits ='0'; 
				}else{
				   $rfq_credits =$rfq;  
				}                
				
				
			}
			
			$getcredits = "select * from wallet where user_id='$userid'";
			$resc = mysqli_query($conn,$getcredits);
			if(mysqli_num_rows($resc)>0){
				while($grow= mysqli_fetch_assoc($resc)){
					$gcredits =$grow["credits"];
					$grfq_credits =$grow["rfq_credits"];
				}
				
				$ucredits = $gcredits+$packcredits;
				$urfq_credits = $grfq_credits + $rfq_credits;
				$updcredits = "update wallet set credits='$ucredits', rfq_credits ='$urfq_credits', subscription_id='$subscription_id' where user_id='$userid'";
				$resup = mysqli_query($conn,$updcredits);
			}
	   
			
			
			
		}else{
			$outp=0;
		}
		
		//Add transaction
		$txn_id = rand(00000000,99999999);
		$admin_income = "INSERT INTO `admin_income`(`user_id`, `txn_amount`, `txn_type`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','$sub_amount','amount','subscription','$txn_id','$txn_date','1')";
		$res_admin_income = mysqli_query($conn, $admin_income);	
		if($res_admin_income)
		{
			echo "Admin Income Inserted";
		}			
		else
		{
			echo "Not inserted";
			mysqli_error($conn);
		}
		
		
		$add_txn = "INSERT INTO `txn_table`(`user_id`, `txn_type`, `txn_amount`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','amount','$sub_amount','upgrade','$txn_id','$txn_date','1')";
		$res_add_txn = mysqli_query($conn, $add_txn);	
		if($res_add_txn)
		{
			echo "Transaction Inserted";
		}			
		else
		{
			echo "Transaction Not inserted";
			mysqli_error($conn);
		}
		//Add transaction

        }
        else{
            $outp=0;
        }
        //OLD Code To calculate Commission
        /* $cfrcode = "cf.".$country_id;
	    $getcf = "select * from franchise_users where frcode='$cfrcode'";
	    $resgetcf = mysqli_query($conn,$getcf);
	    if(mysqli_num_rows($resgetcf)>0){
	        while($rowcf=mysqli_fetch_assoc($resgetcf)){
	            $franchise_id=$rowcf["id"];
	            $frcommission = $rowcf["commission"];
	        }
	        $damount = $amount-($amount*45/100);
	        if($subscription_id==1){
	            $cfamount = 0;
	        }
	        elseif($subscription_id==2){
	            $cfamount = "9.16";
	        }
	        else{
	            $cfamount = "24.66";
	        }
	        //$cfamount = $damount*$frcommission/100;
	        $payment_date = date("Y-m-d");
	        $cfcom = "insert into frachise_accounts (franchise_id,amount,payment_for,payment_date,status) values ('$franchise_id','$cfamount','subscription','$payment_date','0')";
	        $rscf = mysqli_query($conn,$cfcom);
	        
	    } */
		//OLD Code To calculate commission
		
		//New code To calculate commission
		//country Franchise Commission
	   
		$getpackamount = "select * from subscriptions where id='$subscription_id'";
		$respak = mysqli_query($conn,$getpackamount);
		if($respak){
		    while($rowpack = mysqli_fetch_assoc($respak)){
		        $pamount = $rowpack["plan_cost"];
				$chapters = $rowpack["chapters"];
		    }
			$damount = $pamount/$chapters;
			}
	    $cfrcode = "cf".$country_id;
	    $getcf = "select * from franchise_users where frcode='$cfrcode'";
	    $resgetcf = mysqli_query($conn,$getcf);
	    if(mysqli_num_rows($resgetcf)>0){
	        while($rowcf=mysqli_fetch_assoc($resgetcf)){
	            $franchise_id=$rowcf["id"];
	            $frcommission = $rowcf["commission"];
	        }
	       
	        $cfamount = $pamount*$frcommission/100;
	        $payment_date = date("Y-m-d");
	        $cfcom = "insert into frachise_accounts (franchise_id,user_id, amount,payment_for,payment_date,status) values ('$franchise_id','$userid','$cfamount','subscription','$payment_date','0')";
	        $rscf = mysqli_query($conn,$cfcom);
			
			$getcf_wallet = "select * from franchise_wallet where franchise_id = '$franchise_id' ";
			$res_cfwallet = mysqli_query($conn,$getcf_wallet);
			if(mysqli_num_rows($res_cfwallet)>0){
				while($row_cf_wallet= mysqli_fetch_assoc($res_cfwallet)){
					$wallet_amount = $row_cf_wallet["wallet"];
				}
				$cf_new_amount = $wallet_amount+$cfamount;
				$upd_cf_wallet = "update franchise_wallet set wallet='$cf_new_amount' where franchise_id = '$franchise_id'";
				$res_upd_cf_wallet = mysqli_query($conn,$upd_cf_wallet);
				
							
			}
			else{
				$cf_wallet = "insert into franchise_wallet (franchise_id, wallet, status) values ('$franchise_id','$cfamount','1')";
				$res_cf_wallet = mysqli_query($conn,$cf_wallet);
			}
			
	        
	    }
	    
		// Country Chapter Franchise
		 foreach ($chapter_id as $val){
		    $ccfrcode = "ccf".$country_id.$val;
		   $getccf = "select * from franchise_users where frcode='$ccfrcode' and chapter_id='$val'";
	    $resgetccf = mysqli_query($conn,$getccf);
	    if(mysqli_num_rows($resgetccf)>0){
	        while($rowccf=mysqli_fetch_assoc($resgetccf)){
	            $ccfranchise_id=$rowccf["id"];
	            $ccfrcommission = $rowccf["commission"];
	        }
	       
	        $ccfamount = $damount*$ccfrcommission/100;
	        $payment_date = date("Y-m-d");
	        $ccfcom = "insert into frachise_accounts (franchise_id,user_id,amount,payment_for,payment_date,status) values ('$ccfranchise_id','$userid','$ccfamount','subscription','$payment_date','0')";
	        $rccf = mysqli_query($conn,$ccfcom);
	        
			//SF Wallet Update
			$getccf_wallet = "select * from franchise_wallet where franchise_id = '$ccfranchise_id'";
			$res_ccfwallet = mysqli_query($conn,$getccf_wallet);
			if(mysqli_num_rows($res_ccfwallet)>0){
				while($row_ccf_wallet= mysqli_fetch_assoc($res_ccfwallet)){
					$ccfwallet_amount = $row_ccf_wallet["wallet"];
				}
				$ccf_new_amount = $ccfwallet_amount+$ccfamount;
				$upd_ccf_wallet = "update franchise_wallet set wallet='$ccf_new_amount' where franchise_id = '$ccfranchise_id'";
				$res_upd_ccf_wallet = mysqli_query($conn,$upd_ccf_wallet);
				
			}
			else{
				$ccf_wallet = "insert into franchise_wallet (franchise_id, wallet, status) values ('$ccfranchise_id','$ccfamount','1')";
				$res_ccf_wallet = mysqli_query($conn,$ccf_wallet);
			}
			
			
	    }
	   }
		
       // State Chapter Franchise
       
       foreach ($chapter_id as $val){
		   $sfrcode = "sf".$country_id.$state_id.$val;
		   $getsf = "select * from franchise_users where frcode='$sfrcode' and chapter_id='$val'";
	    $resgetsf = mysqli_query($conn,$getsf);
	    if(mysqli_num_rows($resgetsf)>0){
	        while($rowsf=mysqli_fetch_assoc($resgetsf)){
	            $sfranchise_id=$rowsf["id"];
	            $sfrcommission = $rowsf["commission"];
	        }
	       
	        $sfamount = $damount*$sfrcommission/100;
	        $payment_date = date("Y-m-d");
	        $sfcom = "insert into frachise_accounts (franchise_id,user_id,amount,payment_for,payment_date,status) values ('$sfranchise_id','$userid','$sfamount','subscription','$payment_date','0')";
	        $rssf = mysqli_query($conn,$sfcom);
	        
			//SF Wallet Update
			$getsf_wallet = "select * from franchise_wallet where franchise_id = '$sfranchise_id'";
			$res_sfwallet = mysqli_query($conn,$getsf_wallet);
			if(mysqli_num_rows($res_sfwallet)>0){
				while($row_sf_wallet= mysqli_fetch_assoc($res_sfwallet)){
					$sfwallet_amount = $row_sf_wallet["wallet"];
				}
				$sf_new_amount = $sfwallet_amount+$sfamount;
				$upd_sf_wallet = "update franchise_wallet set wallet='$sf_new_amount' where franchise_id = '$sfranchise_id'";
				$res_upd_sf_wallet = mysqli_query($conn,$upd_sf_wallet);
				
			}
			else{
				$sf_wallet = "insert into franchise_wallet (franchise_id, wallet, status) values ('$sfranchise_id','$sfamount','1')";
				$res_sf_wallet = mysqli_query($conn,$sf_wallet);
			}
			
			
	    }
	   }
		//New code To calculate commission
		
		
       
        $outp = json_encode($outp);
	    echo $outp ;
	
	$conn->close();
	
?>