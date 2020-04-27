<?php
    class ZRZ_ACTIVITY{
        public $name = '',//名称
               $slug = 'activity',//链接
               $pages = array();

        public function __construct(){
            $this->name = $this->set_name();
            $this->pages = $this->set_pages();

            //加载活动页面的 css 和 js
            add_action('wp_enqueue_scripts', array($this,'setp_js_and_css'));
        
            //新建文章形式
            add_action( 'init', array($this,'build_post_type'));

            //活动的固定链接
            add_filter('post_type_link', array($this,'custom_link'), 10, 3);

            //新建页面的名称
            add_filter( 'document_title_parts',array($this,'document_title') );

            //添加后台列表的显示项
            add_filter( 'manage_activity_posts_columns', array($this,'set_activity_columns') );
            add_action( 'manage_activity_posts_custom_column' , array($this,'activity_column'), 10, 4 );

            //添加后台 meta-box
            add_action('add_meta_boxes',array($this,'activity_metas_box_init'));
            add_action('save_post',array($this,'activity_metas_box_save'));

            //后台增加js
            add_action( 'admin_enqueue_scripts', array($this,'add_scripts'), 10, 1 );

            //前台活动发布
            add_action( 'admin_enqueue_scripts', array($this,'add_scripts'), 10, 1 );
            add_action( 'wp_ajax_zrz_public_activity', array($this,'zrz_public_activity'));
            add_action( 'wp_ajax_zrz_activity_submit', array($this,'zrz_activity_submit'));//报名

            //扫码支付
            add_action( 'wp_ajax_zrz_weixin_activity_pay', array($this,'zrz_weixin_activity_pay'));//报名扫码支付

            //余额支付
            add_action( 'wp_ajax_zrz_activity_with_balance', array($this,'zrz_activity_with_balance'));//余额支付活动报名费用

        }

        public function set_name(){
            return __('活动','ziranzhi2');
        }

        public function set_pages(){
            return array(
                array(
                   'slug'=>'up',
                   'name'=>__('活动预告','ziranzhi2')
                ),
                array(
                    'slug'=>'reg',
                    'name'=>__('报名中','ziranzhi2')
                ),
                array(
                    'slug'=>'endreg',
                    'name'=>__('报名结束','ziranzhi2')
                ),
                array(
                    'slug'=>'start',
                    'name'=>__('进行中','ziranzhi2')
                ),
                array(
                    'slug'=>'end',
                    'name'=>__('活动结束','ziranzhi2')
                ),
                array(
                    'slug'=>'public',
                    'name'=>__('发布','ziranzhi2')
                )
            );
        }

        public function custom_link($link, $post = 0){

            if ( $post->post_type == $this->slug ){
                return home_url( $this->slug.'/' . $post->ID .'.html' );
            }

            return $link;
            
        }

        //新建文章形式
        public function build_post_type(){
            $labels = array(
                'name'               => sprintf( '%1$s',$this->name),
                'singular_name'      => sprintf( '%1$s',$this->name),
                'add_new'            => sprintf( '新建一个%1$s',$this->name),
                'add_new_item'       => sprintf( '新建一个%1$s',$this->name),
                'edit_item'          => sprintf( '编辑%1$s',$this->name),
                'new_item'           => sprintf( '新%1$s',$this->name),
                'all_items'          => sprintf( '所有%1$s',$this->name),
                'view_item'          => sprintf( '查看%1$s',$this->name),
                'search_items'       => sprintf( '搜索%1$s',$this->name),
                'not_found'          => sprintf( '没有找到有关的%1$s',$this->name),
                'not_found_in_trash' => sprintf( '回收站里没有%1$s',$this->name),
                'parent_item_colon'  => '',
                'menu_name'          => sprintf( '%1$s',$this->name),
            );
            $args = array(
                'labels'        => $labels,
                'public'        => true,
                'menu_position' => 6,
                'supports'      => array( 'title', 'editor', 'author','thumbnail', 'excerpt', 'comments','custom-fields' ),
                'has_archive'   => true,
                'rewrite' => array( 'slug' => $this->slug ),
            );
            register_post_type( $this->slug, $args );

            add_rewrite_tag('%zrz_activity_page%','([^&]+)');
            
            add_rewrite_rule(
                $this->slug.'/([0-9]+)?.html$',
                'index.php?post_type='.$this->slug.'&p=$matches[1]',
                'top' );
            add_rewrite_rule(
                $this->slug.'/([0-9]+)?.html/comment-page-([0-9]{1,})$',
                'index.php?post_type='.$this->slug.'&p=$matches[1]&cpage=$matches[2]',
                'top');
            
            //新建活动页面
            foreach ($this->pages as $page) {
                $slug = $page['slug'];
                add_rewrite_rule(
                    $this->slug.'/'.$slug.'/page/([0-9]+)/?',
                    'index.php?post_type='.$this->slug.'&zrz_activity_page='.$slug.'&paged=$matches[1]',
                    'top' );
                add_rewrite_rule(
                    $this->slug.'/'.$slug.'/?',
                    'index.php?post_type='.$this->slug.'&zrz_activity_page='.$slug,
                    'top' );
            }
        }

        public function document_title($title_parts){
            $activity_page = get_query_var('zrz_activity_page');

            foreach ($this->pages as $page) {
                if($activity_page == $page['slug']){
                    $title_parts['title'] = $page['name'].$this->name;
                    break;
                }
            }
            
            return $title_parts;
        }

        //加载css和js
        public function setp_js_and_css(){

            if(is_post_type_archive ($this->slug) || is_singular($this->slug)){

                //页面宽度
                $page_width = '.content-in,.activity-swiper-info,.activity-public-thumbsrc{
                    width: '.zrz_get_theme_settings('page_width').'px!important;margin:0 auto!important;max-width: 100%;
                }';
                wp_add_inline_style( 'ziranzhi2-style', $page_width );
                if(get_query_var('zrz_activity_page') == 'public'){
                    wp_enqueue_script( 'quill-editor', ZRZ_THEME_URI.'/js/editor/quill.min.js' , array(), null , true );

                    //编辑器样式
                    wp_enqueue_style( 'ziranzhi2-editor-style', ZRZ_THEME_URI.'/js/editor/quill.snow.css' , array() , ZRZ_VERSION, 'all');
                }

                wp_enqueue_style( 'ziranzhi2-activity', ZRZ_THEME_URI.'/modules/activity/style.css' , array() , ZRZ_VERSION, 'all');
                wp_enqueue_script( 'ziranzhi2-avtivity-js', ZRZ_THEME_URI.'/modules/activity/activity.js' , array(), ZRZ_VERSION , true );
            }
        }

        //新增后台管理页面中的列
        public function set_activity_columns($columns) {

            $columns['zrz_activity_status'] = __( '活动状态', 'ziranzhi2' );
            $columns['zrz_activity_address'] = __( '活动地点', 'ziranzhi2' );
            $columns['zrz_activity_time'] = __( '活动时间', 'ziranzhi2' );
            $columns['zrz_activity_role'] = __( '参与权限', 'ziranzhi2' );
            $columns['zrz_activity_price'] = __( '活动价格或权限', 'ziranzhi2' );
            $columns['zrz_activity_people_count'] = __( '总共人数', 'ziranzhi2' );
            $columns['zrz_activity_applicants_count'] = __( '报名人数', 'ziranzhi2' );
        
            return $columns;
        }

        //获取活动状态
        public function get_activity_status($type,$post_id){
            $time = get_post_meta($post_id, 'zrz_activity_time', true);

            $c_time = current_time( 'mysql' );

            //开始报名时间
            $registration_time = isset($time['registration_time']) ? $time['registration_time'] : array();
            
            //结束报名时间
            $end_registration_time = isset($time['end_registration_time']) ? $time['end_registration_time'] : array();
            
            //活动开始时间
            $start_time = isset($time['start_time']) ? $time['start_time'] : array();
            //活动结束时间
            $end_time = isset($time['end_time']) ? $time['end_time'] : array();

            if(!$registration_time || !$end_registration_time || !$start_time || !$end_time) return;

            $data_arr = array();

            $data_arr['date_arr'] = array(
                'registration_time'=>array(
                    'year'=>trim($registration_time['year']),
                    'month'=>$this->set_two_words(trim($registration_time['month'])),
                    'day'=>$this->set_two_words(trim($registration_time['day'])),
                    'hh'=>$this->set_two_words(trim($registration_time['hh'])),
                    'mn'=>$this->set_two_words(trim($registration_time['mn'])),
                ),
                'end_registration_time'=>array(
                    'year'=>trim($end_registration_time['year']),
                    'month'=>$this->set_two_words(trim($end_registration_time['month'])),
                    'day'=>$this->set_two_words(trim($end_registration_time['day'])),
                    'hh'=>$this->set_two_words(trim($end_registration_time['hh'])),
                    'mn'=>$this->set_two_words(trim($end_registration_time['mn'])),
                ),
                'start_time'=>array(
                    'year'=>trim($start_time['year']),
                    'month'=>$this->set_two_words(trim($start_time['month'])),
                    'day'=>$this->set_two_words(trim($start_time['day'])),
                    'hh'=>$this->set_two_words(trim($start_time['hh'])),
                    'mn'=>$this->set_two_words(trim($start_time['mn'])),
                ),
                'end_time'=>array(
                    'year'=>trim($end_time['year']),
                    'month'=>$this->set_two_words(trim($end_time['month'])),
                    'day'=>$this->set_two_words(trim($end_time['day'])),
                    'hh'=>$this->set_two_words(trim($end_time['hh'])),
                    'mn'=>$this->set_two_words(trim($end_time['mn'])),
                )
            );

            $_time = $data_arr['date_arr'];
            $registration_time = $_time['registration_time'];
            $end_registration_time = $_time['end_registration_time'];
            $start_time = $_time['start_time'];
            $end_time = $_time['end_time'];

            $registration_time = $registration_time['year'].'-'.$registration_time['month'].'-'.$registration_time['day'].' '.$registration_time['hh'].':'.$registration_time['mn'].':'.'00';
            $end_registration_time = $end_registration_time['year'].'-'.$end_registration_time['month'].'-'.$end_registration_time['day'].' '.$end_registration_time['hh'].':'.$end_registration_time['mn'].':'.'00';
            $start_time = $start_time['year'].'-'.$start_time['month'].'-'.$start_time['day'].' '.$start_time['hh'].':'.$start_time['mn'].':'.'00';
            $end_time = $end_time['year'].'-'.$end_time['month'].'-'.$end_time['day'].' '.$end_time['hh'].':'.$end_time['mn'].':'.'00';

            if($type == 'zrz_activity_status'){
                if($c_time < $registration_time){
                    return '活动预告';
                }elseif($c_time >= $registration_time && $c_time < $end_registration_time){
                    return '报名中';
                }elseif($c_time >= $end_registration_time && $c_time < $start_time){
                    return '报名结束';
                }elseif($c_time >= $start_time && $c_time <=$end_time){
                    return '进行中';
                }elseif($c_time > $end_time){
                    return '活动结束';
                }
            }

            if($type == 'zrz_activity_time'){
                $data_arr['text'] = '<p>报名时间：<br>'.$registration_time.'~'.$end_registration_time.'</p>
                <p>活动时间：<br>'.$start_time.'~'.$end_time.'</p>';
                $data_arr['date_str'] = array(
                    'registration_time'=>$registration_time,
                    'end_registration_time'=>$end_registration_time,
                    'start_time'=>$start_time,
                    'end_time'=>$end_time
                );
                return $data_arr;
            }
        }

        //获取活动价格或者权限
        public function get_activity_role($type, $post_id){
            $role_and_price = get_post_meta($post_id, 'zrz_activity_role', true);

            $key = isset($role_and_price['key']) ? $role_and_price['key'] : '';
            $role = isset($role_and_price['role']) ? $role_and_price['role'] : array();
            $rmb = isset($role_and_price['rmb']) ? $role_and_price['rmb'] : 0;

            if($key){
                if($type == 'zrz_activity_role'){
                    return array(
                        'str'=>$key == 'credit' ? '积分报名' : ($key == 'rmb' ? '付费报名' : ($key == 'role' ? '允许报名的用户组' : ($key == 'free' ? '免费' : '未设置'))),
                        'key'=>$key,
                        'nub'=>0
                    );
                    
                }elseif($type == 'zrz_activity_price'){
                    if($key == 'credit'){
                        return array(
                            'str'=>$rmb.'积分',
                            'icon'=>zrz_coin(0,0,$rmb),
                            'nub'=>$rmb
                        );
                    }elseif($key == 'rmb'){
                        return array(
                            'str'=>$rmb.'元',
                            'icon'=>'<span class="coin l1 rmb"><i>¥</i>'.$rmb.'</span>',
                            'nub'=>$rmb
                        );
                    }elseif($key == 'role'){
                        $r = '';
                        $i = '';
                        $lv_setting = zrz_get_lv_settings();
                        foreach ($role as $val) {
                            if(!isset($lv_setting[$val])) continue;
                            $lv = $lv_setting[$val];
                            $r .= $lv['name'].'，';
                            $is_vip = strpos($val,'vip') !== false ? true : false;
                            $i .= '<span class="user-lv '.$val.'" title="'.($is_vip ? '' : str_replace('lv','LV.',$val)).' '.$lv['name'].'"><i class="zrz-icon-font-'.($is_vip ? 'vip' : $val).' iconfont"></i></span>';
                        }
                        return array(
                            'str'=>$r,
                            'icon'=>$i,
                            'nub'=>$role
                        );
                    }elseif($key == 'free'){
                        return array(
                            'str'=>'免费',
                            'icon'=>'<span class="free">免费</span>',
                            'nub'=>0
                        );
                    }
                }
            }
        }

        //新增后台管理页面中的列的值
        public function activity_column( $column, $post_id ) {
        
            if($column == 'zrz_activity_status'){
                echo $this->get_activity_status('zrz_activity_status', $post_id);
            }

            if($column == 'zrz_activity_time'){
                $time = $this->get_activity_status('zrz_activity_time', $post_id);
                echo isset($time['text']) ? $time['text'] : ''; 
            }

            if($column == 'zrz_activity_address'){
                echo get_post_meta($post_id, 'zrz_activity_address', true);
            }

            if($column == 'zrz_activity_role' || $column == 'zrz_activity_price'){
                $res = $this->get_activity_role($column, $post_id);
                echo $res['str'];
            }

            if($column == 'zrz_activity_people_count'){
                echo get_post_meta($post_id, 'zrz_activity_people_count', true).'人';
            }

            if($column == 'zrz_activity_applicants_count'){
                $count = get_post_meta($post_id, 'zrz_activity_applicants_count', true);
                echo $count ? $count : '0人';
            }
            
        }

        //增加meta-box
        public function activity_metas_box_init(){

            add_meta_box('activity_status-metas',__('活动状态','ziranzhi2'),array($this,'activity_status'),'activity','side','high');
        
            add_meta_box('activity_address-metas',__('活动地点','ziranzhi2'),array($this,'activity_address'),'activity','side','high');
        
            add_meta_box('activity_time-metas',__('活动时间','ziranzhi2'),array($this,'activity_time'),'activity','side','high');

            add_meta_box('activity_role-metas',__('参与权限','ziranzhi2'),array($this,'activity_role'),'activity','side','high');
        
            add_meta_box('activity_people_count-metas',__('总共人数','ziranzhi2'),array($this,'activity_people_count'),'activity','side','high');

            add_meta_box('activity_applicants_count-metas',__('报名人数','ziranzhi2'),array($this,'activity_applicants_count'),'activity','side','high');
        }
        
        public function activity_status($post){
            $status = $this->get_activity_status('zrz_activity_status', $post->ID);
            echo $status ?: '未发布';

        }

        public function activity_address($post){
            $obj = is_object($post);
            $id = $obj ? $post->ID : $post;

            $address = get_post_meta($id, 'zrz_activity_address', true);

            echo '<input ref="address" data-address="'.$address.'" type="text" '.($obj ? '' : 'v-model="address"').' class="regular-text" name="zrz_activity_address" value="'.$address.'">';
        }

        //月份选择器
        public function day_select($name,$current_day,$model = ''){
            $html ='<select name="'.$name.'" class="month" '.($model ? 'v-model="'.$model.'"' : 'id="mm"').'>';
            for ($i=1; $i <= 12 ; $i++) { 
                $html .= '<option value="'.$this->set_two_words($i).'" '.selected( $current_day, $this->set_two_words($i),false ).'>'.$i.'月</option>';
            }
            $html .= '</select>';
            return $html;
        }

        public function set_two_words($val){
            return sprintf("%02d", $val);
        }

        public function activity_time($post){
            $obj = is_object($post);
            $id = $obj ? $post->ID : $post;
            $time = $this->get_activity_status('zrz_activity_time',$id);
            $time = $time['date_arr'];

            $registration_time = $time['registration_time'];
            $end_registration_time = $time['end_registration_time'];
            $start_time = $time['start_time'];
            $end_time = $time['end_time'];

            $current_time = array(
                'year'=>current_time('Y'),
                'month'=>$this->set_two_words(current_time('m')),
                'day'=>$this->set_two_words(current_time('d')),
                'hh'=>$this->set_two_words(current_time('G')),
                'mn'=>$this->set_two_words(current_time('i'))
            );

            echo '<div id="timestampdiv">
            <p ref="reg" data-year="'.($registration_time['year'] ?: $current_time['year']).'" data-month="'.($registration_time['month'] ?: $current_time['month']).'" data-day="'.($registration_time['day'] ?: $current_time['day']).'"
             data-hh="'.($registration_time['hh'] ?: $current_time['hh']).'" data-mn="'.($registration_time['mn'] ?: $current_time['mn']).'"><span>报名时间：</span>
            <input '.($obj ? 'id="aa"' : 'v-model="time.registration_time.year"').' type="text" class="year" size="4" maxlength="4" name="zrz_activity_time[registration_time][year]" value="'.($registration_time['year'] ?: $current_time['year']).'">-
            '.$this->day_select('zrz_activity_time[registration_time][month]',($registration_time['month'] ?: $current_time['month']),(!$obj ? 'time.registration_time.month' : '')).'-
            <input '.($obj ? 'id="jj"' : 'v-model="time.registration_time.day"').' type="text" class="day" size="2" maxlength="2" name="zrz_activity_time[registration_time][day]" value="'.($registration_time['day'] ?: $current_time['day']).'">@
            <input '.($obj ? 'id="hh"' : 'v-model="time.registration_time.hh"').' type="text" class="hh" size="2" maxlength="2" name="zrz_activity_time[registration_time][hh]" value="'.($registration_time['hh'] ?: $current_time['hh']).'">:
            <input '.($obj ? 'id="mn"' : 'v-model="time.registration_time.mn"').' type="text" class="mn" size="2" maxlength="2" name="zrz_activity_time[registration_time][mn]" value="'.($registration_time['mn'] ?: $current_time['mn']).'">
            </p>
            <p ref="endreg" data-year="'.($end_registration_time['year'] ?: $current_time['year']).'" data-month="'.($end_registration_time['month'] ?: $current_time['month']).'" data-day="'.($end_registration_time['day'] ?: $current_time['day']).'"
            data-hh="'.($end_registration_time['hh'] ?: $current_time['hh']).'" data-mn="'.($end_registration_time['mn'] ?: $current_time['mn']).'"><span>报名结束时间：</span>
            <input '.($obj ? 'id="aa"' : 'v-model="time.end_registration_time.year"').' type="text" class="year" size="4" maxlength="4" name="zrz_activity_time[end_registration_time][year]" value="'.($end_registration_time['year'] ?: $current_time['year']).'">-
            '.$this->day_select('zrz_activity_time[end_registration_time][month]',($end_registration_time['month'] ?: $current_time['month']),(!$obj ? 'time.end_registration_time.month' : '')).'-
            <input '.($obj ? 'id="jj"' : 'v-model="time.end_registration_time.day"').' type="text" class="day" size="2" maxlength="2" name="zrz_activity_time[end_registration_time][day]" value="'.($end_registration_time['day'] ?: $current_time['day']).'">@
            <input '.($obj ? 'id="hh"' : 'v-model="time.end_registration_time.hh"').' type="text" class="hh" size="2" maxlength="2" name="zrz_activity_time[end_registration_time][hh]" value="'.($end_registration_time['hh'] ?: $current_time['hh']).'">:
            <input '.($obj ? 'id="mn"' : 'v-model="time.end_registration_time.mn"').' type="text" class="mn" size="2" maxlength="2" name="zrz_activity_time[end_registration_time][mn]" value="'.($end_registration_time['mn'] ?: $current_time['mn']).'">
            </p>
            <p ref="start" data-year="'.($start_time['year'] ?: $current_time['year']).'" data-month="'.($start_time['month'] ?: $current_time['month']).'" data-day="'.($start_time['day'] ?: $current_time['day']).'"
            data-hh="'.($start_time['hh'] ?: $current_time['hh']).'" data-mn="'.($start_time['mn'] ?: $current_time['mn']).'"><span>活动开始时间：</span>
            <input '.($obj ? 'id="aa"' : 'v-model="time.start_time.year"').' type="text" class="year" size="4" maxlength="4" name="zrz_activity_time[start_time][year]" value="'.($start_time['year'] ?: $current_time['year']).'">-
            '.$this->day_select('zrz_activity_time[start_time][month]',($start_time['month'] ?: $current_time['month']),(!$obj ? 'time.start_time.month' : '')).'-
            <input '.($obj ? 'id="jj"' : 'v-model="time.start_time.day"').' type="text" class="day" size="2" maxlength="2" name="zrz_activity_time[start_time][day]" value="'.($start_time['day'] ?: $current_time['day']).'">@
            <input '.($obj ? 'id="hh"' : 'v-model="time.start_time.hh"').' type="text" class="hh" size="2" maxlength="2" name="zrz_activity_time[start_time][hh]" value="'.($start_time['hh'] ?: $current_time['hh']).'">:
            <input '.($obj ? 'id="mn"' : 'v-model="time.start_time.mn"').' type="text" class="mn" size="2" maxlength="2" name="zrz_activity_time[start_time][mn]" value="'.($start_time['mn'] ?: $current_time['mn']).'">
            </p>
            <p ref="end" data-year="'.($end_time['year'] ?: $current_time['year']).'" data-month="'.($end_time['month'] ?: $current_time['month']).'" data-day="'.($end_time['day'] ?: $current_time['day']).'"
            data-hh="'.($end_time['hh'] ?: $current_time['hh']).'" data-mn="'.($end_time['mn'] ?: $current_time['mn']).'"><span>活动结束时间：</span>
            <input '.($obj ? 'id="aa"' : 'v-model="time.end_time.year"').' type="text" class="year" size="4" maxlength="4" name="zrz_activity_time[end_time][year]" value="'.($end_time['year'] ?: $current_time['year']).'">-
            '.$this->day_select('zrz_activity_time[end_time][month]',($end_time['month'] ?: $current_time['month']),(!$obj ? 'time.end_time.month' : '')).'-
            <input '.($obj ? 'id="jj"' : 'v-model="time.end_time.day"').' type="text" class="day" size="2" maxlength="2" name="zrz_activity_time[end_time][day]" value="'.($end_time['day'] ?: $current_time['day']).'">@
            <input '.($obj ? 'id="hh"' : 'v-model="time.end_time.hh"').' type="text" class="hh" size="2" maxlength="2" name="zrz_activity_time[end_time][hh]" value="'.($end_time['hh'] ?: $current_time['hh']).'">:
            <input '.($obj ? 'id="mn"' : 'v-model="time.end_time.mn"').' type="text" class="mn" size="2" maxlength="2" name="zrz_activity_time[end_time][mn]" value="'.($end_time['mn'] ?: $current_time['mn']).'">
            </p></div>';
        }

        public function activity_role($post){
            $obj = isset($post->ID);
            $id = $obj ? $post->ID : $post;

            $role = get_post_meta($id,'zrz_activity_role',true);
            $key = isset($role['key']) ? $role['key'] : 'free';
            $value = isset($role['role']) ? $role['role'] : array();
            $rmb = isset($role['rmb']) ? $role['rmb'] : '';

            //所有用户组
            $lv = zrz_get_lv_settings();

            $html ='<select name="zrz_activity_role[key]" id="activity-role-key" '.(!$obj ? 'v-model="key"' : '').' ref="key" data-key="'.$key.'">
                <option value="credit" '.selected($key, 'credit',false ).'>积分报名</option>
                <option value="rmb" '.selected($key, 'rmb',false ).'>付费报名</option>
                <option value="role" '.selected($key, 'role',false ).'>允许报名的用户组</option>
                <option value="free" '.selected($key, 'free',false ).'>免费报名</option>';
            
            $html .= '</select>';

            $html .= '<div id="activity-role-value" ref="rmb" data-rmb="'.$rmb.'">
                <p id="ac-price" v-if="key == \'credit\' || key == \'rmb\'"><input type="text" name="zrz_activity_role[rmb]" value="'.$rmb.'" '.(!$obj ? 'v-model="rmb"' : '').'>'.(!$obj ? '<span v-if="key == \'credit\'">积分</span><span v-else>元</span>' : '<span>元</span>').'</p>
                <p id="ac-role" v-if="key == \'role\'" v-cloak>';

            $_lv = '';
            foreach ($lv as $_key => $val) {
                if(isset($val['open']) && $val['open'] == 0) continue;
                $checked = '';
                if(in_array($_key,$value)){
                    $checked = 'checked';
                    $_lv .= $_key.',';
                }
                
                $html .= '<label><input type="checkbox" name="zrz_activity_role[role][]" '.$checked.' value="'.$_key.'" '.(!$obj ? 'v-model="lv"' : '').'>'.$val['name'].'</label>';
                }

            $html .= '</p><span ref="roleData" data-role="'.$_lv.'"></span></div>';
            echo $html;
            unset($html);
        }

         //增加后台js脚本
        public function add_scripts($hook){
            global $post;

            if ( $hook == 'post-new.php' || $hook == 'post.php') {
                if ( 'activity' === $post->post_type ) {     
                    wp_add_inline_script('jquery-migrate', '
                    jQuery(document).ready(function(){
                        function zrzActivity(){
                            var $ =jQuery.noConflict();
                            function activityChange(_key){
                                var role = $("#ac-role"),
                                    price = $("#ac-price");
                                if(_key == "role"){
                                    role.show();
                                    price.hide();
                                }else if(_key == "free"){
                                    role.hide();
                                    price.hide();
                                }else{
                                    role.hide();
                                    price.show();
                                    if(_key == "credit"){
                                        price.children("span").text("积分");
                                    }else{
                                        price.children("span").text("元");
                                    }
                                }
                            }
                            var _key = $("#activity-role-key").find(":selected").val();
                            activityChange(_key);
                            $("#activity-role-key").change(function(){ 
                                var key=$(this).children("option:selected").val();
                                activityChange(key);
                            }) 
                        }
                        zrzActivity();
                    })');

                    wp_add_inline_style('thickbox', '#timestampdiv span{display:block}#activity_address-metas input,#activity_people_count-metas input,#activity_applicants_count-metas input{width:100%}#activity-role-value input{width:50%}#ac-role input{width:auto}#activity-role-value label{display:block}');
                }
            }
        }

        public function activity_people_count($post){
            $obj = isset($post->ID);
            $id = $obj ? $post->ID : $post;
            $count = get_post_meta($id, 'zrz_activity_people_count', true);
            echo '<input ref="peopleCount" data-count="'.$count.'" type="text" class="regular-text" name="zrz_activity_people_count" value="'.$count.'" v-model="count">';
        }

        public function activity_applicants_count($post){
            $count = get_post_meta($post->ID, 'zrz_activity_applicants_count', true);
            echo '<input type="text" class="regular-text" name="zrz_activity_applicants_count" value="'.$count.'" style="max-width: 98%;">';
        }

        public function activity_metas_box_save($post_id){
            $post_type = get_post_type($post_id);
    
            if($post_type != 'activity') return;

            if(isset($_POST['zrz_activity_address'])){
                update_post_meta($post_id,'zrz_activity_address',$_POST['zrz_activity_address']);
            }
        
            if(isset($_POST['zrz_activity_time'])){
                update_post_meta($post_id,'zrz_activity_time',$_POST['zrz_activity_time']);

                $dates = $this->get_activity_status('zrz_activity_time',$post_id);
                $dates = $dates['date_str'];

                update_post_meta($post_id,'zrz_activity_registration_time',$dates['registration_time']);
                update_post_meta($post_id,'zrz_activity_end_registration_time',$dates['end_registration_time']);
                update_post_meta($post_id,'zrz_activity_start_time',$dates['start_time']);
                update_post_meta($post_id,'zrz_activity_end_time',$dates['end_time']);
            }
        
            if(isset($_POST['zrz_activity_role'])){
                update_post_meta($post_id,'zrz_activity_role',$_POST['zrz_activity_role']);
            }
        
            if(isset($_POST['zrz_activity_people_count'])){
                update_post_meta($post_id,'zrz_activity_people_count',$_POST['zrz_activity_people_count']);
            }

            if(isset($_POST['zrz_activity_applicants_count'])){
                update_post_meta($post_id,'zrz_activity_applicants_count',$_POST['zrz_activity_applicants_count']);
            }
        }

        public function check_role($post_id){
            $current_user = get_current_user_id();
            $author = get_post_field('post_author',$post_id);

            if(current_user_can('delete_users')) return true;

            if($post_id == 0 && zrz_current_user_can('activity')){
                return true;
            }

            if($current_user == $author && zrz_current_user_can('activity') && get_post_type($post_id) == 'activity') return true;

            return false;

        }

        //余额支付活动费用
        public function zrz_activity_with_balance(){
            $data = is_array($_POST['data']) ? $_POST['data'] : array();
            $post_id = isset($data['post_id']) ? $data['post_id'] : '';
            $name = isset($data['name']) ? $data['name'] : '';
            $number = isset($data['number']) ? $data['number'] : '';
            $sex = isset($data['sex']) ? $data['sex'] : '';
            $more = isset($data['more']) ? $data['more'] : '';
            
            if(!$post_id || !$name || !$number || !$sex){
                $msg .='<p>您提交的信息不全</p>'; 
            }
        
            if(get_post_type($post_id) != 'activity'){
                $msg .='<p>参数错误</p>'; 
            }

            $user_id = get_current_user_id();
            $c_rmb = get_user_meta($user_id,'zrz_rmb',true);

            $toatl_price = get_post_meta($post_id,'zrz_activity_role',true);
            $toatl_price = $toatl_price['key'] == 'rmb' ? $toatl_price['rmb'] : 0;

            if($c_rmb < $toatl_price){
                print json_encode(array('status'=>401,'msg'=>__('余额不足，请先充值','ziranzhi2')));
                exit;
            }

            //先清除支付信息
            delete_user_meta($user_id,'zrz_ds_resout');
            update_user_meta($user_id,'zrz_ds_resout',$data);
        
            $order_id = 'pay_activity-'.$post_id.'-'.str_shuffle(uniqid()).'-'.$user_id;
        
            $title = '活动付费';

            //生成一个临时订单
            $c_order_id = zrz_build_order_no();
            $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id);
            $resout = $ordre->add_data();

            $resout = zrz_notify_data_update($c_order_id,$toatl_price);

            if($resout){
                update_user_meta($user_id,'zrz_rmb',$c_rmb - $toatl_price);
                print json_encode(array('status'=>200,'msg'=>__('支付成功','ziranzhi2')));
                exit;
            }

            print json_encode(array('status'=>401,'msg'=>__('支付失败','ziranzhi2')));
            exit;
        }

        //微信支付活动费用
        public function zrz_weixin_activity_pay(){
            $data = is_array($_POST['data']) ? $_POST['data'] : array();
            $post_id = isset($data['post_id']) ? $data['post_id'] : '';
            $name = isset($data['name']) ? $data['name'] : '';
            $number = isset($data['number']) ? $data['number'] : '';
            $sex = isset($data['sex']) ? $data['sex'] : '';
            $more = isset($data['more']) ? $data['more'] : '';
            $current_url = isset($_POST['current_url']) ? esc_url($_POST['current_url']) : '';
           $js_type = isset($_POST['type']) ? esc_attr($_POST['type']) : '';
            if(!$post_id || !$name || !$number || !$sex){
                $msg .='<p>您提交的信息不全</p>'; 
            }
        
            if(get_post_type($post_id) != 'activity'){
                $msg .='<p>参数错误</p>'; 
            }

            $user_id = get_current_user_id();

            $toatl_price = get_post_meta($post_id,'zrz_activity_role',true);
            $toatl_price = $toatl_price['key'] == 'rmb' ? $toatl_price['rmb'] : 0;
            
            //先清除支付信息
            delete_user_meta($user_id,'zrz_ds_resout');
            update_user_meta($user_id,'zrz_ds_resout',$data);
        
            $order_id = 'pay_activity-'.$post_id.'-'.str_shuffle(uniqid()).'-'.$user_id;
        
            $title = '活动付费';

            //生成一个临时订单
            $c_order_id = zrz_build_order_no();
            $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id);
            $resout = $ordre->add_data();

            $resout = zrz_back_qcode($c_order_id,$toatl_price*100,$title,$current_url,$js_type);
            if($resout){
                print json_encode(array('status'=>200,'msg'=>$resout,'user_id'=>$user_id));
                exit;
            }

            print json_encode(array('status'=>401,'msg'=>__('获取二维码失败','ziranzhi2')));
            exit;
        }

        //积分报名
        public function zrz_acitivity_credit($user_id,$post_id,$credit,$data){

            if(get_post_type($post_id) != 'activity') return false;
 
            //给活动发起人通知和增加积分
            $post_author = get_post_field('post_author',$post_id);
            $init = new Zrz_Credit_Message($post_author,44);
            $add_msg = $init->add_message($user_id, $credit,$post_id,'');

            if($add_msg){

                //记录活动用户
                $users = get_post_meta($post_id,'zrz_activity_users',true);
                $users = is_array($users) ? $users : array();
                $users[$user_id] = array(
                    'name'=>$data['name'],
                    'number'=>$data['number'],
                    'sex'=>$data['sex'],
                    'more'=>$data['more'],
                    'time'=>current_time( 'mysql' )
                );

                update_post_meta($post_id,'zrz_activity_users',$users);

                //报名人数加1
                $count = get_post_meta($post_id, 'zrz_activity_applicants_count', true);
                $count = is_numeric($count) ? $count : 0;
                update_post_meta($post_id, 'zrz_activity_applicants_count', $count+1);

                $arg = array(
                    'post_id'=>$post_id,
                    'user_id'=>$user_id
                );

                apply_filters( 'zrz_activity_success_credit', $arg );

                return true;
            }

           return false;
        }

        //免费或者权限报名
        public function zrz_activity_free($user_id,$post_id,$role,$data){

            if($role['key'] == 'role'){
                //获取当前用户的权限
                $current_role = get_user_meta($user_id,'zrz_lv',true);
                if(!in_array($current_role,$role['role'])) return false;
            }

            //给发起人通知
            $post_author = get_post_field('post_author',$post_id);
            $init = new Zrz_Credit_Message($post_author,44);
            $add_msg = $init->add_message($user_id, 'empty',$post_id,'');

            if($add_msg){

                //增加用户数组
                $users = get_post_meta($post_id,'zrz_activity_users',true);
                $users = is_array($users) ? $users : array();
                $users[$user_id] = array(
                    'name'=>$data['name'],
                    'number'=>$data['number'],
                    'sex'=>$data['sex'],
                    'more'=>$data['more'],
                    'time'=>current_time( 'mysql' )
                );

                update_post_meta($post_id,'zrz_activity_users',$users);

                //报名人数加1
                $count = get_post_meta($post_id, 'zrz_activity_applicants_count', true);
                $count = is_numeric($count) ? $count : 0;
                update_post_meta($post_id, 'zrz_activity_applicants_count', $count+1);

                $arg = array(
                    'post_id'=>$post_id,
                    'user_id'=>$user_id
                );

                apply_filters( 'zrz_activity_success_free', $arg );
            
                return true;
            }

            return false;
        }

        //提交报名
        public function zrz_activity_submit(){
            $post_id = isset($_POST['post_id']) ? wp_strip_all_tags($_POST['post_id']) : '';
            $data = array(
                'name'=>isset($_POST['name']) ? wp_strip_all_tags($_POST['name']) : '',
                'number'=>isset($_POST['number']) ? (int)$_POST['number'] : '',
                'sex'=>isset($_POST['sex']) ? (int)$_POST['sex'] : '',
                'more'=>isset($_POST['more']) ? wp_strip_all_tags($_POST['more']) : '',
            );

            if(!$post_id) exit;

            $user_id = (int)get_current_user_id();
            $post_author = get_post_field('post_author',$post_id);
            
            // if($user_id == $post_author){
            //     print json_encode(array('status'=>401,'msg'=>__('不能报名自己的活动','ziranzhi2')));
            //     exit;
            // }

            //检查用户是否已经报名
            $users = get_post_meta($post_id,'zrz_activity_users',true);
            $users = is_array($users) ? $users : array();

            if(isset($users[$user_id])){
                print json_encode(array('status'=>401,'msg'=>__('您已经报过名了','ziranzhi2')));
		        exit;
            }

            $role = get_post_meta($post_id,'zrz_activity_role',true);
            
            if($role['key'] == 'rmb'){
                print json_encode(array('status'=>300));
                exit;
            }elseif($role['key'] == 'credit'){
                //检查积分是否足够
                $credit_total = (int)get_user_meta($user_id,'zrz_credit_total',true);
                $credit = $role['rmb'];
                if($credit_total >= $credit){
                    if($this->zrz_acitivity_credit($user_id,$post_id,$role['rmb'],$data)){
                        //给报名的人减掉积分
                        $init = new Zrz_Credit_Message($user_id,45);
                        
                        if($init->add_message($post_author, -$credit,$post_id,'')){
                            print json_encode(array('status'=>200,'msg'=>__('报名成功','ziranzhi2')));
                            exit;
                        }
                    }
                }
                
            }elseif($role['key'] == 'free' || $role['key'] == 'role'){
                if($this->zrz_activity_free($user_id,$post_id,$role,$data)){
                    print json_encode(array('status'=>200,'msg'=>__('报名成功','ziranzhi2')));
                    exit;
                }
            }

            print json_encode(array('status'=>401));
		    exit;
        }

        public function zrz_public_activity(){
            $time = isset($_POST['time']) ? $_POST['time'] : 0;
            $count = isset($_POST['count']) ? (int)$_POST['count'] : 0;
            $title = isset($_POST['title']) ? wp_strip_all_tags($_POST['title']) : '';
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $key = isset($_POST['key']) ? wp_strip_all_tags($_POST['key']) : '';
            $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : null;
            $status = isset($_POST['status']) ? 'pending' : 'draft';
            $rmb = isset($_POST['rmb']) ? (int)$_POST['rmb'] : 0;
            $lv = isset($_POST['lv']) ? $_POST['lv'] : array();
            $address = isset($_POST['address']) ? wp_strip_all_tags($_POST['address']) : array();
            $thumb = isset($_POST['thumb']) ? (int)$_POST['thumb'] : 0;

            if(!$count){
                print json_encode(array('status'=>401,'msg'=>__('请输入参与人数','ziranzhi2')));
		        exit;
            }

            if(!$title){
                print json_encode(array('status'=>401,'msg'=>__('请输入标题','ziranzhi2')));
		        exit;
            }

            if(!$content){
                print json_encode(array('status'=>401,'msg'=>__('请输入活动内容','ziranzhi2')));
		        exit;
            }

            if(!$content){
                print json_encode(array('status'=>401,'msg'=>__('请输入活动内容','ziranzhi2')));
		        exit;
            }

            $current_id = get_current_user_id();
            $author = $current_id;

            //检查当前用户是否由权限发布
            if(!zrz_current_user_can('activity')){
                print json_encode(array('status'=>401,'msg'=>__('您没有权限这么做','ziranzhi2')));
		        exit;
            }

            if($post_id){

                $author = get_post_field('post_author',$post_id);

                //检查权限
                if(!$this->check_role($post_id)){
                    print json_encode(array('status'=>401,'msg'=>__('您没有权限这么做','ziranzhi2')));
		            exit;
                }

                if(get_post_status($post_id) == 'publish'){
                    $post_date = get_post_time( 'Y-m-d H:i:s', false , $post_id);
                    $post_date_gmt = get_post_time( 'Y-m-d H:i:s', true , $post_id);
                }else{
                    $post_date = current_time( 'Y-m-d H:i:s', false );
                    $post_date_gmt = current_time( 'Y-m-d H:i:s', true);
                }
            }

            if(current_user_can('delete_users')){
                $status = 'publish';
            }

            //提交
            $arg = array(
                'ID'=> $post_id,
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => $status,
                'post_type'=>$this->slug,
                'post_author' => $author,
                'post_date'      => $post_date,
                'post_date_gmt'  => $post_date_gmt,
                'comment_status'=>'open'
            );

            $_post_id = wp_insert_post( $arg );
            
            if($_post_id){
                //设置缩略图
                if($thumb){
                    set_post_thumbnail($_post_id, $thumb);
                }
                
                $files = isset($_POST['filesArg']) ? $_POST['filesArg'] : array();
                if(!empty($files)){
                    foreach ($files as $file) {
                        wp_update_post(
                            array(
                                'ID' => $file, 
                                'post_parent' => $_post_id
                            )
                        );
                    }
                }

                //更新时间
                update_post_meta($_post_id,'zrz_activity_time',$time);

                $dates = $this->get_activity_status('zrz_activity_time',$_post_id);
                $dates = $dates['date_str'];

                update_post_meta($_post_id,'zrz_activity_registration_time',$dates['registration_time']);
                update_post_meta($_post_id,'zrz_activity_end_registration_time',$dates['end_registration_time']);
                update_post_meta($_post_id,'zrz_activity_start_time',$dates['start_time']);
                update_post_meta($_post_id,'zrz_activity_end_time',$dates['end_time']);

                //更新当前用户数量
                update_post_meta($_post_id,'zrz_activity_people_count',$count);

                //更新权限
                $arg = array(
                    'key'=>$key,
                    'rmb'=>$rmb,
                    'role'=>$lv
                );

                update_post_meta($_post_id,'zrz_activity_role',$arg);

                //更新地址
                update_post_meta($_post_id,'zrz_activity_address',$address);

                $link = get_permalink($_post_id);
            }else{
                print json_encode(array('status'=>401,'msg'=>__('发布失败','ziranzhi2')));
		        exit;
            }

            print json_encode(array('status'=>200,'msg'=>__('发布成功','ziranzhi2'),'link'=>$link));
		    exit;
        }

    }
    new ZRZ_ACTIVITY();