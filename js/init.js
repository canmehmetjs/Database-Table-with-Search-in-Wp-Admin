jQuery('#bonuin_search_invoice').keyup( function( e ) {
	var link = this;
	var transaction_id   = jQuery('#bonuin_search_invoice').val();
	
	
	
	jQuery.ajax({
		type: 'POST',   // Adding Post method
		url: bonuin_params.ajaxurl, // Including ajax file
		data : {
			action: 'bonuin_search_transaction',
			transaction_id: transaction_id
		},
		beforeSend:function(){
		 
		 jQuery("#invoice_listings tbody tr").detach();
		 jQuery("#bonuin_loader").show();
		 
		 
		},
		success: function(data){ // Show returned data using the function.
			jQuery("#bonuin_loader").hide();
			jQuery("#create_make").html('Add Make');
			jQuery("#invoice_listings tbody").html(data);
			
			
		}
		
	});
	
	// Prevent the default behavior for the link
	e.preventDefault();
});



