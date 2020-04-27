<?php
/*
* 积分与通知
* $type 是积分类型，用数字表示，对应 zrz_message 表中的 type ,目前最大46项。含义如下 ：
*
* 4 新注册用户增加积分和通知
* 46 邀请注册奖励积分（邀请）
*
* 1 如果当前评论有父级，给父级评论作者通知（评论）
* 2 给评论者增加积分（评论）
* 3 文章被回复，给文章作者通知（评论）
* 8 不喜欢某个评论，给评论作者通知（评论）
* 9 喜欢某个评论，给这个人的人通知，并扣除积分（评论）
* 10 喜欢某个评论，给评论作者通知（评论）
*
* 11 关注了某人，给某人通知（关注）
* 15 取消关注某人，给某人通知（关注）
* 42 关注了某人，给自己增加积分（关注）
* 43 取消关注了某人，给自己减掉的积分（关注）
*
* 12 私信通知（私信）
* 13 私信内容（私信）
*
* 14 管理员给某人变更了积分，通知某人（用户）
* 37 管理员给某人变更了余额，通知某人（用户）
* 16 签到的通知（用户）
*
* 17 发表帖子通知（bbpress）
* 18 帖子回复通知（bbpress）
* 19 给帖子的作者通知（bbpress）
* 20 帖子回复时提到某人，给这个人通知(bbpress)
*
* 5 发表文章（文章）
* 6 文章被点赞，给文章作者通知（文章）
* 7 文章被取消点赞，给文章作者通知（文章）
* 21 打赏人减掉积分时通知（文章）
* 22 被打赏人增加积分时通知（文章）
* 25 文章被删除时发出通知（文章）
* 31 付费文章购买通知（文章）
* 32 付费文章出售通知（文章）
* 33 积分文章购买通知（文章）
* 34 积分文章出售通知（文章）
* 35 打赏给作者通知（文章）
*
* 36 发表研究（研究）
*
* 23 有人申请了有情链接，给管理员通知（友情链接）
* 24 发表了冒泡，给冒泡作者通知（冒泡）
* 26 冒泡被点赞，给冒泡作者通知（冒泡）
* 27 冒泡被取消点赞，给冒泡作者通知（冒泡）
*
* 28 积分购买（商城）
* 29 积分抽奖（商城）
* 30 购买（商城）
* 38 积分兑换
*
* 39 邀请别人成功，给自己增加积分
* 40 被邀请人增加积分
*
* 41 提现申请
*
* 44 报名通知（活动）
* 45 给报名的人减掉积分（活动）
*/

class Zrz_Credit_Message{

    private $user_id;//用户ID
    private $type;//通知类型
    private $date;//时间
    private $hot;//是否增加活跃值

    private $wpdb;//数据库全局变量
    private $table_name;//表名

    private $users;
    private $credit;
    private $credit_total;
    private $key;
    private $value;


    public function __construct($user_id, $type = 0,$hot = true){

        $this->user_id = (int)$user_id;
        $this->type = (int)$type;
        $this->hot = $hot;

        $this->date =  current_time( 'mysql' );

        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'zrz_message';

    }

    //添加数据
    private function add_data(){

    	if( $this->wpdb->insert( $this->table_name, array(
            'user_id'=> $this->user_id,
            'msg_type'=> $this->type,
            'msg_read'=> 0,
            'msg_date'=> $this->date,
            'msg_users'=> $this->type != 41 ? json_encode(array($this->users),JSON_UNESCAPED_UNICODE) : $this->users,
            'msg_credit'=> $this->credit,
            'msg_credit_total'=> (int)$this->credit_total,
            'msg_key'=> $this->key,
            'msg_value'=> sanitize_text_field($this->value)
        ) ) )
    	return $this->wpdb->insert_id;

    	return false;

    }

    //更新数据
    private function update_data($msg_id,$set){

        if(!$msg_id) return;
        $set['msg_date'] = $this->date;
        $resout = $this->wpdb->update(
            $this->table_name,
            $set,
            array('msg_id'=>$msg_id)
        );
        if($resout) return true;

        return false;

    }

