<?php 
/**
 * [mo_share description]
 * @param  string $stop [description]
 * @return html       [description]
 */
function mo_share($stop=''){
    global $post;
	if( _hui('share_s') ){
        echo _hui('share_code');
        return;
    }
    
    ?><div class="bdshare">
		<div id="bdshare" class="shares"><span><?php _e('分享到：','haoui'); ?></span>
		    <a etap="share" data-share="tqq" class="bds_tqq share" title="分享到腾讯微博"></a>
		    <a etap="share" data-share="douban" class="bds_douban share"  title="分享至豆瓣"></a>
			<a etap="share" data-share="weibo" class="bds_tsina share" title="<?php _e('分享到新浪微博','haoui'); ?>"></a>
			<a etap="share" data-share="qq" class="bds_sqq share"  title="<?php _e('分享到QQ好友','haoui'); ?>"></a>
			<a etap="share" data-share="qzone" class="bds_qzone share"  title="<?php _e('分享到QQ空间','haoui'); ?>"></a>
			<a href="javascript:;" data-url="<?php echo get_the_permalink(); ?>" class="bds_weixin share wx_share" title="<?php _e('分享到微信','haoui'); ?>"></a>
			<a etap="share" data-share="twitter" class="bds_twitter share" title="分享至twitter"></a>
			<a etap="share" data-share="facebook" class="bds_facebook share" title="分享至facebook"></a>
			<!--<a etap="share" data-share="renren" class="bds_renren share" title="分享至人人网"></a>-->
			<?php if(is_single()){ ?>
			<a class="bds_cover" data-action="create-bigger-image" data-nonce="<?php echo wp_create_nonce('bigger-image-'.$post->ID );?>" data-id="<?php echo $post->ID; ?>" id="bigger-cover" href="javascript:;" title="<?php _e('生成海报','haoui'); ?>"><i class="fa fa-paper-plane"></i> 生成海报</a><?php } ?>

		</div>
	</div><?php
}