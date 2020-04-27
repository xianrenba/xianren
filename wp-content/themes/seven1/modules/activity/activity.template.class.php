<?php
class RZR_ACTIVITY_TEMPLATE{
        public $name = '活动',//名称
               $slug = 'activity';//链接

        public function __construct(){
            add_action( 'seven_activity_home_before', array($this,'swiper'), 0 );//幻灯
            add_action( 'seven_activity_home_list_before', array($this,'activity_list_before'), 0 );//活动筛选
            add_action( 'seven_activity_home_list', array($this,'activity_list'), 0 );//活动列表
            add_action( 'seven_activity_home_sidebar', array($this,'sidebar'), 0 );//活动侧边栏
            add_action( 'seven_activity_single_top', array($this,'single_top'), 0 );//活动内页顶部
            add_action( 'seven_activity_single_content', array($this,'single_content'), 0 );//活动内页content
            add_filter( 'pre_get_posts', array($this,'get_posts') );//筛选
            add_filter( 'zrz_display_links_filter', array($this,'public_activity_button') );//增加一个发布项
        }

        //幻灯
        public function swiper(){
            $show = zrz_get_display_settings('activity');
            
            $setting = apply_filters('seven_activity_swiper_setting',array(
                'item' => array(
                    array(
                        'id'=>32209,
                        'thumb'=>'',
                    ),
                    array(
                        'id'=>32189,
                        'thumb'=>'',
                    ),
                    array(
                        'id'=>32188,
                        'thumb'=>'',
                    ),
                    array(
                        'id'=>32205,
                        'thumb'=>'',
                    ),
                    array(
                        'id'=>32217,
                        'thumb'=>'',
                    ),
                ),
                'show_background'=>true,
            ));

            $arg = isset($show['swiper_arg']) && $show['swiper_arg'] != '' ? $show['swiper_arg'] : '';

            if(isset($show['swiper_show']) && $show['swiper_show'] == 0 || !$arg) {
                echo '<div class="h20"></div>';
                return;
            }

            $row = explode(PHP_EOL, $arg );

            $swiper_arg = array();

    		if($row && is_array($row) && !empty($row)){
    			foreach ($row as $val) {
    				$val = DeleteHtml($val);
    				$list = explode( "|", $val );
    				$swiper_arg[] = array(
                        'id'=>$list[0],
                        'thumb'=>$list[1] 
                    );
    			}
    		}

            $dot = '';
            $img = '';
            $title = '';
            $background = '';

            $i = 0;
            foreach ($swiper_arg as $item) {
                $id = $item['id'];
                $author = get_post_field('post_author',$id);
                if($item['thumb'] != '0'){
                    $thumb = $item['thumb'];
                }else{
                    $thumb = zrz_get_post_thumb($id);
                }
                $background .= ' <div ref="background" :class="[\'img-bg\',\'blur-dark\',\'pos-a\',{\'picked\':picked == '.$i.'}]" '.($setting['show_background'] ? 'style="background-image:url('.zrz_get_thumb($thumb,80,40).')' : '').'"></div>';
                $img .= '<div class="activity-swiper-img" style="background-image:url('.zrz_get_thumb($thumb,ceil(zrz_get_theme_settings('page_width')*0.75),500).')"><a href="'.get_permalink($id).'" class="link-block"></a></div>';
                $dot .= '<li ref="dot" @mouseenter="select('.$i.')" :class="[\'mouh\',{\'picked\':picked == '.$i.'}]"><img src="'.zrz_get_thumb($thumb,100,100).'" /></li>';
                $i ++;
            }

            $html = '
                <div id="activity-swiper" class="pos-r">
                    <div class="activity-swiper-in pos-r">
                        '.$background.'
                        <div class="activity-swiper-info">
                            <div class="activity-swiper-insert">
                                <div class="activity-swiper" ref="swiper">
                                    '.$img.'
                                </div>
                                <ul class="activity-swiper-dot">'.$dot.'</ul>
                            </div>
                        </div>
                    </div>
            </div>';

            echo $html;
            unset($html);
        }

