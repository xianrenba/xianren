<?php 
class 垇暡緺觎业
{
	function __construct()
	{
		$_var_0 = array('wp' => 'clear_rec_setup_schedule', 'wp_ajax_zrz_shop_order_check' => '惽屗氌記弪慈涷', 'wp_ajax_zrz_media_upload' => 'zrz_media_upload', 'archive_template' => '炨虪螠賭裎闈呅', 'single_template' => '炨虪螠賭裎闈呅', 'zrz_get_letter_avatar' => '으라呲阈咟褷', 'zrz_check_theme' => '虪螠賭裎');
		foreach ($_var_0 as $_var_1 => $_var_2) {
			if ($_var_1 == 'zrz_get_letter_avatar' || $_var_1 == 'zrz_check_theme') {
				add_filter($_var_1, array(__CLASS__, $_var_2));
			} else {
				add_action($_var_1, array(__CLASS__, $_var_2));
			}
		}
	}
	public static final function 虪螠賭裎()
	{
		return self::氌記弪();
	}
	public static final function 으라呲阈咟褷($_var_3)
	{
		/* if (!self::氌記弪()) {
			return;
		} */
		return $_var_3;
	}
	/*
    'admin_init' => '轹臧桃珒单銍墦',
	public static final function 轹臧桃珒单銍墦()
	{
		if (is_admin()) {
			$_var_4 = self::氌記弪();
			if (isset($_POST['zrz_theme_id'])) {
				update_option('zrz_theme_id', $_POST['zrz_theme_id']);
				self::呲阈咟褷();
			}
			if (!$_var_4) {
				$_var_5 = array('zrz_credit_signup', 'theme_style_select', 'aliyun_access_key_secret', 'auto_avatar', 'open_weixin_secret', 'video_size', 'ajax_comment', 'weixin_gz_appid', 'comment_vote_up_deduct_open', 'vip_allow', 'shop_show_d', 'open');
				foreach ($_var_5 as $_var_6) {
					if (isset($_POST[$_var_6])) {
						die('主题未激活！请购买后激活 <a href="https://7b2.com/themes">柒比贰</a>');
					}
				}
			}
		}
	} */
	public static final function 炨虪螠賭裎闈呅($_var_7)
	{
		global $post;
		$_var_8 = 'activity';
		if (isset($post->post_type) && $post->post_type == $_var_8 || is_post_type_archive($_var_8)) {
			if (!self::氌記弪()) {
				return;
			}
			if (isset($post->post_type) && $post->post_type == $_var_8) {
				$_var_9 = '/modules/activity/single.php';
				$_var_7 = ZRZ_THEME_DIR . $_var_9;
			}
			if (is_post_type_archive($_var_8)) {
				$_var_9 = '/modules/activity/home.php';
				$_var_7 = ZRZ_THEME_DIR . $_var_9;
			}
			$_var_10 = get_query_var('zrz_activity_page');
			if ($_var_10 == 'public') {
				$_var_9 = '/modules/activity/public.php';
				$_var_7 = ZRZ_THEME_DIR . $_var_9;
			}
		}
		return $_var_7;
	}
	/* 'zrz_check_user_daily' => '呲阈咟褷',
	public static final function 呲阈咟褷()
	{
		$_var_11 = self::馅幺惾湩嗱茷戱();
		if ($_var_11 == 'localhost') {
			return;
		}
		$_var_12 = get_option('zrz_theme_id');
		$_var_13 = 'https://7b2.com/domain-check';
		$_var_14 = wp_remote_post($_var_13, array('method' => 'POST', 'timeout' => 300, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'decompress' => false, 'headers' => array('Accept-Encoding' => ''), 'sslverify' => false, 'body' => array('check' => $_var_11, 'id' => $_var_12), 'cookies' => array()));
		$_var_15 = 'dismissed_wp_pointers_admin';
		if (isset($_var_14['body']) && $_var_14['body'] != 0) {
			update_option($_var_15, $_var_14['body']);
		} else {
			update_option($_var_15, 0);
		}
	} */
	/* public static final function 馅幺惾湩嗱茷戱($_var_16 = '')
	{
		$_var_17 = new TLDExtract();
		if ($_var_16) {
			$_var_18 = $_var_17->extract($_var_16);
		} else {
			$_var_18 = $_var_17->extract($_SERVER['HTTP_HOST']);
		}
		$_var_19 = $_var_18->domain . $_var_18->tld;
		if ($_var_19 == 'localhost' || ip2long($_var_19)) {
			return 'localhost';
		}
		return $_var_18->domain . '.' . $_var_18->tld;
	} */
	public static final function clear_rec_setup_schedule()
	{
		if (!wp_next_scheduled('zrz_check_user_daily')) {
			wp_schedule_event(time(), 'daily', 'zrz_check_user_daily');
		}
	}
	/* 'admin_notices' => '惽屗氌ぽんごにほん',
	public static final function 惽屗氌ぽんごにほん()
	{
		if (!self::氌記弪()) {
			echo '<span style="color:red;font-weight:700;margin-top:20px;display:block">主题需要激活，请 <a href="' . home_url('/wp-admin/admin.php?page=zrz_options') . '">激活</a>（未激活主题无法正常使用）</span>';
		} elseif (self::馅幺惾湩嗱茷戱() == 'localhost') {
			echo '<span style="color:green;font-weight:700">当前为测试环境。</span>';
		}
	} */
	public static final function 氌記弪()
	{
		//$_var_20 = 'dismissed_wp_pointers_admin';
		return true;
	}
	public static final function 惽屗氌記弪慈涷()
	{
		if (!self::氌記弪()) {
			return '';
		}
		$_var_21 = get_current_user_id();
		$_var_22 = get_user_meta($_var_21, 'zrz_orders', true);
		if (!$_var_22 || !is_array($_var_22)) {
			print json_encode(array('status' => 401, 'msg' => __('支付失败', 'ziranzhi2')));
			die;
		}
		$_var_23 = $_var_22['ids'];
		$_var_24 = $_var_22['payed'];
		print json_encode(array('status' => 200, 'msg' => $_var_23, 'payed' => $_var_24));
		die;
	}
	public static final function zrz_media_upload()
	{
		if (!self::氌記弪()) {
			return '';
		}
		$_var_25 = isset($_POST['type']) ? esc_attr($_POST['type']) : '';
		$_var_26 = isset($_POST['user_id']) ? esc_attr($_POST['user_id']) : '';
		$_var_27 = isset($_POST['cover_y']) ? esc_attr($_POST['cover_y']) : '';
		check_ajax_referer($_var_26, 'security');
		if (!(defined('DOING_AJAX') && DOING_AJAX) || $_var_26 != get_current_user_id() && !current_user_can('edit_users')) {
			print json_encode(array('status' => 401, 'msg' => __('非法操作', 'ziranzhi2')));
			die;
		}
		$_var_28 = new zrz_media($_FILES['file'], $_var_25, $_var_26, '', $_var_27);
		$_var_29 = $_var_28->media_upload();
		if ($_var_29) {
			print $_var_29;
			die;
		}
	}
}
new 垇暡緺觎业();
class TLDExtract
{
	const SCHEME_RE = '#^([a-zA-Z][a-zA-Z0-9+\\-.]*:)?//#';
	private $fetch;
	private $cacheFile;
	private $extractor = null;
	public function __construct($_var_30 = true, $_var_31 = '')
	{
		$this->fetch = $_var_30;
		$this->cacheFile = !empty($_var_31) ? $_var_31 : dirname(__FILE__) . DIRECTORY_SEPARATOR . '.tld_set';
	}
	public function __invoke($_var_32)
	{
		return $this->extract($_var_32);
	}
	public function extract($_var_33)
	{
		$_var_34 = $this->getHost($_var_33);
		$_var_35 = $this->getTldExtractor();
		list($_var_36, $_var_37) = $_var_35->extract($_var_34);
		if (empty($_var_37) && $this->isIp($_var_34)) {
			return new TLDExtractResult('', $_var_34, '');
		}
		$_var_38 = strrpos($_var_36, '.');
		if ($_var_38 !== false) {
			$_var_39 = substr($_var_36, 0, $_var_38);
			$_var_40 = substr($_var_36, $_var_38 + 1);
		} else {
			$_var_39 = '';
			$_var_40 = $_var_36;
		}
		return new TLDExtractResult($_var_39, $_var_40, $_var_37);
	}
	private function getHost($_var_41)
	{
		$_var_42 = preg_replace(self::SCHEME_RE, '', $_var_41);
		list($_var_42, ) = explode('/', $_var_42, 2);
		$_var_43 = explode('@', $_var_42, 2);
		if (count($_var_43) == 2) {
			$_var_42 = $_var_43[1];
		}
		$_var_44 = strrpos($_var_42, ']');
		if ($this->startsWith($_var_42, '[') && $_var_44) {
			$_var_42 = substr($_var_42, 0, $_var_44 + 1);
		} else {
			list($_var_42, ) = explode(':', $_var_42);
		}
		return $_var_42;
	}
	private function getTldExtractor()
	{
		if ($this->extractor !== null) {
			return $this->extractor;
		}
		$_var_45 = @file_get_contents($this->cacheFile);
		if (!empty($_var_45)) {
			$this->extractor = new PublicSuffixListTLDExtractor(unserialize($_var_45));
			return $this->extractor;
		}
		$_var_46 = array();
		if ($this->fetch) {
			$_var_46 = $this->fetchTldList();
		}
		if (empty($_var_46)) {
			$_var_47 = dirname(__FILE__) . DIRECTORY_SEPARATOR . '.tld_set_snapshot';
			$_var_45 = @file_get_contents($_var_47);
			if (!empty($_var_45)) {
				$this->extractor = new PublicSuffixListTLDExtractor(unserialize($_var_45));
				return $this->extractor;
			}
		} else {
			@file_put_contents($this->cacheFile, serialize($_var_46));
		}
		$this->extractor = new PublicSuffixListTLDExtractor($_var_46);
		return $this->extractor;
	}
	private function fetchTldList()
	{
		$_var_48 = $this->fetchPage('http://mxr.mozilla.org/mozilla-central/source/netwerk/dns/effective_tld_names.dat?raw=1');
		$_var_49 = array();
		if (!empty($_var_48) && preg_match_all('@^(?P<tld>[.*!]*\\w[\\S]*)@um', $_var_48, $_var_50)) {
			$_var_49 = array_fill_keys($_var_50['tld'], true);
		}
		return $_var_49;
	}
	private function fetchPage($_var_51)
	{
		if (ini_get('allow_url_fopen')) {
			return @file_get_contents($_var_51);
		} else {
			if (is_callable('curl_exec')) {
				$_var_52 = curl_init($_var_51);
				curl_setopt_array($_var_52, array('CURLOPT_RETURNTRANSFER' => true, 'CURLOPT_HEADER' => false, 'CURLOPT_FAILONERROR' => true));
				$_var_53 = curl_exec($_var_52);
				curl_close($_var_52);
				return $_var_53;
			}
		}
		return '';
	}
	private function isIp($_var_54)
	{
		if ($this->startsWith($_var_54, '[') && $this->endsWith($_var_54, ']')) {
			$_var_54 = substr($_var_54, 1, -1);
		}
		return (bool) filter_var($_var_54, FILTER_VALIDATE_IP);
	}
	private function startsWith($_var_55, $_var_56)
	{
		$_var_57 = strlen($_var_56);
		return substr($_var_55, 0, $_var_57) === $_var_56;
	}
	private function endsWith($_var_58, $_var_59)
	{
		$_var_60 = strlen($_var_59);
		if ($_var_60 == 0) {
			return true;
		}
		return substr($_var_58, -$_var_60) === $_var_59;
	}
}
class TLDExtractResult implements ArrayAccess
{
	private $fields;
	public function __construct($_var_61, $_var_62, $_var_63)
	{
		$this->fields = array('subdomain' => $_var_61, 'domain' => $_var_62, 'tld' => $_var_63);
	}
	public function __get($_var_64)
	{
		if (array_key_exists($_var_64, $this->fields)) {
			return $this->fields[$_var_64];
		}
		throw new OutOfRangeException(sprintf('Unknown field "%s"', $_var_64));
	}
	public function __isset($_var_65)
	{
		return array_key_exists($_var_65, $this->fields);
	}
	public function __set($_var_66, $_var_67)
	{
		throw new LogicException('Can\'t modify an immutable object.');
	}
	public function __toString()
	{
		return sprintf('%s(subdomain=\'%s\', domain=\'%s\', tld=\'%s\')', __CLASS__, $this->subdomain, $this->domain, $this->tld);
	}
	public function offsetExists($_var_68)
	{
		return array_key_exists($_var_68, $this->fields);
	}
	public function offsetGet($_var_69)
	{
		return $this->__get($_var_69);
	}
	public function offsetSet($_var_70, $_var_71)
	{
		throw new LogicException(sprintf('Can\'t modify an immutable object. You tried to set "%s".', $_var_70));
	}
	public function offsetUnset($_var_72)
	{
		throw new LogicException(sprintf('Can\'t modify an immutable object. You tried to unset "%s".', $_var_72));
	}
	public function toArray()
	{
		return $this->fields;
	}
}
class PublicSuffixListTLDExtractor
{
	private $tlds;
	public function __construct($_var_73)
	{
		$this->tlds = $_var_73;
	}
	public function extract($_var_74)
	{
		$_var_75 = explode('.', $_var_74);
		for ($_var_76 = 0; $_var_76 < count($_var_75); $_var_76++) {
			$_var_77 = join('.', array_slice($_var_75, $_var_76));
			$_var_78 = '!' . $_var_77;
			if (array_key_exists($_var_78, $this->tlds)) {
				return array(join('.', array_slice($_var_75, 0, $_var_76 + 1)), join('.', array_slice($_var_75, $_var_76 + 1)));
			}
			$_var_79 = '*.' . join('.', array_slice($_var_75, $_var_76 + 1));
			if (array_key_exists($_var_79, $this->tlds) || array_key_exists($_var_77, $this->tlds)) {
				return array(join('.', array_slice($_var_75, 0, $_var_76)), $_var_77);
			}
		}
		return array($_var_74, '');
	}
}