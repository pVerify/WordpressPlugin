jQuery(document).ready(function(){
	jQuery("#pverify_config_form").submit(function(e){
		e.preventDefault();
		var formData = jQuery("#pverify_config_form").serialize();
		
		jQuery.ajax({
			url: script_params.ajaxurl+"?action=submit_verifyKeysform",
			type: 'POST',
			dataType: 'json',
			data: formData,
			success: function (response) {
				
				if(response.status == "success"){
					jQuery("#error_msg").hide();
					jQuery("#success_msg").show();
					jQuery("#success_msg span").text(response.msg);
				}	

				if(response.status == "failed"){
					jQuery("#success_msg").hide();
					jQuery("#error_msg").show();
					jQuery("#error_msg span").text(response.msg);
				}

				setTimeout(function(){ 
					jQuery("#success_msg").hide();
					jQuery("#error_msg").hide();
					location.reload();
				}, 3000);
			},
		});
		return false;
	});
});

