<?php
/*
* 任务系统
*/
class ZRZ_TASK{

    private $user_id,$type,$task_data;

    function __construct($user_id,$type = ''){
        $this->user_id = $user_id;
        $this->type = $this->type_id_to_name($type);
        $this->task_data = get_user_meta($this->user_id,'zrz_task',true);
        $this->task_data = is_array($this->task_data) && !empty($this->task_data) ? $this->task_data : array(
            'comment'=>array(
                'count'=>0,
                'text'=>__('文章评论','ziranzhi2'),
                'des'=>__('对他人的文章进行评论，或者回复他人的评论','ziranzhi2'),
            ),//评论，主动任务
            'post'=>array(
                'count'=>0,
                'text'=>__('写文章','ziranzhi2'),
                'des'=>__('书写高质量的文章，并且通过了管理员的审核','ziranzhi2'),
            ),//发文，主动任务
            'post_commented'=>array(
                'count'=>0,
                'text'=>__('评论被回复','ziranzhi2'),
                'des'=>__('你在某篇文章中的评论被其他网友回复了','ziranzhi2'),
            ),//文章被回复，回复被评论，被动任务
            'comment_vote_up'=>array(
                'count'=>0,
                'text'=>__('评论被点赞','ziranzhi2'),
                'des'=>__('你的评论被其他网友点了赞','ziranzhi2'),
            ),//评论被点赞，被动任务
            'comment_vote_up_deduct'=>array(
                'count'=>0,
                'text'=>__('评论点赞','ziranzhi2'),
                'des'=>__('给他人的评论点赞','ziranzhi2'),
            ),//给其他人的评论点赞，主动任务
            'followed'=>array(
                'count'=>0,
                'text'=>__('关注他人','ziranzhi2'),
                'des'=>__('你对某人进行了关注','ziranzhi2'),
            ),//关注他人，主动任务
            'follow'=>array(
                'count'=>0,
                'text'=>__('被关注','ziranzhi2'),
                'des'=>__('你被某人关注了','ziranzhi2'),
            ),
            'reply'=>array(
                'count'=>0,
                'text'=>__('回复帖子','ziranzhi2'),
                'des'=>__('对他人的帖子进行了回复','ziranzhi2'),
            ),//回复帖子，主动任务
            'topic'=>array(
                'count'=>0,
                'text'=>__('发表帖子','ziranzhi2'),
                'des'=>__('发布了帖子，并且审核通过','ziranzhi2'),
            ),//发表帖子，主动任务
            'pps'=>array(
                'count'=>0,
                'text'=>'发布'.zrz_custom_name('bubble_name'),
                'des'=>'发布了'.zrz_custom_name('bubble_name').',并且通过了审核'
            ),//发表冒泡，主动任务
            'labs'=>array(
                'count'=>0,
                'text'=>'发布'.zrz_custom_name('labs_name'),
                'des'=>'发布了'.zrz_custom_name('labs_name').',并且通过了审核'
            ),//发表研究，主动任务
            'invitation'=>array(
                'count'=>0,
                'text'=>__('邀请注册','ziranzhi2'),
                'des'=>__('某人通过您的邀请链接进入了进行了注册','ziranzhi2'),
            ),//邀请注册，主动任务
        );
    }

    //消息类型转换成名称
    public function type_id_to_name($type){
        $name = '';
        switch ($type) {
            case 2:
                $name = 'comment';
                break;
            case 5:
                $name = 'post';
                break;
            case 1:
            case 3:
            case 20:
                $name = 'post_commented';
                break;
            case 10:
                $name = 'comment_vote_up';
                break;
            case 9:
                $name = 'comment_vote_up_deduct';
                break;
            case 42:
                $name = 'followed';
                break;
            case 11:
                $name = 'follow';
                break;
            case 18:
                $name = 'reply';
                break;
            case 17:
                $name = 'topic';
                break;
            case 24:
                $name = 'pps';
                break;
            case 36:
                $name = 'labs';
                break;
            case 39:
                $name = 'invitation';
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }

    //检查任务
    public function check_task(){
        if($this->type === '') return true;
        $count = $this->task_data[$this->type];

        //当前任务完成数量
        $count = $count['count'];

        //设置的数量
        $opt_task = zrz_get_task_setting($this->type);
        $count_setting = $opt_task['count'];
        $allow = $opt_task['open'];

        if($count < $count_setting && $allow) return true;
        return false;
    }

    //任务计数
    public function task_count(){
        if($this->type === '') return;
        $data = $this->task_data[$this->type];
        $data['count'] ++;
        $this->task_data[$this->type] = $data;
        update_user_meta($this->user_id,'zrz_task',$this->task_data);
    }

    //获取任务列表
    public function get_task_list(){
        $html = '<ul>';
        foreach ($this->task_data as $key => $val) {
            $opt_task = zrz_get_task_setting($key);
            $opt_count = isset($opt_task['count']) ? $opt_task['count'] : 0;
            $finish = $val['count'] < $opt_count ? false : true;
            $credit = zrz_get_credit_settings('zrz_credit_'.$key);
            $allow = isset($opt_task['open']) ? $opt_task['open'] : 0;
            if($allow){
                $html .= '<li class="clearfix"><div class="task-text fl"><h2>'.$val['text'].'<b class="'.($finish ? ' green' : ' red').'">'.$val['count'].'/'.$opt_count.'</b></h2><p class="gray">'.$val['des'].'</p></div><div class="mar5-t task-finish fr '.($finish ? 'green' : 'red').'">'.($finish ? '<span>已完成</span>' : '<span>未完成</span>').'<p class="gray">每次奖励：'.$credit.'</p></div></li>';
            }
        }
        $html .= '</ul>';
        return $html;
    }

    //任务完成进度
    public function task_finish(){
        $count = 0;
        $total = 0;
        foreach ($this->task_data as $key => $val) {
            $opt_task = zrz_get_task_setting($key);
            $opt_count = isset($opt_task['count']) ? (int)$opt_task['count'] : 0;
            $allow = isset($opt_task['open']) ? (int)$opt_task['open'] : 0;
            if($allow){
                $count += $val['count'];
                $total += $opt_count;
            }
        }
        if(!$total) return false;
        return (sprintf("%.2f", $count/$total*100)).'%';
    }
}
