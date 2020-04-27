<?php 
$umlikes=get_post_meta($post->ID,'um_post_likes',true); 
$umcollects=get_post_meta($post->ID,'um_post_collects',true); 
if(empty($umlikes)):$umlikes=0; endif;if(empty($umcollects)):$umcollects=0; endif;
$like_cookie = 'um_post_like_'.$post->ID;
$umlikes_array = explode(',',$umlikes);
$umlikes_count = $umlikes!=0?count($umlikes_array):0;

?>   
    <?php $uid = get_current_user_id(); if(!empty($uid)&&$uid!=0){ ?>
	<span class="postlist-meta-like item like-btn <?php if(isset($_COOKIE[$like_cookie])) echo ' love-yes'; ?>" style="float:right;" pid="<?php echo $post->ID ; ?>" title="<?php _e('点击喜欢','um'); ?>"><i class="fa fa-heart"></i><span><?php echo $umlikes_count; ?></span>&nbsp;</span>
	<?php }else{ ?>
	<span class="postlist-meta-like item like-btn user-reg "  title="<?php _e('必须登录才能点赞','um'); ?>" style="float:right;" ><i class="fa fa-heart"></i>赞<span><?php echo $umlikes_count; ?></span>&nbsp;</span>
	<?php } ?>
	<?php $uid = get_current_user_id(); if(!empty($uid)&&$uid!=0){ ?>		
		<?php 
		   $mycollects = get_user_meta($uid,'um_collect',true);
		   $mycollects = explode(',',$mycollects);
		?>
		<?php global $curauth; ?>
		<?php if (!in_array($post->ID,$mycollects)){ ?>
		<span class="postlist-meta-collect item collect-btn collect-no pv" style="float:right;" pid="<?php echo $post->ID ; ?>" uid="<?php echo get_current_user_id(); ?>" title="<?php _e('点击收藏','um'); ?>"><i class="fa fa-star"></i><span><?php echo $umcollects; ?></span>&nbsp;</span>
		<?php }elseif(isset($curauth->ID)&&$curauth->ID==$uid){ ?>
		<span class="postlist-meta-collect item collect-btn collect-yes remove-collect pv" style="float:right;cursor:pointer;" pid="<?php echo $post->ID ; ?>" uid="<?php echo get_current_user_id(); ?>" title="<?php _e('取消收藏','um'); ?>"><i class="fa fa-star"></i><span><?php echo $umcollects; ?></span>&nbsp;</span>
		<?php }else{ ?>
		<span class="postlist-meta-collect item collect-btn collect-yes pv" style="float:right;cursor:default;" uid="<?php echo get_current_user_id(); ?>" title="<?php _e('你已收藏','um'); ?>"><i class="fa fa-star"></i><span><?php _e('已收藏','um'); ?></span>&nbsp;</span>
		<?php } ?>
		<?php }else{ ?>
		<span class="postlist-meta-collect item collect-btn collect-no pv" style="float:right;cursor:default;" title="<?php _e('必须登录才能收藏','um'); ?>"><i class="fa fa-star"></i><span><?php echo $umcollects; ?></span>&nbsp;</span>
	<?php } ?>