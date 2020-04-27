<?php
/*
    侧边栏小工具缓存插件
	Version: 0.5.2
	GitHub URI: https://github.com/kasparsd/widget-output-cache
	Author: Kaspars Dambis
	Author URI: http://kaspars.net
*/

WidgetOutputCache::instance();


class WidgetOutputCache {

	// Store IDs of widgets to exclude from cache
	private $excluded_ids = array();


	protected function __construct() {

		// Overwrite widget callback to cache the output
		add_filter( 'widget_display_callback', array( $this, 'widget_callback' ), 10, 3 );

		// Cache invalidation for widgets
		add_filter( 'widget_update_callback', array( $this, 'cache_bump' ) );

		// Allow widgets to be excluded from the cache
		add_action( 'in_widget_form', array( $this, 'widget_controls' ), 10, 3 );

		// Load widget cache exclude settings
		add_action( 'init', array( $this, 'init' ), 10 );

		// Save widget cache settings
		add_action( 'sidebar_admin_setup', array( $this, 'save_widget_controls' ) );

	}


	public static function instance() {

		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;

	}


	function init() {

		$this->excluded_ids = (array) get_option( 'cache-widgets-excluded', array() );

	}


	function widget_callback( $instance, $widget_object, $args ) {

		// Don't return the widget
		if ( false === $instance || ! is_subclass_of( $widget_object, 'WP_Widget' ) )
			return $instance;

		if ( in_array( $widget_object->id, $this->excluded_ids ) )
			return $instance;

		$timer_start = microtime(true);

		$cache_key = sprintf(
				'cwdgt-%s',
				md5( $widget_object->id . get_option( 'cache-widgets-version', 1 ) )
			);

		$cached_widget = get_transient( $cache_key );

		if ( empty( $cached_widget ) ) {

			ob_start();
				$widget_object->widget( $args, $instance );
				$cached_widget = ob_get_contents();
			ob_end_clean();

			set_transient(
				$cache_key,
				$cached_widget,
				apply_filters( 'widget_output_cache_ttl', 60 * 12, $args )
			);

			printf(
				'%s <!-- Stored in widget cache in %s seconds (%s) -->',
				$cached_widget,
				round( microtime(true) - $timer_start, 4 ),
				$cache_key
			);

		} else {

			printf(
				'%s <!-- From widget cache in %s seconds (%s) -->',
				$cached_widget,
				round( microtime(true) - $timer_start, 4 ),
				$cache_key
			);

		}

		// We already echoed the widget, so return false
		return false;

	}


	function cache_bump( $instance ) {

		update_option( 'cache-widgets-version', time() );

		return $instance;

	}


	function widget_controls( $object, $return, $instance ) {

		$is_excluded = in_array( $object->id, $this->excluded_ids );

		printf(
			'<p>
				<label>
					<input type="checkbox" name="widget-cache-exclude" value="%s" %s />
					%s
				</label>
			</p>',
			esc_attr( $object->id ),
			checked( $is_excluded, true, false ),
			esc_html__( '不缓存这个小工具', 'ziranzhi2' )
		);

	}


	function save_widget_controls() {

		// current_user_can( 'edit_theme_options' ) is already being checked in widgets.php
		if ( empty( $_POST ) || ! isset( $_POST['widget-id'] ) )
			return;

		$widget_id = $_POST['widget-id'];
		$is_excluded = isset( $_POST['widget-cache-exclude'] );

		if ( ! isset($_POST['delete_widget']) && $is_excluded ) {

			// Wiget is being saved and it is being excluded too
			$this->excluded_ids[] = $widget_id;

		} elseif ( in_array( $widget_id, $this->excluded_ids ) ) {

			// Widget is being removed, remove it from exclusions too
			$exclude_pos_key = array_search( $widget_id, $this->excluded_ids );
			unset( $this->excluded_ids[ $exclude_pos_key ] );

		}

		$this->excluded_ids = array_unique( $this->excluded_ids );

		update_option( 'cache-widgets-excluded', $this->excluded_ids );

	}
}

//缓存自定义菜单
add_filter( 'pre_wp_nav_menu', 'zrz_get_nav_menu_cache', 10, 2 );
function zrz_get_nav_menu_cache( $nav_menu, $args ) {
    $cache_key      = zrz_get_nav_menu_cache_key($args);
    $cached_menu    = get_transient( $cache_key );
    if ( ! empty( $cached_menu ) )
        return $cached_menu;

    return $nav_menu;
}

add_filter( 'wp_nav_menu', 'zrz_set_nav_menu_cache', 10, 2 );
function zrz_set_nav_menu_cache( $nav_menu, $args ) {
    $cache_key      = zrz_get_nav_menu_cache_key($args);
    set_transient( $cache_key, $nav_menu, 86400 );

    return $nav_menu;
}

function zrz_get_nav_menu_cache_key($args){
    $timestamp = get_transient('nav-menu-cache-timestamp');
    if($timestamp === false){
        $timestamp = time();
        set_transient( 'nav-menu-cache-timestamp', $timestamp, 86400 );
    }
    return apply_filters( 'nav_menu_cache_key' , 'nav-menu-' . md5( serialize( $args ).serialize(get_queried_object()) ) . $timestamp );
}

// 更新菜单，清理缓存
add_action( 'wp_update_nav_menu', 'zrz_delete_nav_menu_cache' );
function zrz_delete_nav_menu_cache( $menu_id, $menu_data){
    set_transient( 'nav-menu-cache-timestamp', time(), 86400 );
}
