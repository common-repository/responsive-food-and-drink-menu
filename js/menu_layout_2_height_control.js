jQuery(document).ready(function() {
    var $ = jQuery;
    $('.rfadm_menu_wrap').each(function() {
    	var h = $(this).find( $('.layout_2_text_wrap') ).height();
    	$(this).find( $('.menu_image_layout_wrap ') ).css('height', h + 'px');
    });
});