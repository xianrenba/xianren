<?php
/**
* @package   Options_Framework
* @author    Devin Price <devin@wptheming.com>
* @license   GPL-2.0+
* @link      http://wptheming.com
* @copyright 2010-2014 WP Theming
*/ class Options_Framework_Admin 
{
	protected $options_screen = null;
	public function init() 
	{
		$options = & Options_Framework::_optionsframework_options();
		if ( $options ) 
		{
			add_action( 'admin_menu', array( $this, 'add_custom_options_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_init', array( $this, 'settings_init' ) );
			add_action( 'wp_before_admin_bar_render', array( $this, 'optionsframework_admin_bar' ) );
		}
	}
	function settings_init() 
	{
		$options_framework = new Options_Framework;
		$name = $options_framework->get_option_name();
		register_setting( 'optionsframework', $name, array ( $this, 'validate_options' ) );
		add_action( 'optionsframework_after_validate', array( $this, 'save_options_notice' ) );
	}
	static function menu_settings() 
	{
		$menu = array( 'mode' => 'submenu', 'page_title' => __( '主题设置', 'haoui' ), 'menu_title' => __( '主题设置', 'haoui' ), 'capability' => 'edit_theme_options', 'menu_slug' => 'options-framework', 'parent_slug' => 'themes.php', 'icon_url' => 'dashicons-admin-customizer', 'position' => '61' );
		return apply_filters( 'optionsframework_menu', $menu );
	}
	function add_custom_options_page() 
	{
		$menu = $this->menu_settings();
		$secret = get_option('dux-secret-key');
		$last = get_option('dux-last-time');
		$now = time();
		$recheck = true;
		if($secret && (!$last || (( $now - $last ) > 3600)))
		{
			$result_body = json_decode($this->option_send_request($secret));
			if($result_body->code=='1')
			{
				$recheck = true;
				update_option("dux-last-time" ,time());
			}
			else
			{
				$recheck = false;
			}
		}
		if( !$secret && !$recheck )
		{
			$this->options_screen = add_menu_page( '主题激活', '主题激活', 'edit_theme_options', 'options-framework', array( $this, 'option_panel_active' ), 'dashicons-admin-network' , '61' );
		}
		else
		{
			$this->options_screen = add_menu_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], array( $this, 'options_page' ), $menu['icon_url'], $menu['position'] );
		}
	}
	function enqueue_admin_styles( $hook ) 
	{
		if ( $this->options_screen != $hook ) return;
		wp_enqueue_style( 'optionsframework', OPTIONS_FRAMEWORK_DIRECTORY . 'css/optionsframework.css', array(), Options_Framework::VERSION );
		wp_enqueue_style( 'wp-color-picker' );
	}
	function enqueue_admin_scripts( $hook ) 
	{
		if ( $this->options_screen != $hook ) return;
		wp_enqueue_script( 'options-custom', OPTIONS_FRAMEWORK_DIRECTORY . 'js/options-custom.js', array( 'jquery','wp-color-picker' ), Options_Framework::VERSION );
		add_action( 'admin_head', array( $this, 'of_admin_head' ) );
	}
	function of_admin_head() 
	{
		do_action( 'optionsframework_custom_scripts' );
	}
	function options_page() 
	{
		?>

		<div id="optionsframework-wrap" class="wrap">

		<?php $menu = $this->menu_settings();
		?>
		<h2><?php echo esc_html( $menu['page_title'] );
		?></h2>

	   <h2 class="nav-tab-wrapper">
	       <?php echo Options_Framework_Interface::optionsframework_tabs();
		?>
	   </h2>

	   <?php settings_errors( 'options-framework' );
		?>

	   <div id="optionsframework-metabox" class="metabox-holder">
		   <div id="optionsframework" class="postbox">
				<form action="options.php" method="post">
				<?php settings_fields( 'optionsframework' );
		?>
				<?php Options_Framework_Interface::optionsframework_fields();
		?>
				<div id="optionsframework-submit">
					<input type="submit" class="button-primary" name="update" value="<?php echo __( '保存设置', 'haoui' );
		?>" />
					<input type="submit" class="reset-button button-secondary" name="reset" value="<?php echo __( '重置全部设置', 'haoui' );
		?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'textdomain' ) );
		?>' );" />
					<div class="clear"></div>
				</div>
				</form>
			</div> <!-- / #container -->
		</div>
		<?php do_action( 'optionsframework_after' );
		?>
		</div> <!-- / .wrap -->

	<?php
}
	function validate_options( $input ) 
	{
		if ( isset( $_POST['reset'] ) ) 
		{
			add_settings_error( 'options-framework', 'restore_defaults', __( '设置已重置！', 'haoui' ), 'updated fade' );
			return $this->get_default_values();
		}
		$clean = array();
		$options = & Options_Framework::_optionsframework_options();
		foreach ( $options as $option ) 
		{
			if ( ! isset( $option['id'] ) ) 
			{
				continue;
			}
			if ( ! isset( $option['type'] ) ) 
			{
				continue;
			}
			$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );
			if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) 
			{
				$input[$id] = false;
			}
			if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) 
			{
				foreach ( $option['options'] as $key => $value ) 
				{
					$input[$id][$key] = false;
				}
			}
			if ( has_filter( 'of_sanitize_' . $option['type'] ) ) 
			{
				$clean[$id] = apply_filters( 'of_sanitize_' . $option['type'], $input[$id], $option );
			}
		}
		do_action( 'optionsframework_after_validate', $clean );
		return $clean;
	}
	function save_options_notice() 
	{
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		add_settings_error( 'options-framework', 'save_options', __( '设置保存成功！', 'haoui' ), 'updated fade' );
	}
	function get_default_values() 
	{
		$output = array();
		$config = & Options_Framework::_optionsframework_options();
		foreach ( (array) $config as $option ) 
		{
			if ( ! isset( $option['id'] ) ) 
			{
				continue;
			}
			if ( ! isset( $option['std'] ) ) 
			{
				continue;
			}
			if ( ! isset( $option['type'] ) ) 
			{
				continue;
			}
			if ( has_filter( 'of_sanitize_' . $option['type'] ) ) 
			{
				$output[$option['id']] = apply_filters( 'of_sanitize_' . $option['type'], $option['std'], $option );
			}
		}
		return $output;
	}
	function optionsframework_admin_bar() 
	{
		$menu = $this->menu_settings();
		global $wp_admin_bar;
		if ( 'menu' == $menu['mode'] ) 
		{
			$href = admin_url( 'admin.php?page=' . $menu['menu_slug'] );
		}
		else 
		{
			$href = admin_url( 'themes.php?page=' . $menu['menu_slug'] );
		}
		$args = array( 'parent' => 'appearance', 'id' => 'of_theme_options', 'title' => $menu['menu_title'], 'href' => $href );
		$wp_admin_bar->add_menu( apply_filters( 'optionsframework_admin_bar', $args ) );
	}
	function option_panel_active()
	{
		if(isset($_POST['token']))
		{
			$token = trim($_POST['token']);
			$err = false;
			if($token=='')
			{
				$err = true;
				$err_token = '激活码不能为空';
			}
			if($err==false)
			{
				$result_body = json_decode($this->option_send_request($token));
				if($result_body)
				{
					if($result_body->code=='1')
					{
						update_option( "dux-secret-key", $token);
						update_option( "dux-last-time" , time());
						update_option( "qux-status", 'certified');
						$active = $result_body;
						echo '<meta http-equiv="refresh" content="0">';
					}
					else
					{
						$active = new stdClass();
						$active->code = 0;
						$active->msg = $result_body->msg;
					}
				}
				else
				{
					$active = new stdClass();
					$active->code = 10;
					$active->msg = '激活失败，请稍后再试！';
				}
			}
		}
		$dux_secret_key = get_option('dux-secret-key') ?>
       <div id="optionsframework-wrap" class="wrap">
		<h2>主题激活</h2>
	   <div id="optionsframework-metabox" class="metabox-holder">
		   <div id="optionsframework" class="postbox">
				<form  class="form-horizontal" id="wpcom-panel-form" method="post" action="">
                  <div class="form-horizontal" style="width:400px;margin:80px auto;">
                       <?php if (isset($active)) 
		{
			?><p class="col-xs-offset-3 col-xs-9" style="<?php echo ($active->code==1?'color:green;':'color:#F33A3A;');
			?>">
                       <?php echo $active->msg;
			?></p><?php }
		?>
                       <?php if($dux_secret_key && empty($active) )
		{
			?><p class="col-xs-offset-3 col-xs-9" style="color:#F33A3A">授权码失活，请重新输入！</p>
                       <?php }
		else
		{
		}
		?>
                       <div class="form-group">
                           <label for="token" class="col-xs-3 control-label">授权码</label>
                           <div class="col-xs-9">
                               <input type="text" name="token" class="form-control" id="token" value="<?php echo isset($token)?$token:'';
		?>" placeholder="请输入授权码激活主题" autocomplete="off">
                               <?php if(isset($err_token))
		{
			?><div class="j-msg" style="color:#F33A3A;font-size:12px;margin-top:3px;margin-left:3px;"><?php echo $err_token;
			?></div><?php }
		?>
                           </div>
                       </div>
                       <div class="form-group">
                           <label class="col-xs-3 control-label"></label>
                           <div class="col-xs-9">
                               <input type="submit" class="button button-primary" value="提 交">
                           </div>
                       </div>
                   </div>
				</form>
			</div> <!-- / #container -->
		</div>

		</div> <!-- / .wrap -->
       <script>(function($){$('.form-control').focus(function(){$(this).next('.j-msg').hide();});})(jQuery);</script>
   <?php
}
	function option_send_request($token)
	{
		$url = 'https://www.xxxxx.xxxx/oauth/check.php?url='.$_SERVER['HTTP_HOST'].'&key=1&authcode='.$token;
		$result = wp_remote_get($url);
		if(is_array($result))
		{
			return $result['body'];
		}
	}
}
