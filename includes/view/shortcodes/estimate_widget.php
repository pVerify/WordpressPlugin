<?php 
/**
 * Estimate Widget Shortcode 
 */

	if(!empty($iframe_url)){ ?>
		<iframe width="100%" height="900px" frameBorder="0" src="<?php echo esc_url($iframe_url); ?>"></iframe>
	<?php }else{ ?>
		<div class="errormsg_div"> <p><?php echo $err_msg; ?></p> </div>
	<?php }
?> 
