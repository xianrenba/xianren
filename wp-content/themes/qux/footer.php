<?php  
	if( _hui('footer_brand_s') ){
		_moloader('mo_footer_brand', false);
	}
?>

<footer class="footer">
	<div class="container">
		<?php if( _hui('flinks_s') && _hui('flinks_cat') && ((_hui('flinks_home_s')&&is_home()) || (!_hui('flinks_home_s'))) ){ ?>
			<div class="flinks">
				<?php 
					wp_list_bookmarks(array(
						'category'         => _hui('flinks_cat'),
						'show_description' => false,
						'between'          => '',
						'title_before'     => '<strong>',
    					'title_after'      => '</strong>',
						'category_before'  => '',
						'category_after'   => ''
					));
				?>
			</div>
		<?php } ?>
		<?php if( _hui('fcode') ){ ?>
			<div class="fcode">
				<?php echo _hui('fcode') ?>
			</div>
		<?php } ?>
		<p>&copy; <?php echo date('Y'); ?> <a href="<?php echo home_url() ?>"><?php echo get_bloginfo('name') ?></a> &nbsp; <?php echo _hui('footer_seo') ?></p>
		<?php echo _hui('trackcode') ?> &nbsp; 本次加载用时：<?php timer_stop(1); ?>秒
	</div>
</footer>
<?php if(!is_user_logged_in() && _hui('user_page_s')) load_template(UM_DIR . '/template/loginbox.php');  ?>
<?php 
	if( _hui('kefu') ){ 
		$kefuhtml = '';
		if( _hui('kefu_m') && wp_is_mobile() ){
			$kefuorder = trim(_hui('kefu_m_px'));
		}else{
			$kefuorder = trim(_hui('kefu_px'));
		}
		if( $kefuorder ){
			$kefuorder = explode(' ', $kefuorder);
			foreach ($kefuorder as $key => $value) {
				switch ($value) {
					case '1':
						$kefuhtml .= '<li class="rollbar-totop"><a href="javascript:(scrollTo());"><i class="fa fa-angle-up"></i><span>'._hui('kefu_top_tip_m').'</span></a>'.(_hui('kefu_top_tip')?'<h6>'. _hui('kefu_top_tip') .'<i></i></h6>':'').'</li>';
						break;

					case '2':
						if( _hui('fqq_id') ) $kefuhtml .= '<li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='. _hui('fqq_id') .'&site=qq&menu=yes"><i class="fa fa-qq"></i><span>'._hui('fqq_tip_m').'</span></a>'.(_hui('fqq_tip')?'<h6>'. _hui('fqq_tip') .'<i></i></h6>':'').'</li>';
						break;

					case '3':
						if( _hui('kefu_tel_id') ) $kefuhtml .= '<li><a href="tel:'. _hui('kefu_tel_id') .'"><i class="fa fa-phone"></i><span>'._hui('kefu_tel_tip_m').'</span></a>'.(_hui('kefu_tel_tip')?'<h6>'. _hui('kefu_tel_tip') .'<i></i></h6>':'').'</li>';
						break;

					case '4':
						if( _hui('kefu_wechat_qr') ) $kefuhtml .= '<li class="rollbar-qrcode"><a href="javascript:;"><i class="fa fa-qrcode"></i><span>'._hui('kefu_wechat_tip_m').'</span></a>'.(_hui('kefu_wechat_tip')?'<h6>'. _hui('kefu_wechat_tip') .(_hui('kefu_wechat_qr')?'<img src="'._hui('kefu_wechat_qr').'">':'').'<i></i></h6>':'').'</li>';
						break;

					case '5':
						if( _hui('kefu_sq_id') ) $kefuhtml .= '<li><a target="_blank" href="'. _hui('kefu_sq_id') .'"><i class="fa fa-globe"></i><span>'._hui('kefu_sq_tip_m').'</span></a>'.(_hui('kefu_sq_tip')?'<h6>'. _hui('kefu_sq_tip') .'<i></i></h6>':'').'</li>';
						break;
                    
					case '6':
						if( (is_single()||is_page()) && comments_open() ) $kefuhtml .= '<li><a href="javascript:(scrollTo(\'#comments\',-15));"><i class="fa fa-comments"></i><span>'._hui('kefu_comment_tip_m').'</span></a>'.(_hui('kefu_comment_tip')?'<h6>'. _hui('kefu_comment_tip') .'<i></i></h6>':'').'</li>';
						break;
                    
                    case '7':
					    $kefuhtml .= '<li><a id="StranLink"><i class="fa wencode">繁</i><span>'._hui('f_wencode_m').'</span></a>'.(_hui('f_wencode_tip')?'<h6>'. _hui('f_wencode_tip') .'<i></i></h6>':'').'</li>';
						break;
                    
                    case '8':
                        $layout = the_layout();
                        if( is_home() && _hui('index_layout')){
                        	if( $layout=='index-blog'){
                        		$kefuhtml .= '<li id="layoutswt" class="mobile-hide"><a herf="javascript:;"><i class="fa fa-th is_blog"></i><span>CMS模式</span></a><h6>CMS模式<i></i></h6></li>'; 
                        	} elseif( $layout=='index-cms'){
                        		$kefuhtml .= '<li id="layoutswt" class="mobile-hide"><a herf="javascript:;"><i class="fa fa-th-large is_cms"></i><span>卡片模式</span></a><h6>卡片模式<i></i></h6></li>'; 
                        	} else{
                        		$kefuhtml .= '<li id="layoutswt" class="mobile-hide"><a herf="javascript:;"><i class="fa fa-th-list is_blocks"></i><span>博客模式</span></a><h6>博客模式<i></i></h6></li>';
                        	}
                        }
                        break;
                    case '9':
                    	if(isset($_COOKIE["um_qux_dark"]) && $_COOKIE["um_qux_dark"] == 'dark'){
                    		$kefuhtml .= '<li id="qux-dark" class="mobile-hide"><a herf="javascript:;"><i class="fa fa-sun-o"></i><span>日间模式</span></a><h6>日间模式<i></i></h6></li>';
                    	}else{
                    		$kefuhtml .= '<li id="qux-dark" class="mobile-hide"><a herf="javascript:;"><i class="fa fa-moon-o"></i><span>夜间模式</span></a><h6>夜间模式<i></i></h6></li>';
                    	}
					default:
						
						break;
				}
			}

	    	echo '<div class="rollbar rollbar-'._hui('kefu').'"><ul>'.$kefuhtml.'</ul></div>';
		}
	}
