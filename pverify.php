<?php 
/**
 * @package Pverify
 * @version 1.0
 */
/*
Plugin Name: Pverify
Plugin URI: 
Description: This is pverify plugin
Author: Digambar Patil
Version: 1.0
Author URI: 
Licence:
Text-domain:Pverify
*/

if(!class_exists('pverify_class')){
	if( !defined( 'ABSPATH' ) ) {
		die;
	}

	class pverify_class{

		function __construct()
		{
			add_action('admin_menu', array($this,'pverify_submenu_page'));
			register_activation_hook(__FILE__, array($this,'fn_pverify_activate'));
			register_deactivation_hook( __FILE__, array($this,'fn_delete_tblpl'));
			add_action('admin_enqueue_scripts', array($this, 'inc_setting_all_scripts'));
			add_action('wp_enqueue_scripts', array($this, 'inc_front_scripts'));
			add_action( "wp_ajax_submit_verifyKeysform", array($this,"submit_verifyKeysform" ));
			add_action( "wp_ajax_nopriv_submit_verifyKeysform", array($this,"submit_verifyKeysform" ));	
			add_shortcode('pverify-widget', array($this, 'pverify_widget_shortcode'));
			add_shortcode('eligibility-widget', array($this, 'eligibility_widget_shortcode'));
		}

		function fn_pverify_activate(){
			global $wpdb;
			$table_name = $wpdb->prefix . 'pverify_pl';
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				client_api_id varchar(200) NOT NULL,
				client_secret varchar(200) NOT NULL,
				created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		function fn_delete_tblpl(){
			global $wpdb;
			$table_name = $wpdb->prefix . 'pverify_pl';
			$sql = "DROP TABLE IF EXISTS $table_name";
			$wpdb->query($sql);
			delete_option("my_plugin_db_version");
		}
		function inc_setting_all_scripts(){
			wp_register_style( 'PverifyStyleAdmin', plugins_url( '/assets/css/admin.css', __FILE__ ));
			wp_enqueue_style( 'PverifyStyleAdmin');
			wp_enqueue_script( 'PverifyAdminScript', plugins_url( '/assets/js/admin.js', __FILE__ ));

			$params = array(
				'ajaxurl' => admin_url( 'admin-ajax.php'),
			);
			wp_localize_script( 'PverifyAdminScript', 'script_params', $params );
		}

		function inc_front_scripts(){
			wp_register_style( 'PverifyStyleFront', plugins_url( '/assets/css/front_style.css', __FILE__ ));
			wp_enqueue_style( 'PverifyStyleFront');
		}

		function pverify_submenu_page(){
			add_menu_page('Pverify', 'Pverify', 'manage_options', 'pverify', array($this,'fn_pverify_output') );	
		}

		function fn_pverify_output(){
			require_once( plugin_dir_path( __FILE__ ). '/includes/admin/configuration.php');
		}

		function submit_verifyKeysform(){
			global $wpdb;

			$record_id = $_POST['record_id'];
			$client_api_id = $_POST['client_api_id'];
			$client_secret = $_POST['client_secret'];
			$response = array();

			if(!empty($client_api_id) && !empty($client_secret)){

				if(!empty($record_id)){
					$table_name = $wpdb->prefix.'pverify_pl';
					$wpdb->query($wpdb->prepare("UPDATE $table_name SET client_api_id='$client_api_id', client_secret='$client_secret' WHERE id = $record_id"));
					$response['status'] = "success";
					$response['msg'] = "Details updated successfully!!";
				}else{
					
					$table_name = $wpdb->prefix.'pverify_pl';
					$tbl_data = array(		
						'client_api_id' => $client_api_id,		
						'client_secret' => $client_secret,
					);
					
					$insertq = $wpdb->query("INSERT INTO ".$table_name." (client_api_id, client_secret) VALUES ('$client_api_id', '$client_secret')"  );
					if($insertq){
						$response['status'] = "success";
						$response['msg'] = "Details saved successfully!!";
					}else{
						$response['status'] = "failed";
						$response['msg'] = "Details not saved!";
					}
				}
			}else{
				$response['status'] = "failed";
				$response['msg'] = "All details are required!";
			}

			die(json_encode($response));
		}

		function pverify_widget_shortcode(){
			global $wpdb;
			$dhtml = "";
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
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://premium.pverify.com/Widget/Setup",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>"{\r\n    \"clientApiId\": \"$clientApiId\",\r\n    \"clientSecret\": \"$clientSecret\"\r\n}",
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json",
				    "Cookie: GCLB=COSQiI3D4s-MnQE"
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);

				$response_data = json_decode($response);
				
				$res_data_array = array();
				$TransactionSetupId = "";
				$res_data_array = (array)$response_data;
				if( count($res_data_array) > 0 ){
					$TransactionSetupId = $res_data_array['TransactionSetupId'];
					$err_msg = $res_data_array['Message'];
				}				
				
				if(!empty($TransactionSetupId)){
					$iframe_url = "https://premium.pverify.com/Component/EstimateInquiry?SetupId=".$TransactionSetupId;
				}
			}

			if(!empty($iframe_url)){
				$dhtml .= '<iframe width="100%" height="900px" frameBorder="0" src="'.$iframe_url.'"></iframe>'; 
			}else{
				$dhtml .= '<div class="errormsg_div"> <p>'.$err_msg.'</p> </div>';
			}
			
			return $dhtml;
		}

		function eligibility_widget_shortcode(){
			global $wpdb;
			$dhtml = "";
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
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://premium.pverify.com/Widget/Setup",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>"{\r\n    \"clientApiId\": \"$clientApiId\",\r\n    \"clientSecret\": \"$clientSecret\"\r\n}",
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json",
				    "Cookie: GCLB=COSQiI3D4s-MnQE"
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);

				$response_data = json_decode($response);				
				
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

			if(!empty($iframe_url)){
				$dhtml .= '<iframe width="100%" height="1000px" frameBorder="0" src="'.$iframe_url.'"></iframe>'; 
			}else{
				$dhtml .= '<div class="errormsg_div"> <p>'.$err_msg.'</p> </div>';
			}
			
			return $dhtml;
		}
	}
}

$obj_pverify = new pverify_class;
?>