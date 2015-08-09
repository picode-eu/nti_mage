jQuery(document).ready(function()
{
	/*
	 * Avoid duplicate content on customer acount edit page (admin)
	 * find all furnizor attributes from furnizor information tab
	 * and remove them from account information tab
	*/
	var providerInputs = jQuery('#customer_info_tabs_account_content input, #customer_info_tabs_account_content select, #customer_info_tabs_account_content textarea');
	
	providerInputs.each(function(){
		var inputId = jQuery(this).attr('id');
		var idPart = inputId.split('_');
		
		if(jQuery.inArray('accountfurnizor', idPart) !== -1) {
		    jQuery('#customer_info_tabs_account_content #' + inputId).closest('tr').remove();
		}
		
		if(jQuery.inArray('accountbusiness', idPart) !== -1) {
            jQuery('#customer_info_tabs_account_content #' + inputId).closest('tr').remove();
        }
        
        if(jQuery.inArray('accountac', idPart) !== -1) {
            jQuery('#customer_info_tabs_account_content #' + inputId).closest('tr').remove();
        }
	});
	
	/*
	 * add required class to provider inputs
	 * if the customer belongs to providers group
	*/
	var customerGroup = jQuery('#_accountgroup_id');
	
    // add required labels and classes on load
    /*
    if(customerGroup.val() != 1){ // 1 is for general
        jQuery('.customer_info_tabs_furnizor_details_tab_content .hor-scroll tr').each(function(){
           // get orig label text
           var labelText = jQuery(this).find('td.label label').text();
           // clear orig label text
           jQuery(this).find('td.label label').text('');
           // add new label text
           jQuery(this).find('td.label label').html(labelText + ' <span class="required">*</span>');
           // add reguired class
           jQuery(this).find('input[type="text"]').addClass('required-entry');
           jQuery(this).find('select').addClass('required-entry');
       });
    }
    
    // remove/add required labels and classes on change
    customerGroup.change(function(){
        if(jQuery(this).val() == '1'){ // 1 is for general
            jQuery('.customer_info_tabs_furnizor_details_tab_content .hor-scroll').each(function(){
                jQuery(this).find('span.required').remove();
                jQuery(this).find('.required-entry').removeClass('required-entry');
            });
       }else{
           jQuery('.customer_info_tabs_furnizor_details_tab_content .hor-scroll tr').each(function(){
               // get orig label text
               var labelText = jQuery(this).find('td.label label').text();
               // clear orig label text
               jQuery(this).find('td.label label').text('');
               // add new label text
               jQuery(this).find('td.label label').html(labelText + ' <span class="required">*</span>');
               // add reguired class
               jQuery(this).find('input[type="text"]').addClass('required-entry');
               jQuery(this).find('select').addClass('required-entry');
           });
       }
    });
    */
   
    /*
     * 
    */
});