    //查找数据
    public function get_data($where , $select ,$ord = '',$limit = 0, $offset = 0){

            if(!$where || !$select) return;

            if($ord){
                $order = ' GROUP BY msg_key ORDER BY msg_date DESC';
                $tab = '(SELECT '.$select.' FROM '.$this->table_name.' WHERE '.$where.' ORDER BY msg_date DESC) '.$this->table_name;
				if($ord === 'count'){
					$check = $this->wpdb->get_results( "SELECT count(*) FROM $tab $order" );
				}else{
					$check = $this->wpdb->get_results( "SELECT * FROM $tab $order LIMIT $offset,$limit" );
				}
            }else{
                $order = 'ORDER BY msg_date ASC';
                if($limit == 'all'){
                    $check = $this->wpdb->get_results( "SELECT $select FROM $this->table_name WHERE $where $order" );
                }else{
                    $check = $this->wpdb->get_results( "SELECT $select FROM $this->table_name WHERE $where $order LIMIT $offset,$limit" );
                }
            }

            if($check) return $check;

            return false;
    }

    //积分变更
    private function credit_action($type = 0){
        if($this->credit == 'empty') return false;

        if(!$this->credit || !$this->user_id || $this->type == 41) return false;

        //如果任务完成则不增加积分
        $task = new ZRZ_TASK($this->user_id,$type);
        //检查任务完成情况
        if(!$task->check_task()){
            return false;
        }else{
            //任务加1
            $task->task_count();
        }

        //取得用户当前的积分并进行计算
        $user_credit = (int)get_user_meta($this->user_id,'zrz_credit_total',true);
        $user_credit_new = $user_credit + (int)$this->credit;

        //增加活跃值
        if($this->hot){
            $user_hot = (int)get_user_meta($this->user_id,'zrz_hot',true);
            if($type != 14){
                update_user_meta($this->user_id,'zrz_hot', abs($this->credit) + $user_hot);
            }
        }

        //如果最后积分小于零，返回
        if($user_credit_new < 0){
            return false;
        }

        //写入积分,如果是积分抽奖和积分兑换则不受限制
        update_user_meta($this->user_id,'zrz_credit_total',$user_credit_new);
        zrz_set_lv($this->user_id);

        return apply_filters('zrz_update_credit',$user_credit_new);
    }

