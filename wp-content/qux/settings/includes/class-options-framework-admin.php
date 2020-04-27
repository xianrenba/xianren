<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Options_Framework_Admin {

	/**
     * Page hook for the options screen
     *
     * @since 1.7.0
     * @type string
     */
    protected $options_screen = null;

    /**
     * Hook in the scripts and styles
     *
     * @since 1.7.0
     */
    public function init() {

		// Gets options to load
    	$options = & Options_Framework::_optionsframework_options();

		// Checks if options are available
    	if ( $options ) {

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'add_custom_options_page' ) );
			
			// Add the required scripts and styles
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			// Settings need to be registered after admin_init
			add_action( 'admin_init', array( $this, 'settings_init' ) );

			// Adds options menu to the admin bar
			add_action( 'wp_before_admin_bar_render', array( $this, 'optionsframework_admin_bar' ) );

		}

    }

	/**
     * Registers the settings
     *
     * @since 1.7.0
     */
    function settings_init() {

		// Get the option name
		$options_framework = new Options_Framework;
	    $name = $options_framework->get_option_name();

		// Registers the settings fields and callback
		register_setting( 'optionsframework', $name, array ( $this, 'validate_options' ) );

		// Displays notice after options save
		add_action( 'optionsframework_after_validate', array( $this, 'save_options_notice' ) );

    }

	/*
	 * Define menu options
	 *
	 * Examples usage:
	 *
	 * add_filter( 'optionsframework_menu', function( $menu ) {
	 *     $menu['page_title'] = 'The Options';
	 *	   $menu['menu_title'] = 'The Options';
	 *     return $menu;
	 * });
	 *
	 * @since 1.7.0
	 *
	 */
	
	static function menu_settings() {

		$menu = array(

			// Modes: submenu, menu
            'mode' => 'submenu',

            // Submenu default settings
            'page_title' => __( '主题设置', 'haoui' ),
			'menu_title' => __( '主题设置', 'haoui' ),
			'capability' => 'edit_theme_options',
			'menu_slug' => 'options-framework',
            'parent_slug' => 'themes.php',

            // Menu default settings
            'icon_url' => 'dashicons-admin-customizer',
            'position' => '61'

		);

		return apply_filters( 'optionsframework_menu', $menu );
	}

	/**
     * Add a subpage called "Theme Options" to the appearance menu.
     *
     * @since 1.7.0
     */
	function add_custom_options_page() {

		$menu = $this->menu_settings();

		// If you want a top level menu, see this Gist:
		// https://gist.github.com/devinsays/884d6abe92857a329d99

		// Code removed because it conflicts with .org theme check.
		$secret = get_option('dux-secret-key');
		$last = get_option('dux-last-time');
		$now = time();
		$recheck = true;
		if($secret && ( $now - $last ) > 1440){
			$result_body = json_decode($this->option_send_request($secret));
			if($result_body->code=='1'){
				$recheck = true;
				update_option("dux-last-time" ,time());
			}else{
				$recheck = false;
			}
			
		}
		    $this->options_screen = add_menu_page(
                $menu['page_title'],
                $menu['menu_title'],
                $menu['capability'],
                $menu['menu_slug'],
                array( $this, 'options_page' ),
                $menu['icon_url'],
                $menu['position']
            );



	}

	/**
     * Loads the required stylesheets
     *
     * @since 1.7.0
     */

	function enqueue_admin_styles( $hook ) {

		if ( $this->options_screen != $hook )
	        return;

		wp_enqueue_style( 'optionsframework', OPTIONS_FRAMEWORK_DIRECTORY . 'css/optionsframework.css', array(),  Options_Framework::VERSION );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
     * Loads the required javascript
     *
     * @since 1.7.0
     */
	function enqueue_admin_scripts( $hook ) {

		if ( $this->options_screen != $hook )
	        return;

		// Enqueue custom option panel JS
		wp_enqueue_script( 'options-custom', OPTIONS_FRAMEWORK_DIRECTORY . 'js/options-custom.js', array( 'jquery','wp-color-picker' ), Options_Framework::VERSION );

		// Inline scripts from options-interface.php
		add_action( 'admin_head', array( $this, 'of_admin_head' ) );
	}

	function of_admin_head() {
		// Hook to add custom scripts
		do_action( 'optionsframework_custom_scripts' );
	}

	/**
     * Builds out the options panel.
     *
	 * If we were using the Settings API as it was intended we would use
	 * do_settings_sections here.  But as we don't want the settings wrapped in a table,
	 * we'll call our own custom optionsframework_fields.  See options-interface.php
	 * for specifics on how each individual field is generated.
	 *
	 * Nonces are provided using the settings_fields()
	 *
     * @since 1.7.0
     */
	 function options_page() { ?>

		<div id="optionsframework-wrap" class="wrap">

		<?php $menu = $this->menu_settings(); ?>
		<h2><?php echo esc_html( $menu['page_title'] ); ?></h2>

	    <h2 class="nav-tab-wrapper">
	        <?php echo Options_Framework_Interface::optionsframework_tabs(); ?>
	    </h2>

	    <?php settings_errors( 'options-framework' ); ?>

	    <div id="optionsframework-metabox" class="metabox-holder">
		    <div id="optionsframework" class="postbox">
				<form action="options.php" method="post">
				<?php settings_fields( 'optionsframework' ); ?>
				<?php Options_Framework_Interface::optionsframework_fields(); /* Settings */ ?>
				<div id="optionsframework-submit">
					<input type="submit" class="button-primary" name="update" value="<?php echo __( '保存设置', 'haoui' ); ?>" />
					<input type="submit" class="reset-button button-secondary" name="reset" value="<?php echo __( '重置全部设置', 'haoui' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'textdomain' ) ); ?>' );" />
					<div class="clear"></div>
				</div>
				</form>
			</div> <!-- / #container -->
		</div>
		<?php do_action( 'optionsframework_after' ); ?>
		</div> <!-- / .wrap -->

	<?php
	}

	/**
	 * Validate Options.
	 *
	 * This runs after the submit/reset button has been clicked and
	 * validates the inputs.
	 *
	 * @uses $_POST['reset'] to restore default options
	 */
	function validate_options( $input ) {

		/*
		 * Restore Defaults.
		 *
		 * In the event that the user clicked the "Restore Defaults"
		 * button, the options defined in the theme's options.php
		 * file will be added to the option for the active theme.
		 */

		if ( isset( $_POST['reset'] ) ) {
			add_settings_error( 'options-framework', 'restore_defaults', __( '设置已重置！', 'haoui' ), 'updated fade' );
			return $this->get_default_values();
		}

		/*
		 * Update Settings
		 *
		 * This used to check for $_POST['update'], but has been updated
		 * to be compatible with the theme customizer introduced in WordPress 3.4
		 */

		$clean = array();
		$options = & Options_Framework::_optionsframework_options();
		foreach ( $options as $option ) {

			if ( ! isset( $option['id'] ) ) {
				continue;
			}

			if ( ! isset( $option['type'] ) ) {
				continue;
			}

			$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

			// Set checkbox to false if it wasn't sent in the $_POST
			if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
				$input[$id] = false;
			}

			// Set each item in the multicheck to false if it wasn't sent in the $_POST
			if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
				foreach ( $option['options'] as $key => $value ) {
					$input[$id][$key] = false;
				}
			}

			// For a value to be submitted to database it must pass through a sanitization filter
			if ( has_filter( 'of_sanitize_' . $option['type'] ) ) {
				$clean[$id] = apply_filters( 'of_sanitize_' . $option['type'], $input[$id], $option );
			}
		}

		// Hook to run after validation
		do_action( 'optionsframework_after_validate', $clean );

		return $clean;
	}

	/**
	 * Display message when options have been saved
	 */

	function save_options_notice() {
		add_settings_error( 'options-framework', 'save_options', __( '设置保存成功！', 'haoui' ), 'updated fade' );
	}

	/**
	 * Get the default values for all the theme options
	 *
	 * Get an array of all default values as set in
	 * options.php. The 'id','std' and 'type' keys need
	 * to be defined in the configuration array. In the
	 * event that these keys are not present the option
	 * will not be included in this function's output.
	 *
	 * @return array Re-keyed options configuration array.
	 *
	 */
	function get_default_values() {
		$output = array();
		$config = & Options_Framework::_optionsframework_options();
		foreach ( (array) $config as $option ) {
			if ( ! isset( $option['id'] ) ) {
				continue;
			}
			if ( ! isset( $option['std'] ) ) {
				continue;
			}
			if ( ! isset( $option['type'] ) ) {
				continue;
			}
			if ( has_filter( 'of_sanitize_' . $option['type'] ) ) {
				$output[$option['id']] = apply_filters( 'of_sanitize_' . $option['type'], $option['std'], $option );
			}
		}
		return $output;
	}

	/**
	 * Add options menu item to admin bar
	 */

	function optionsframework_admin_bar() {

		$menu = $this->menu_settings();

		global $wp_admin_bar;

		if ( 'menu' == $menu['mode'] ) {
			$href = admin_url( 'admin.php?page=' . $menu['menu_slug'] );
		} else {
			$href = admin_url( 'themes.php?page=' . $menu['menu_slug'] );
		}

		$args = array(
			'parent' => 'appearance',
			'id' => 'of_theme_options',
			'title' => $menu['menu_title'],
			'href' => $href
		);

		$wp_admin_bar->add_menu( apply_filters( 'optionsframework_admin_bar', $args ) );
	}
	
	
	function option_panel_active(){
        if(isset($_POST['token'])){
            $token = trim($_POST['token']);
            $err = false;
            if($token==''){
                $err = true;
                $err_token = '激活码不能为空';
            }/*else if(strlen($token)!=32){
                $err = true;
                $err_token = '激活码不正确';
            }*/
            if($err==false){
                $result_body = json_decode($this->option_send_request($token));
                if($result_body->code=='1'){
                	update_option( "dux-secret-key", $token );
                	update_option( "dux-last-time" , time());
                	$active = $result_body;
                    echo '<meta http-equiv="refresh" content="0">';
                }else if($result_body->code !='1'){
                	$active = new stdClass();
                    $active->code = 0;
                    $active->msg = '激活码错误或无效！';
                }else{
                    $active = new stdClass();
                    $active->code = 10;
                    $active->msg = '激活失败，请稍后再试！';
                }
            }
        }
        $dux_secret_key = get_option('dux-secret-key')
        ?>
        <div id="optionsframework-wrap" class="wrap">
		<h2>主题激活</h2>
	    <div id="optionsframework-metabox" class="metabox-holder">
		    <div id="optionsframework" class="postbox">
				<form  class="form-horizontal" id="wpcom-panel-form" method="post" action="">
                   <div class="form-horizontal" style="width:400px;margin:80px auto;">
                        <?php if (isset($active)) { ?><p class="col-xs-offset-3 col-xs-9" style="<?php echo ($active->code==1?'color:green;':'color:#F33A3A;');?>">
                        <?php echo $active->msg; ?></p><?php } ?>
                        <?php if($dux_secret_key && empty($active) ){ ?><p class="col-xs-offset-3 col-xs-9" style="color:#F33A3A">你使用了错误的授权，请重新输入！</p>
                        <?php }else{} ?>
                        <div class="form-group">
                            <label for="token" class="col-xs-3 control-label">授权码</label>
                            <div class="col-xs-9">
                                <input type="text" name="token" class="form-control" id="token" value="<?php echo isset($token)?$token:'';?>" placeholder="请输入授权码激活主题" autocomplete="off">
                                <?php if(isset($err_token)){ ?><div class="j-msg" style="color:#F33A3A;font-size:12px;margin-top:3px;margin-left:3px;"><?php echo $err_token;?></div><?php } ?>
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
    
    function option_send_request($token){
    	$url = 'https://www.qyblog.cn/oauth/check.php?url='.$_SERVER['HTTP_HOST'].'&key=1&authcode='.$token;
        if( function_exists('curl_init') ) { 
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$cache = curl_exec($ch);
			curl_close($ch);
		} else {
			$cache = file_get_contents($url); 
		}
		return $cache;
    }

} 
?>