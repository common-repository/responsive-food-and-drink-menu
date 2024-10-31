jQuery(document).ready(function() {
    var $ = jQuery;
    $('.pdf_menu_widget_grid_item_wrap').each(function() {
    	var w = $(this).width();
    	$(this).find( $('.pdf_menu_widget_grid_item ') ).css('height', w + 'px');
    });
});