<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if( !class_exists('pVerify_Main') ){

	/**
	 * Plugin Main Class
	 */
	class pVerify_Main
	{
		public $plugin_file;
		public $plugin_dir;
		public $plugin_path;
		public $plugin_url;
	
		/**
		 * Static Singleton Holder
		 * @var self
		 */
		protected static $instance;
		
		/**
		 * Get (and instantiate, if necessary) the instance of the class
		 *
		 * @return self
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		} 
		
		public function __construct()
		{
			$this->plugin_file = PVERIFY_PLUGIN_FILE;
			$this->plugin_path = trailingslashit( dirname( $this->plugin_file ) );
			$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
			$this->plugin_url  = str_replace( basename( $this->plugin_file ), '', plugins_url( basename( $this->plugin_file ), $this->plugin_file ) );

			add_action('plugins_loaded', array( $this, 'plugins_loaded' ), 1);
			add_action('admin_menu', array($this,'fn_pVerify_admin_menu_callback'));
			add_action('admin_enqueue_scripts', array($this, 'fn_pVerify_enqueue_admin_scripts'));
			add_action('wp_enqueue_scripts', array($this, 'fn_pVerify_enqueue_front_scripts'));
			add_action('init', array($this, 'fn_pVerify_add_shortcode'));
			add_action('wp_ajax_submit_verifyKeysform', array($this,'fn_pVerify_submit_verifyKeysform'));
		}
		
		/**
		 * plugin activation callback
		 * @see register_deactivation_hook()
		 *
		 * @param bool $network_deactivating
		 */
		public static function activate() {
			$plugin_path = dirname( PVERIFY_PLUGIN_FILE );

			require_once $plugin_path . '/includes/pVerify-db-config.php';
			$db_obj = new pVerify_DB_Config();
			$db_obj->fn_create_pVerify_tables();
		}

		/**
		 * plugin deactivation callback
		 * @see register_deactivation_hook()
		 *
		 * @param bool $network_deactivating
		 */
		public static function deactivate( $network_deactivating ) {

		}
		
		/**
		 * plugin deactivation callback
		 * @see register_uninstall_hook()
		 *
		 * @param bool $network_uninstalling
		 */
		public static function uninstall() {
		   
		    global $table_prefix, $wpdb;
		    
		    $plugin_path = dirname( PVERIFY_PLUGIN_FILE );
		    require_once $plugin_path . '/includes/pVerify-db-config.php';
			$db_obj = new pVerify_DB_Config();
			$tblname = $db_obj->pverify_pl_table;
			$pverify_pl_table = $table_prefix . "$tblname";
			$wpdb->query( "DROP TABLE IF EXISTS $pverify_pl_table" );
		}
		
		public function plugins_loaded() {
			$this->loadLibraries();
		}

		/**
		 * Load all the required library files.
		 */
		protected function loadLibraries() {

			require_once $this->plugin_path . 'includes/pVerify-db-config.php';
		}

		public function fn_pVerify_admin_menu_callback(){

			add_menu_page(
		        __( 'pVerify', 'pverify' ),
		        'pVerify',
		        'manage_options',
		        'pverify',
		        array($this,'fn_pVerify_admin_menu_page_callback'),
		        'dashicons-admin-generic',
		        25
		    );
		}

		function fn_pVerify_admin_menu_page_callback(){

			global $table_prefix, $wpdb;
			$db_obj = new pVerify_DB_Config();
			$tblname = $db_obj->pverify_pl_table;
			$pverify_pl_table = $table_prefix . "$tblname";
			$result = $wpdb->get_results( "SELECT * FROM $pverify_pl_table ORDER BY id ASC LIMIT 1 ");

			$record_id = $client_api_id = $client_secret = "";
			$shortcode_sec_class = "shortcode_sec_class_hide"; 
			if(count($result) > 0){
				$shortcode_sec_class = "shortcode_sec_class_show";
				$record_id = $result[0]->id;	
				$client_api_id = $result[0]->client_api_id;	
				$client_secret = $result[0]->client_secret;
			}
			
			require_once( $this->plugin_path. 'includes/view/admin/pVerify-auth.php');
		}
        
        public function fn_pVerify_enqueue_admin_scripts(){

			wp_enqueue_style( 'pVerify-admin-css', $this->plugin_url."assets/css/admin.css" );
			wp_enqueue_script( 'pVerify-admin-script', $this->plugin_url."assets/js/admin.js" );

			$params = array(
				'ajaxurl' => admin_url( 'admin-ajax.php'),
			);
			wp_localize_script( 'pVerify-admin-script', 'script_params', $params );
		}

		public function fn_pVerify_enqueue_front_scripts(){

			wp_enqueue_style( 'pVerify-front-css', $this->plugin_url."assets/css/front_style.css" );
		}

		public function fn_pVerify_add_shortcode() {
		    
		    add_shortcode('pverify-widget', array($this, 'fn_pVerify_widget_shortcode'));
			add_shortcode('eligibility-widget', array($this, 'fn_pVerify_eligibility_widget_shortcode'));
		}

		public function fn_pVerify_widget_shortcode() {
						
			global $wpdb;

			$iframe_url = "";
		    $err_msg = "";

			$tblname = $wpdb->prefix.'pverify_pl';
			$result = $wpdb->get_results( "SELECT * FROM $tblname ORDER BY id ASC LIMIT 1 ");
					
			$clientApiId = "";
			$clientSecret = "";

			if(count($result) > 0){
						
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
					$iframe_url = "https://premium.pverify.com/Component/EstimateInquiry?SetupId=".$TransactionSetupId;
				}
			}

			ob_start();
			require_once( $this->plugin_path. 'includes/view/shortcodes/estimate_widget.php');
			return ob_get_clean();
		}

		public function fn_pVerify_eligibility_widget_shortcode() {
			
			global $wpdb;
	
			$iframe_url = "";
		    $err_msg = "";

			$tblname = $wpdb->prefix.'pverify_pl';
			$result = $wpdb->get_results( "SELECT * FROM $tblname ORDER BY id ASC LIMIT 1 ");
					
			$clientApiId = "";
			$clientSecret = "";

			if(count($result) > 0){
				
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

			ob_start();
			require_once( $this->plugin_path. 'includes/view/shortcodes/eligibility_widget.php');			
			return ob_get_clean();
		}

		public function fn_pVerify_submit_verifyKeysform() {

			$posted_data   = $_POST;
			$record_id     = sanitize_text_field( $posted_data['record_id'] );
			$client_api_id = sanitize_text_field( $posted_data['client_api_id'] );
			$client_secret = sanitize_text_field( $posted_data['client_secret'] );
			$response = array('status' => 'failed', 'msg' => 'Something went wrong, please try again after some time.');

			if(empty($client_api_id) || empty($client_secret)){
				
				echo json_encode($response);
				exit();
			}

			global $table_prefix, $wpdb;
			$db_obj = new pVerify_DB_Config();
			$tblname = $db_obj->pverify_pl_table;
			$pverify_pl_table = $table_prefix . "$tblname";

			if(!empty($record_id)){

				$updated = $wpdb->query($wpdb->prepare("UPDATE $pverify_pl_table SET client_api_id='$client_api_id', client_secret='$client_secret' WHERE id = $record_id"));
				if($updated){
					$response = array();
					$response['status'] = "success";
					$response['msg'] = "Details updated successfully!!";
				}
			}else{
				
				$insertq = $wpdb->query("INSERT INTO $pverify_pl_table (client_api_id, client_secret) VALUES ('$client_api_id', '$client_secret')"  );
				$response = array();
				if($insertq){
					$response['status'] = "success";
					$response['msg'] = "Details saved successfully!!";
				}else{
					$response['status'] = "failed";
					$response['msg'] = "Details not saved.";
				}
			}

			echo json_encode($response);
			exit();
		}
    }
}
