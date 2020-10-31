<?php 
get_header(); 

// paging
$pagedtext = '';
if( $paged && $paged > 1 ){
	$pagedtext = ' <small>第'.$paged.'页</small>';
}

$cur_day = '';
$weekarray = array("日","一","二","三","四","五","六");

?>
<section class="container">
	<div class="content-wrap">
		<div class="content-bulletin">
			<div class="bulletin-head">
                <h1 class="bulletin-title">网站公告</h1>
            </div>
            <div class="bulletin-list">
                <?php  foreach ( $posts as $post ) { setup_postdata( $post );
                        if($cur_day != $date = get_the_date(get_option('date_format'))){
                            $pre_day = '';
                            $week = $weekarray[date('w', strtotime(get_the_date('c')) )];
                            if(date(get_option('date_format'), time()) == $date) {
                                $pre_day = '今天 • ';
                            }else if(date(get_option('date_format'), strtotime("-1 day")) == $date){
                                $pre_day = '昨天 • ';
                            }else if(date(get_option('date_format'), strtotime("-2 day")) == $date){
                                $pre_day = '前天 • ';
                            }
                            echo '<div class="bulletin-date">'. $pre_day .$date . ' • 星期' . $week.'</div>';
                            if($cur_day=='') echo '<div class="bulletin-new"></div>';
                            $cur_day = $date;
                        } ?>
                        <div class="bulletin-item" data-id="<?php the_ID();?>">
                            <span class="bulletin-time"><?php the_time(get_option('time_format'));?></span>
                            <div class="bulletin-content">
                                <h2><a href="<?php the_permalink();?>" target="_blank"><?php the_title();?></a></h2>
                                <?php the_excerpt();?>
                                <?php if(get_the_post_thumbnail()){ ?>
                                <a class="bulletin-img" href="<?php the_permalink();?>" title="<?php echo esc_attr(get_the_title());?>" target="_blank"><?php the_post_thumbnail('full'); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                <?php } ?>
                <?php  _moloader('mo_paging'); ?>
            </div>
            <?php wp_reset_query();?>
		</div>
	</div>
</section>
<?php get_footer(); ?>