        public function public_activity_button($arg){
            $arg[] = array(
                'show'=>zrz_current_user_can('activity') ? true : false,
                'link'=>home_url('/'.$this->slug.'/public'),
                'text'=>sprintf( '发起%1$s',$this->name),
                'icon'=>'<i class="iconfont zrz-icon-font-huodong"></i>'
            );
            return $arg;
        }

        public function activity_list_before(){
            $activity_page = get_query_var('zrz_activity_page');

            $link = home_url('/'.$this->slug);
            $html = '<div class="fs14 activity-filter pd20 pos-r"><div class="activity-filter-link"><a 
            class="fd '.(!$activity_page ? 'current' : '').'" href="'.$link.'"><span>全部</span></a><a 
            class="fd '.($activity_page == 'up' ? 'current' : '').'" href="'.$link.'/up"><span>活动预告</span></a><a 
            class="fd '.($activity_page == 'reg' ? 'current' : '').'" href="'.$link.'/reg"><span>报名中</span></a><a 
            class="fd '.($activity_page == 'endreg' ? 'current' : '').'" href="'.$link.'/endreg"><span>报名结束</span></a><a 
            class="fd '.($activity_page == 'start' ? 'current' : '').'" href="'.$link.'/start"><span>进行中</span></a><a 
            class="fd '.($activity_page == 'end' ? 'current' : '').'" href="'.$link.'/end"><span>活动结束</span></a></div>
            <a href="'.home_url('/activity/public').'" class="button public-activity">发布活动</a></div>';

            echo $html;
            unset($html);
        }

        public function get_activity_meta($id,$type){
            $activity = new ZRZ_ACTIVITY();
            $time = $activity->get_activity_status('zrz_activity_time',$id);
            $time = $time['date_str'];

            //地址
            if($type == 'address'){
                return get_post_meta($id,'zrz_activity_address',true);
            }
            if($type == 'time'){
                return $time;
            }
            if($type == 'applicants_count'){
                $count = get_post_meta($id, 'zrz_activity_applicants_count', true);
                return $count ? $count : 0;
            }
            if($type == 'people_count'){
                $count = get_post_meta($id, 'zrz_activity_people_count', true);
                return $count ? $count : 0;
            }

            if($type == 'status'){
                $activity_status = $activity->get_activity_status('zrz_activity_status',$id);
                switch ($activity_status) {
                    case '活动预告':
                        return '<span class="green status">活动预告</span>';
                        break;
                    case '报名中':
                        return '<span class="red status">报名中</span>';
                        break;
                    case '报名结束':
                        return '<span class="pink status">报名结束</span>';
                        break;
                    case '进行中':
                        return '<span class="blue status">进行中</span>';
                        break;
                    case '活动结束':
                        return '<span class="gray status">活动结束</span>';
                        break;
                }
            }

            if($type == 'status_text'){
                return $activity->get_activity_status('zrz_activity_status',$id);
            }

            if($type == 'role'){
                $role = $activity->get_activity_role('zrz_activity_role',$id);
                return $role['key'];
            }

            if($type == 'nub'){
                $role = $activity->get_activity_role('zrz_activity_price',$id);
                return $role['nub'];
            }

            if($type == 'price'){
                $price_type = $activity->get_activity_role('zrz_activity_price',$id);
                if(is_array($price_type)){
                    return $price_type['icon'];
                }
                return $price_type;
            }
        }

