/**
 * Custom scripts needed for the colorpicker, image button selectors,
 * and navigation tabs.
 */

jQuery(document).ready(function($) {

	// Loads the color pickers
	$('.of-color').wpColorPicker();

	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');
	});

	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	// Loads tabbed sections if they exist
	if ( $('.nav-tab-wrapper').length > 0 ) {
		options_framework_tabs();
	}

	function options_framework_tabs() {

		var $group = $('.group'),
			$navtabs = $('.nav-tab-wrapper a'),
			active_tab = '';

		// Hides all the .group sections to start
		$group.hide();

		// Find if a selected tab is saved in localStorage
		if ( typeof(localStorage) != 'undefined' ) {
			active_tab = localStorage.getItem('active_tab');
		}

		// If active tab is saved and exists, load it's .group
		if ( active_tab != '' && $(active_tab).length ) {
			$(active_tab).fadeIn();
			$(active_tab + '-tab').addClass('nav-tab-active');
		} else {
			$('.group:first').fadeIn();
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}

		// Bind tabs clicks
		$navtabs.click(function(e) {

			e.preventDefault();

			// Remove active class from all tabs
			$navtabs.removeClass('nav-tab-active');

			$(this).addClass('nav-tab-active').blur();

			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem('active_tab', $(this).attr('href') );
			}

			var selected = $(this).attr('href');

			$group.hide();
			$(selected).fadeIn();

		});
	}
	

	 
	/*$('.of-chosen').chosen({
	     no_results_text: "没有找到结果！",
	     allow_single_deselect: true,
	     disable_search_threshold: 15,
	     width: '100%'
	});*/
	
	/*$(".of-chosen").select2({
	    tags: true,
	    width: '100%',
	    multiple: true
	});*/
	/*$(".of-chosen").select2({
        triggerChange: true,
        allowClear: true,
        width: "100%"
    });
    
    default_params = {};
    default_params.bindOrder = 'sortableStop';
    default_params.sortableOptions = {placeholder: 'ui-state-highlight'};
    
    $(".of-chosen").select2Sortable(default_params);
    
    $(".of-chosen").on("change", function() {$(".of-chosen").select2SortableOrder();});*/
    
    
    $('#optionsframework').find( '.of-chosen' ).each(
        function() {
            var default_params = {
                width: '100%',
                triggerChange: true,
                allowClear: true
            };

            $( this ).select2( default_params );

            if ( $( this ).hasClass( 'select2-sortable' ) ) {
                default_params = {};
                default_params.bindOrder = 'sortableStop';
                default_params.sortableOptions = {placeholder: 'ui-state-highlight'};
                $( this ).select2Sortable( default_params );
            }

            $( this ).on(
                "change", function() {
                    $( this ).select2SortableOrder();
                }
            );
        }
    );

	
	jQuery('#notice_cat_s').click(function() {
	  	jQuery('#section-site_notice_cat').fadeToggle(400);
	});

	if (jQuery('#notice_cat_s:checked').val() !== undefined) {
		jQuery('#section-site_notice_cat').show();
	}
		
	$('.'+$('#alipay_option').children('option:selected').val()).show();
	if($('#wxpay_option').children('option:selected').val() == 'xhpay'){
		$('.xhwxpay').show();
	}else{
		$('.'+$('#wxpay_option').children('option:selected').val()).show();	
	}
		
	$('#alipay_option').change(function(){
		var p1 = $(this).children('option:selected').val()
		if(p1 == 'alipay'){
			$('.alipay').show();
		}else{
			$('.alipay').hide();
		}
			
		if(p1 == 'f2fpay'){
			$('.f2fpay').show();
		}else{
			$('.f2fpay').hide();
		}
			
		if(p1 == 'alipay_jk'){
			$('.alipay_jk').show();
		}else{
			$('.alipay_jk').hide();
		}
			
		if(p1 == 'codepay'){
			$('.codepay').show();
		}else{
			$('.codepay').hide();
		}
			
		if(p1 == 'xhpay'){
			$('.xhpay').show();
		}else{
			$('.xhpay').hide();
		}
　　});
　　　　
　　$('#wxpay_option').change(function(){
		var p2 = $(this).children('option:selected').val();
			
		if(p2 == 'wxpay'){
			$('.wxpay').show();
		}else{
			$('.wxpay').hide();
		}
			
		if(p2 == 'payjs'){
			$('.payjs').show();
		}else{
			$('.payjs').hide();
		}
			
		if(p2 == 'xhpay'){
			$('.xhwxpay').show();
		}else{
			$('.xhwxpay').hide();
		}
　　});

});
