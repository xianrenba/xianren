<?php
class widget_ui_themeinfo extends WP_Widget {

   /*  function Authorinfo() {
        $widget_ops = array('classname' => 'widget_ui_authorinfo', 'description' => '显示当前文章的作者信息！');
        $this->WP_Widget('Authorinfo', '本文作者', $widget_ops);
    } */

   /*  function update($new_instance, $old_instance) {
        return $new_instance;
    } */
    function __construct(){
		parent::__construct( 'widget_ui_themeinfo', 'qux 主题推荐', array( 'classname' => 'widget_ui_themeinfo' ) );
	}
	
    function widget($args, $instance) {
        extract( $args );
      
       	$title   = apply_filters('widget_name', $instance['title']);
		$tag     = isset($instance['tag']) ? $instance['tag'] : '';
		$content = isset($instance['content']) ? $instance['content'] : '';
		$link    = isset($instance['link']) ? $instance['link'] : '';
        $src    = isset($instance['src']) ? $instance['src'] : '';

      
        echo $before_widget;
        echo widget_themeinfo($title, $tag, $content, $link, $src);
        //echo um_author_info_module();
        echo $after_widget;
    }
    	
    function form($instance) {
		$defaults = array( 
			'title' => 'DUX轻语博客加强版V2.7', 
			'tag' => '59', 
			'content' => '<li><i class="fa fa-check"></i>安装wordpress及主题服务一次</li>
					 <li><i class="fa fa-check"></i>一次购买终身免费提供主题更新服务</li>
					<li><i class="fa fa-check"></i>强大的人工服务，为你解决主题问题</li>
					<li><i class="fa fa-check"></i>使用无忧，协助使用后台设置</li>', 
			'link' => 'https://www.qyblog.cn/2017/02/991.html#pay-content', 
            'src' => 'https://www.qyblog.cn/wp-content/uploads/2018/03/dp.png',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
        <p>
			<label>
				商品图片(358x145)：
				<input style="width:100%;" id="<?php echo $this->get_field_id('src'); ?>" name="<?php echo $this->get_field_name('src'); ?>" type="url" value="<?php echo $instance['src']; ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				商品名称：
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" class="widefat" />
			</label>
		</p>
		<p>
			<label>
				商品描述：
				<textarea id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" class="widefat" rows="3"><?php echo $instance['content']; ?></textarea>
			</label>
		</p>
		<p>
			<label>
				商品售价：
				<input id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo $instance['tag']; ?>" class="widefat" />
			</label>
		</p>
		<p>
			<label>
				商品链接：
				<input style="width:100%;" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="url" value="<?php echo $instance['link']; ?>" size="24" />
			</label>
		</p>
<?php
	}
}
function widget_themeinfo($title, $tag, $content, $link, $src){
    ?>
 <style>
.themeinfo{
 	overflow: hidden;
 }
   
.themeinfo img{
    display: block;
    height: auto;
    border-radius: 3px 3px 0 0;
    background-size: cover;
}
.theme-info {padding:10px;}
.theme-info h2{
   font-size:18px;
   font-weight:700;
   margin:0;
   padding:5px 0 10px 0;
}
.theme-info ul{margin-left: 10px;margin-top:0;margin-bottom:0;padding:0;}
.theme-info ul li{
    font-size: 13px;
    margin-bottom: 8px;
	list-style:none;
}
.theme-info ul li i{margin-right:5px;}
.themeinfo-content .sale{
    margin:0;
	border-top: 1px solid #eee;
	padding:10px 5px;
	margin: 0 15px;
}
.themeinfo-content .sale h2{
    margin:0;
    font-size: 14px;
    color: #999;
    line-height: 20px;
}
.themeinfo-content .sale small{
    font-style: normal;
    font-size: 12px;
    position: relative;
    margin-right: 4px;
}
.themeinfo-content .sale strong{
    float: right;
    font-size: 20px;
    line-height: 1;
    font-weight: 200;
} 
.themeinfo-content a{
    color: #fff;
    background-color: #0098d4;
    border-color: #0098d4;
    display:block;
	padding:10px;
	margin:10px 15px;
	text-align:center;
	text-decoration:none;
	cursor: pointer;
 }
.themeinfo-content a:hover{
   opacity:0.8;
}
  </style>  
	<div class="widget-content themeinfo">
            <img title="DUX主题" src="<?php echo $src ?>">
            <div class="themeinfo-content">
                <div class="theme-info">
                <h2><?php echo $title ?></h2>
                 <ul>
				    <?php echo $content ?>
				 </ul>
                </div>
				<div class="sale"><strong><small>￥</small><?php echo $tag ?></strong><h2>统一零售价:</h2></div>
                <a href="<?php echo $link ?>" target="_blank">立即购买</a>
            </div>
    </div>  
    <?php
}
?>