        public function activity_list(){
            $author = get_the_author_meta( 'ID' );
            $thumb = zrz_get_post_thumb(get_the_id());
            $id = get_the_id();
            
            $activity_status = $this->get_activity_meta($id,'status');
            $time = $this->get_activity_meta($id,'time');
            $people_count = $this->get_activity_meta($id,'people_count');
            $applicants_count = $this->get_activity_meta($id,'applicants_count');
            $address = $this->get_activity_meta($id,'address');
           

            $html = '<div class="activity-content pd20">
                <div class="activity-meta mar20-b">
                    '.get_avatar($author,'50').'
                    <div class="activity-meta-right">
                        '.zrz_get_user_page_link($author).'
                        <span class="activity-time"><i class="zrz-icon-font-shijian iconfont"></i>'.$time['start_time'].'</span>
                        <span class="activity-people"><i class="zrz-icon-font-renshu iconfont"></i>'.$people_count.'</span>
                        <span class="activity-applicants-count"><i class="zrz-icon-font-baomingrenyuan- iconfont"></i>'.$applicants_count.'</span>
                        <span class="activity-address"><i class="zrz-icon-font-dizhi iconfont"></i>'.$address.'</span>
                        '.$activity_status.'
                    </div>
                </div>
                <div class="activity-entry pos-r">
                    <a href="'.get_permalink().'" class="link-block"></a>
                    <img class="pos-a activity-thumb" src="'.zrz_get_thumb($thumb,460,'full').'" />
                    <div class="activity-title">
                        <h2><a href="'.get_permalink().'">'.get_the_title().'</a></h2>
                        <p>'.zrz_get_content_ex().'</p>
                    </div>
                </div>
            </div>';
            echo $html;
            unset($html);
        }

        //侧边栏
        public function sidebar(){
            echo '';
        }

        //内页顶部
        public function single_top(){
            $id = get_the_id();
            $thumb = zrz_get_post_thumb($id,true);
            $html = '<div class="activity-single-top pos-r"><div class="activity-single-top-bg pos-a img-bg blur-dark" style="background-image:url('.zrz_get_thumb($thumb,100,'full').');"></div>';
            $html .= $thumb ? '<img class="pos-r" src="'.zrz_get_thumb($thumb,ceil(zrz_get_theme_settings('page_width')*0.74751),'full').'" />' : '';
            $html .= '</div>';
            echo $html;
            unset($html);
        }

        //检查用户是否有权限报名
        public function check_user_role($id){
            $current_id = get_current_user_id();
            $credit = get_user_meta($current_id,'zrz_credit_total',true);
            $rmb = get_user_meta($current_id,'zrz_rmb',true);
            $current_role = get_user_meta($current_id,'zrz_lv',true);

            //检查权限
            $role = $this->get_activity_meta($id,'role');
            $nub = $this->get_activity_meta($id,'nub');

            switch ($role) {
                case 'credit':
                    return $credit >= $nub ? true : false;
                    break;
                case 'role':
                    if(in_array($current_role,$nub)) return true;
                    return false;
                    break;
                default:
                    return true;
                    break;
            }
        }

