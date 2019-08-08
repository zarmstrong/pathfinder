<?php



	$dnsFile = $_POST['dnsfile'];
	$JSON = $_POST['output'];
	$result = "success";
	
    //check if the file is writeable and if there was a log failure
	if(is_writable($dnsFile)) {
		
        //make a backup of the password file
		exec('cp -p ' . $dnsFile . " " . $dnsFile . ".bkp");

        //open the file for writing
        $fh=fopen($dnsFile, "w+");
        //if it opened successfully...
        if ($fh) {
			
			//replace ~ with carriage returns
			$updated_json = str_replace("~","\n",$JSON);
			
			//write the file
			fwrite($fh, $updated_json);
		} else {
			$result = "notopen";
		}
		
	} else {
		$result = "unwriteable";
	}

	//return to security page with result
	header("location:unifi.php?result=" . $result);

 
?>
