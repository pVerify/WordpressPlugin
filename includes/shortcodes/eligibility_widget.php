<?php 
/**
 * Eligibility Widget Shortcode
 */

    global $wpdb;
	
	$iframe_url = "";
    $err_msg = "";

	$tblname = $wpdb->prefix.'pverify_pl';
	$result = $wpdb->get_results( "SELECT * FROM $tblname ORDER BY id ASC LIMIT 1 ");
			
	$record_id = "";
	$clientApiId = "";
	$clientSecret = "";

	$shortcode_sec_class = "shortcode_sec_class_hide"; 
	if(count($result) > 0){
		$shortcode_sec_class = "shortcode_sec_class_show";		
		
		$record_id = $result[0]->id;	
		$clientApiId = $result[0]->client_api_id;	
		$clientSecret = $result[0]->client_secret;			
	}

	if(!empty($clientApiId) && !empty($clientSecret)){				
					
		$endpoint = "https://premium.pverify.com/Widget/Setup";			
		$body = array(
			'clientApiId'  => $clientApiId,
			'clientSecret' => $clientSecret,
		);
				
		$options = array(
			'method'  	=> 'POST',
			'body'      => $body,
			'headers'   => array("Content-Type: application/json", "Cookie: GCLB=COSQiI3D4s-MnQE")
		);
		
		$responsewp = wp_remote_post($endpoint, $options);
		$responsewp_body = wp_remote_retrieve_body($responsewp);
		$response_data = json_decode($responsewp_body);
				
		$res_data_array = array();
		$TransactionSetupId = "";
		$res_data_array = (array)$response_data;

		if( count($res_data_array) > 0 ){
			$TransactionSetupId = $res_data_array['TransactionSetupId'];
			$err_msg = $res_data_array['Message'];
		}				
				
		if(!empty($TransactionSetupId)){
			$iframe_url = "https://premium.pverify.com/Component/ElgInquiry?SetupId=".$TransactionSetupId;
		}
	}

	if(!empty($iframe_url)){ ?>
		<iframe width="100%" height="1000px" frameBorder="0" src="<?php echo $iframe_url; ?>"></iframe>
	<?php }else{?>
		<div class="errormsg_div"> <p><?php echo $err_msg; ?></p> </div>
	<?php }
?>