        //活动分页
        public function get_posts($query){
            $activity_page = get_query_var('zrz_activity_page');

            $c_time = current_time( 'mysql' );
            
            if($activity_page == 'up'){
                $arg = array(
                    array(
                        'key'     => 'zrz_activity_registration_time',
                        'value'   => $c_time,
                        'compare' => '>',
                        'type'    => 'DATETIME'
                    ),
                );
            }elseif($activity_page == 'reg'){
                $arg = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'zrz_activity_registration_time',
                        'value'   => $c_time,
                        'compare' => '<=',
                        'type'    => 'DATETIME'
                    ),
                    array(
                        'key'     => 'zrz_activity_end_registration_time',
                        'value'   => $c_time,
                        'compare' => '>=',
                        'type'    => 'DATETIME'
                    )
                );
            }elseif($activity_page == 'endreg'){
                $arg = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'zrz_activity_end_registration_time',
                        'value'   => $c_time,
                        'compare' => '<',
                        'type'    => 'DATETIME'
                    ),
                    array(
                        'key'     => 'zrz_activity_start_time',
                        'value'   => $c_time,
                        'compare' => '>',
                        'type'    => 'DATETIME'
                    )
                );
            }elseif($activity_page == 'start'){
                $arg = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'zrz_activity_start_time',
                        'value'   => $c_time,
                        'compare' => '<=',
                        'type'    => 'DATETIME'
                    ),
                    array(
                        'key'     => 'zrz_activity_end_time',
                        'value'   => $c_time,
                        'compare' => '>',
                        'type'    => 'DATETIME'
                    )
                );
            }elseif($activity_page == 'end'){
                $arg = array(
                    array(
                        'key'     => 'zrz_activity_end_time',
                        'value'   => $c_time,
                        'compare' => '<=',
                        'type'    => 'DATETIME'
                    ),
                );
            }else{
                return $query;
            }
            
            if( ! is_admin() && $query->is_main_query() && $activity_page) {
                $query->set( 'meta_query', $arg);
            }

            return $query;
        }

        //内页内容
        public function single_content(){
            $activity = new ZRZ_ACTIVITY();
            $id = get_the_id();
            $author = get_the_author_meta( 'ID' );
            
            $activity_status = $this->get_activity_meta($id,'status');
            $time = $this->get_activity_meta($id,'time');
            $people_count = $this->get_activity_meta($id,'people_count');
            $applicants_count = $this->get_activity_meta($id,'applicants_count');
            $address = $this->get_activity_meta($id,'address');
            $price = $this->get_activity_meta($id,'price');
            $role_set = $this->get_activity_meta($id,'role');
            $role = $this->check_user_role($id);
            $status_text = $this->get_activity_meta($id,'status_text');
            $status = get_post_status($id);

            //权限检查
            $can_edit = $activity->check_role($id);

            $users = get_post_meta($id,'zrz_activity_users',true);
            $users = is_array($users) ? $users : array();

            $users_list = $user_avatar_list = '';

            $price_data = get_post_meta($id,'zrz_activity_role',true);
            $current_id = get_current_user_id();
            $user_info = get_userdata($current_id);

            wp_localize_script( 'ziranzhi2-avtivity-js', 'activity_single', array(
                'post_id'=>$id,
                'key'=>isset($price_data['key']) ? $price_data['key'] : '',
                'rmb'=>isset($price_data['rmb']) ? $price_data['rmb'] : '',
                'role'=>isset($price_data['role']) ? $price_data['role'] : '',
                'number'=>isset($user_info->user_login) && zrz_isMobile($user_info->user_login) ? $user_info->user_login : '',
                'priceText'=>$price
            ));

            //检查是否已经报名
            $isset = isset($users[$current_id]) ? true : false;

            if(!empty($users)){
                $users_list = '<div class="activity-table pd20">
                <div class="fs12 mar5-b clearfix activity-show">截止到目前，已报名人数为'.count($users).'<span class="fr" @click="showTableText = !showTableText" v-text="showTableText ? \'隐藏\' : \'展开\'"></span></div>
                <table cellpadding="5" cellspacing="0" border="0" width="100%" class="b-r b-b l1 fs13" v-show="showTableText" v-cloak>
                <tbody>
                    <tr class="tab-head">
                        <td class="ac-user">网站注册信息</td> 
                        <td class="ac-name">姓名</td> 
                        <td class="ac-number">电话</td> 
                        <td class="ac-sex">性别</td> 
                        <td class="ac-time">报名时间</td> 
                        <td class="ac-more">备注</td>
                    </tr>';
                $user_avatar_list = '<div class="activity-avatar-list"><p>已报名：</p><ul class="clearfix">';
                foreach ($users as $key => $val) {
                    $users_list .= '<tr>
                        <td class="ac-user">'.zrz_get_user_page_link($key).'<span class="gray">('.$key.')</span></td> 
                        <td class="ac-name">'.$val['name'].'</td> 
                        <td class="ac-number">'.$val['number'].'</td> 
                        <td class="ac-sex">'.($val['sex'] == 1 ? '男' : '女').'</td> 
                        <td class="ac-time">'.$val['time'].'</td> 
                        <td class="ac-more">'.$val['more'].'</td>
                    </tr>';
                    $user_avatar_list .= '<li><a href="'.zrz_get_user_page_url($key).'">'.get_avatar($key,50,50).'</a></li>';
                }
                $users_list .= '</tbody></table></div>';
                $user_avatar_list .= '</ul></div>';
            }
            
            $button = '<button class="" @click="showForm()">立刻报名</button>';

            if($status_text != '报名中'){
                $button = '<button class="disabled">'.$status_text.'</button>';
            }else{
                switch ($role_set) {
                    case 'credit':
                        $button = $role ? $button : '<button class="disabled">积分不足</button>';
                        break;
                    case 'role':
                        $button = $role ? $button : '<button class="disabled">权限不足</button>';
                        break;
                    default:
                        break;
                }
            }

            if($isset){
                $button = '<button class="success-activity">恭喜，您已报名成功</button>';
            }

            $html = '<header class="activity-single-header">
                <h1>'.get_the_title().($can_edit ? '<a class="fs12 red mar20-l" href="'.home_url('/activity/public?id='.$id).'">编辑活动</a>' : '').'</h1>
                <div class="activity-single-meta pos-r">
                    '.get_avatar($author,50,50).'
                    <div class="activity-info">
                        <p><i class="zrz-icon-font-shijian iconfont"></i><b>报名时间：</b><span>'.$time['registration_time'].'<i> 至 </i>'.$time['end_registration_time'].'</span></p>
                        <p><i class="zrz-icon-font-shijian iconfont red"></i><b>活动时间：</b><span>'.$time['start_time'].'<i> 至 </i>'.$time['end_time'].'</span></p>
                        <p><i class="zrz-icon-font-dizhi iconfont"></i><b>活动地点：</b>'.$address.'</p>
                        <p><i class="zrz-icon-font-renshu iconfont"></i><b>计划人数：</b>'.$people_count.'人</p>
                        <p><i class="zrz-icon-font-baomingrenyuan- iconfont"></i><b>报名人数：</b>'.$applicants_count.'人</p>
                        <p><i class="zrz-icon-font-qizhi iconfont"></i><b>活动发起：</b>'.zrz_get_user_page_link($author).'</p>
                    </div>
                </div>
                '.($status != 'publish' ? '<div class="t-r red">活动尚未发布，等待修改或审核。</div>' : '').'
                <div class="clearfix pd20 bg-blue-light" ref="bmInfo">
                    <div class="fl"><p>'.$price.'</p></div>
                    <div class="fr">'.$button.'</div>
                </div>
                '.$user_avatar_list.'
                '.($author == $current_id || current_user_can('delete_users') ? $users_list : '').'
                <div :class="[\'dialog\', \'pay-form\',{\'dialog--open\':show}]"  v-cloak>
                    <div class="dialog__overlay"></div>
                    <div class="dialog__content">
                        <div class="pay-title pd10 pos-r clearfix b-b">
                            <div class="fl"><span v-html="userData.avatar"></span><span v-text="userData.name"></span></div>
                            <div class="fr"><span v-html="\'活动报名\'"></span></div>
                            <span class="pos-a close mouh" @click.stop="closeForm"><i class="iconfont zrz-icon-font-icon-x"></i></span>
                        </div>
                        <div class="pd20">
                            <div class="price-text t-c">
                                <div v-html="priceText"></div>
                            </div>
                            <div class="bm-data mar20-b">
                                <label><span>姓名：</span><input placeholder="请填写您的真实姓名" type="text" class="block" v-model="bmdata.name" @focus="focusRest()"/></label>
                                <label><span>电话：</span><input placeholder="请填写您的真实联系方式" type="text" class="block" v-model="bmdata.number" @focus="focusRest()"/></label>
                                <p class="bmredio"><span>性别：</span><label><input type="radio" value="1" class="radio" v-model="bmdata.sex">男</label><label><input type="radio" value="0" class="radio mar10-l" v-model="bmdata.sex">女</label></p>
                                <label><span>备注：</span><textarea class="block bm-more" v-model="bmdata.more"></textarea></label>
                            </div>
                            <button class="w100 pd10" v-text="buttonText" @click="submit()"></button>
                            <div class="red fs12 mar10-t" v-text="error"></div>
                        </div>
                    </div>
                </div>
                <payment :show="showPay" :type-text="\'付费报名\'" :type="\'activity\'" :price="price" :data="bmdata" @close-form="closePayForm"></payment>
            </header>';
            $html .= '<div class="activity-table mar20-t">
            <h2>活动内容</h2>
        </div><div class="entry-content">
            '.apply_filters('the_content',get_the_content()).'</div>';
            echo $html;
            unset($html);
        }
    }
    new RZR_ACTIVITY_TEMPLATE();