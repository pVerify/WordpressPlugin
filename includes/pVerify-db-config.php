<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if( !class_exists('pVerify_DB_Config') ){

	/**
	 * Plugin Database files Class
	 */
	class pVerify_DB_Config
	{
				
		public $pverify_pl_table;
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
			$this->pverify_pl_table = 'pverify_pl';
		}

		public function fn_create_pVerify_tables()
		{
			global $table_prefix, $wpdb;
			$tblname = $this->pverify_pl_table;
			$pverify_pl_table = $table_prefix . "$tblname";
			if($wpdb->get_var( "show tables like '$pverify_pl_table'" ) != $pverify_pl_table) 
			{
				$sql = "CREATE TABLE `". $pverify_pl_table . "` ( ";
		        $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
		        $sql .= "  `client_api_id` varchar(500) NOT NULL, ";
		        $sql .= "  `client_secret` varchar(500) NOT NULL, ";
		        $sql .= "  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP, ";
		        $sql .= "  PRIMARY KEY (`id`) "; 
		        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
		        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		        dbDelta($sql);
			}
		}
    }
}
