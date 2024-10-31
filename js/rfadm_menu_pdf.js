jQuery(function($) {
 
	/*******************************************************************************************
	 * This is for the PDF of the menu
	 *******************************************************************************************/

	// Check to see if the 'Delete File' link exists on the page...
    if($('a#rfadm_menu_pdf_delete').length === 1) {
 
        // Since the link exists, we need to handle the case when the user clicks on it...
        $('#rfadm_menu_pdf_delete').click(function(evt) {
         
            // We don't want the link to remove us from the current page
            // so we're going to stop it's normal behavior.
            evt.preventDefault();
             
            // Find the text input element that stores the path to the file
            // and clear it's value.
            $('#rfadm_menu_pdf_url').val('');
            $('.current_pdf').fadeOut();
             
            // Hide this link so users can't click on it multiple times
            $(this).hide();
        });
    }
    
    
	/*******************************************************************************************
	 * This is for the menu items images
	 *******************************************************************************************/
   if($('a.rfadm_menu_item_delete').length === 1) {
    	 
        // Since the link exists, we need to handle the case when the user clicks on it...
        $('.rfadm_menu_item_delete').click(function(evt) {
         
            // We don't want the link to remove us from the current page
            // so we're going to stop it's normal behavior.
            evt.preventDefault();
             
            // Find the text input element that stores the path to the file
            // and clear it's value.
            $(this).parents('.menu_item_wrap').find('.img_url_input').val('');
             
            // Hide this link so users can't click on it multiple times
            $(this).parents('.menu_item_wrap').find('.show_image_name').hide();
            $(this).hide();
            
        });
   }
   
   // alert if non-pdf upload
   $('input#rfadm_pdf_menu_pdf').change(function () {
	   var f = $(this).val();
	   if (f.length > 0) {
		   var last_three = f.substr(f.length - 3).toLowerCase();
		   if (last_three != 'pdf') {
			   $(this).val('');
			   alert('Warning: This file does not appear to be a PDF. Only PDF files will be accepted.');
		   }
	   }
   });
   
});