?>

<?php  
	/*$roll = '';
	if( is_home() && _hui('sideroll_index_s') ){
		$roll = _hui('sideroll_index');
	}else if( (is_category() || is_tag() || is_search()) && _hui('sideroll_list_s') ){
		$roll = _hui('sideroll_list');
	}else if( is_single() && _hui('sideroll_post_s') ){
		$roll = _hui('sideroll_post');
	}
	if( $roll ){
		$roll = json_encode(explode(' ', $roll));
	}else{
		$roll = json_encode(array());
	}*/
	_moloader('mo_get_user_rp');

    $fullimage = 0;
    if( (is_single() && _hui('full_image')) || (is_page() && _hui('page_full_image')) ){
		$fullimage = 1;
	}
	if(_hui('wxpay_option') == 'wxpay'){
		$wxpay = _url_for('wxpay');//THEME_URI .'/payment/wxpay/wxpay.php';
	}else if(_hui('wxpay_option') == 'xhpay'){
		$wxpay = _url_for('xhpay');//THEME_URI .'/payment/xhpay/pay.php';
	}else{
		$wxpay = _url_for('payjs');//THEME_URI .'/payment/payjs/payjs.php';
	}
	if(_hui('alipay_option') == 'alipay'){
		$alipay = _url_for('alipay');//THEME_URI .'/payment/alipay/alipayapi.php';
	}else if(_hui('alipay_option') == 'codepay'){
		$alipay = _url_for('codepay');
	}else if(_hui('alipay_option') == 'xhpay'){
		$alipay = _url_for('xhpay');
	}else if(_hui('alipay_option') == 'f2fpay'){
		$alipay = _url_for('qrpay');
	}else{
		$alipay = _url_for('mqpay');//THEME_URI .'/payment/alipay_jk/alipay_jk.php';
	}
?>
<script>
window.jsui={
    www: '<?php echo home_url() ?>',
    uri: '<?php echo get_stylesheet_directory_uri() ?>',
    ver: '<?php echo THEME_VERSION ?>',
	/*roll: <?php echo $roll ?>,*/
    ajaxpager: '<?php echo _hui("ajaxpager") ?>',
    url_rp: '<?php echo mo_get_user_rp() ?>',
    fullimage: '<?php echo $fullimage ?>',
	wxpay_url: '<?php echo $wxpay; ?>',
	alipay_url: '<?php echo $alipay; ?>',
	ajaxloading: '<?php echo THEME_URI .'/img/floading.gif'; ?>' 
};
</script>
<?php wp_footer(); ?>
<?php
if (_hui ('copy_b') && is_singular()) echo '<script type="text/Javascript">document.oncontextmenu=function(e){return false;};document.onselectstart=function(e){return false;};</script><style>body{ -moz-user-select:none;}</style><SCRIPT LANGUAGE=javascript>if (top.location != self.location)top.location=self.location;</SCRIPT><noscript><iframe src=*.html></iframe></noscript>'; ?>
<?php
if (_hui ('copydialog_b') && is_singular()) echo '<script type="text/javascript">document.body.oncopy=function(){swal("复制成功", "若要转载请务必保留原文链接，申明来源，谢谢合作！", "success");}</script>'; ?>
</body>
</html>