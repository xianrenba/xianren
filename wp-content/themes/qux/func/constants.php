<?php

/* 定义全局常量 */
/* Path */
defined( 'THEME_TPL' ) || define('THEME_TPL', get_template_directory() . '/template');

defined( 'THEME_URI' ) || define( 'THEME_URI', get_stylesheet_directory_uri());

defined( 'UM_URI' ) || define( 'UM_URI', get_stylesheet_directory_uri());

defined( 'THEME_DIR' ) || define( 'THEME_DIR', get_template_directory());

defined( 'UM_DIR' ) || define('UM_DIR', get_template_directory());

/* Some Endpoints */
$site_endpoints = json_encode(array(
    'oauth_qq'                  =>  'oauth/qq',
    'oauth_weibo'               =>  'oauth/weibo',
    'oauth_weixin'              =>  'oauth/weixin',
    'oauth_qq_last'             =>  'oauth/qq/last',
    'oauth_weibo_last'          =>  'oauth/weibo/last',
    'oauth_weixin_last'         =>  'oauth/weixin/last',
    'xhpay'                     =>  'site/xhpay',
    'payjs'                     =>  'site/payjs',
    'codepay'                   =>  'site/codepay',   
    'alipay'                    =>  'site/alipay',
    'wxpay'                     =>  'site/wxpay',
    'mqpay'                     =>  'site/mqpay',
    'qrpay'                     =>  'site/qrpay',
    'mqpaynotify'               =>  'site/mqpaynotify',
    'captcha'                   =>  'site/captcha',
    'qr'                        =>  'site/qrcode',
    'signin'                    =>  'm/signin',
    'signup'                    =>  'm/signup',
    'activate'                  =>  'm/activate',
    'signout'                   =>  'm/signout',
    'findpass'                  =>  'm/findpass',
    'resetpass'                 =>  'm/resetpass',
    'manage_home'               =>  'management', //302 to status
    'manage_status'             =>  'management/status', // 全站数据统计 用户文章评论订单等数据量 运营时间等
    'manage_users'              =>  'management/users/all',
    'manage_admins'             =>  'management/users/administrator',
    'manage_editors'            =>  'management/users/editor',
    'manage_authors'            =>  'management/users/author',
    'manage_contributors'       =>  'management/users/contributor',
    'manage_subscribers'        =>  'management/users/subscriber',
    'manage_posts'              =>  'management/posts',
    'manage_comments'           =>  'management/comments',
    'manage_orders'             =>  'management/orders/all',
    'manage_cash_orders'        =>  'management/orders/cash',
    'manage_credit_orders'      =>  'management/orders/credit',
    'manage_completed_orders'   =>  'management/orders/completed',
    'manage_coupons'            =>  'management/coupons',
	'manage_cards'              =>  'management/cards',
    'manage_members'            =>  'management/members',
    'manage_products'           =>  'management/products',
    'user'                      =>  'u/user'
    // 'edit_post'                 =>  'management/editpost',
    // TODO: Add more
));
defined('SITE_ROUTES') || define('SITE_ROUTES', $site_endpoints);

$user_roites = json_encode(array('user' => 'user'));
defined('USER_ROUTES') || define('USER_ROUTES', $user_roites);

/* Allowed PayMent Routes */
$site_allow_routes = json_encode(array(
	'alipay' => 'alipay',
	'mqpay' => 'mqpay',
	'mqpaynotify' => 'mqpay.notify',
	'wxpay' => 'wxpay',
	'xhpay' => 'xhpay',
	'payjs' => 'payjs',
	'codepay' => 'codepay',
	'qrpay'  => 'qrpay',
	'download' => 'download',
	'captcha' => 'captcha',
	'qrcode'  => 'qrcode'
));
defined('ALLOWED_SITE_ROUTES') || define('ALLOWED_SITE_ROUTES', $site_allow_routes);

/* Allowed Management Routes */
$manage_allow_routes = json_encode(array(
    'status' => 'status',
    'posts' => 'posts',
    'comments' => 'comments',
    'editpost' => 'editpost',
    'users' => array(
        'all',
        'administrator',
        'editor',
        'author',
        'contributor',
        'subscriber'
    ),
    //'user' => 'user',
    'orders' => array(
        'all',
        'credit',
        'cash',
        'completed'
    ),
    //'order' => 'order',
    'coupons' => 'coupons',
	'cards' => 'cards',
    'members' => 'members',
    'products' => 'products'
));
defined('ALLOWED_MANAGE_ROUTES') || define('ALLOWED_MANAGE_ROUTES', $manage_allow_routes);

/* Allowed Action */
$m_allow_actions = json_encode(array(
    'signin' => 'Signin',
    'signup' => 'Signup',
    'activate' => 'Activate',
    'signout' => 'Signout',
    'refresh' => 'Refresh',
    'findpass' => 'Findpass',
    'resetpass' => 'Resetpass'
));
defined('ALLOWED_M_ACTIONS') || define('ALLOWED_M_ACTIONS', $m_allow_actions);

/* Allowed Oauth Types */
$oauth_allow_types = json_encode(array('qq', 'weibo', 'weixin'));  // TODO: more e.g github..
defined('ALLOWED_OAUTH_TYPES') || define('ALLOWED_OAUTH_TYPES', $oauth_allow_types);