    //添加消息
    public function add_message($users = '',$credit = 0,$key = 0,$value = '',$type = 'add'){

        //如果设置了空，则不增加消息，保留活动报名通知
        if($credit === 'empty' && $this->type != 44) return false;

        //检查未读的历史数据
        $check = $this->get_data('user_id='.$this->user_id.' AND msg_read=0 AND msg_type='.$this->type.' AND msg_key='.$key,'msg_id,msg_credit,msg_credit_total,msg_users,msg_value','','all');

        //如果存在未读的历史数据
        if($check && $this->type != 13 && $this->type != 41 && $this->type != 30 && $this->type != 16 && $this->type != 45){
            $msg_id = (int)$check[0]->msg_id;
            $msg_users = json_decode($check[0]->msg_users);

            //如果是兑换或者抽奖
            if($this->type == 28 || $this->type == 29 || $this->type == 30 || $this->type == 38){
                if($this->type != 29){
                    $value = (int)$check[0]->msg_value + 1;
                }

                $users = (int)end($msg_users) + 1;
            }
            
            if(!in_array($users, $msg_users,true)){
                if($this->type != 28 && $this->type != 29 && $this->type != 38){
                    array_push($msg_users, $users);
                }
                $msg_users_json = json_encode($msg_users,JSON_UNESCAPED_UNICODE);

                //积分处理
                $msg_credit = $check[0]->msg_credit;
                $msg_credit_total = $check[0]->msg_credit_total;

                //如果是有积分动作对通知
                if($credit){
                    $this->credit = $credit;
                    $credit_total = $this->credit_action($this->type);
                    if($credit_total){
                        $msg_credit = $msg_credit + $credit;
                        $msg_credit_total = $credit_total;
                    }
                }
                if($this->type == 12){
                    $msg_credit_total = 1;
                }
                $set = array(
                    'msg_credit'=>$msg_credit,
                    'msg_credit_total'=>$msg_credit_total,
                    'msg_users'=>$msg_users_json,
                    'msg_value'=>sanitize_text_field($value)
                );
                $resout = $this->update_data($msg_id,$set);
            }elseif($this->type == 35 || $this->type == 14 || $this->type == 37 || $this->type == 12){

                $msg_users = array_flip($msg_users);
				unset($msg_users[$users]);
                $msg_users = array_flip($msg_users);

                $msg_users[] = $users;

                $msg_users_json = json_encode($msg_users,JSON_UNESCAPED_UNICODE);

                $set = array(
                    'msg_users'=>$msg_users_json,
                    'msg_value'=>sanitize_text_field($value)
                );
                $resout = $this->update_data($msg_id,$set);
            }else{
                $resout = false;
            }
        }else{
            //不存在未读的信息，直接添加
            $this->credit = 0;
            $this->credit_total = 0;
            $this->users = $users;
            $this->key = $key;
            $this->value = $value;

            //如果是申请提现，credit 字段则是提现的金额，不通过积分函数处理
            if($this->type == 44 && $credit == 'empty'){
                $this->credit_total = 1;
            }elseif($this->type == 41){
                $this->credit = $credit;
            }elseif($credit){
                $this->credit = $credit;
                $credit_total = $this->credit_action($this->type);
                
                if($credit_total){
                    $this->credit = $credit;
                    $this->credit_total = $credit_total;
                }
            }

            if($this->type == 12){
                $this->credit_total = 1;
            }

            $resout = $this->add_data();
        }

        if($resout) return true;
        return false;
    }

    public function get_message($count = 0,$where = '',$limit = 0,$offset = 0){

    	if($count){
    		if($where) $where = " AND $where";
    		$check = $this->wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name WHERE user_id=$this->user_id $where" );
    	}else{
    		$check = $this->wpdb->get_results( "SELECT msg_id,msg_type,msg_read,msg_date,msg_credit,msg_credit_total,msg_users,msg_key,msg_value FROM $this->table_name WHERE user_id='$this->user_id' AND $where ORDER BY msg_date DESC LIMIT $offset,$limit" );
    	}
    	if($check)	return $check;
    	return 0;
    }

}

//启动定时
add_action( 'wp', 'zrz_clear_msg_by_time' );
function zrz_clear_msg_by_time() {
	if ( !wp_next_scheduled( 'zrz_clear_msg_by_time_event' ) ) {
		wp_schedule_event(time(), 'daily', 'zrz_clear_msg_by_time_event');
        wp_schedule_event(time(), 'daily', 'zrz_clear_orders_by_time_event');
	}
}

//定时动作删除消息
add_action( 'zrz_clear_msg_by_time_event', 'zrz_clear_msg_by_time_do' );
function zrz_clear_msg_by_time_do(){
    $setting = zrz_get_display_settings('delete_msg');
    if(!$setting['msg_open']) return;
    $time = date("Y-m-d H:i:s",strtotime('-'.$setting['msg_time'].' day'));
    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_message';
	$wpdb->query( ' DELETE FROM '.$table_name.' WHERE msg_read=1 AND NOT msg_type=13 AND NOT msg_type=41 AND msg_date < \''.$time.'\'' );
}

//定时删除临时订单
add_action( 'zrz_clear_orders_by_time_event', 'zrz_clear_orders_by_time_do' );
function zrz_clear_orders_by_time_do(){
    $setting = zrz_get_display_settings('delete_msg');
    $time = date("Y-m-d H:i:s",strtotime('-1 day'));
    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_order';
	$wpdb->query( ' DELETE FROM '.$table_name.' WHERE order_state="w" AND order_date < \''.$time.'\'' );
}