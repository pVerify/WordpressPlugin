<?php
/**
 * pVerify Auth save page
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1>pVerify</h1>
			<div class="adminform_sec">
				<form method="post" action="" id="pverify_config_form">
					<input type="hidden" name="record_id" id="record_id" class="form-control" value="<?php echo $record_id; ?>">
					<div class="form-group">
			        	<label class="form-lables">Client API ID:</label>
			            <input type="text" name="client_api_id" id="client_api_id" class="form-control" value="<?php echo $client_api_id; ?>" required="required">
    				</div>

    				<div class="form-group">
			        	<label class="form-lables">Client Secret:</label>
			            <input type="text" name="client_secret" id="client_secret" value="<?php echo $client_secret; ?>" class="form-control" required="required">
    				</div>

    				<div class="form-group">
    					<button type="submit" class="btn btn-default admin_frm_btn" id="admin_frm_btn">Save</button>	
    				</div>
				</form>

				<div class="alert alert-success alert_info" id="success_msg">
				    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				    <span></span>
				</div>

				<div class="alert alert-danger alert_info" id="error_msg">
				    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				    <span></span>
				</div>

				<div class="shortcode_info_sec <?php echo $shortcode_sec_class; ?>" id="shortcode_info_sec">
					<h3>Use this shortcode on page</h3>
					<?php 
						if(count($result) > 0){
					?>
						<h3>Estimate Inquiry Widget: <strong>[pverify-widget]</strong></h3>
						<h3>Eligibility Widget: <strong>[eligibility-widget]</strong></h3>
					<?php 
						}
					?>
				</div>			
				
			</div>
		</div>
	</div>
</div>
