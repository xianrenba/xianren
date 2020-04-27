<?php
    $thumb = zrz_get_post_thumb();
    $join_count = (int)get_post_meta(get_the_id(),'zrz_join_num',true);
    $status = zrz_post_status();
    $can = current_user_can('delete_users');
 ?>

 <div id="post<?php echo $id; ?>" ref="postList" class="pos-r pd10 post-list content user-edit">
    <div class="pos-r">
        <a href="<?php echo get_permalink(); ?>">
            <?php if($thumb) { ?>
                <div class="thumb">

                    <div style="background-image:url('<?php echo zrz_get_thumb($thumb,300,200); ?>')" class="preview thumb-in"></div>
                    <span class="gray user-post-time pos-a shadow"><?php echo zrz_time_ago(); ?></span>
                </div>
            <?php }else{ ?>
                <div class="thumb text-thumb"> ?>
                    <div class="preview thumb-in">
                        <span class="lm"><?php echo zrz_get_content_ex(); ?></span>
                        <span class="gray user-post-time pos-a shadow"><?php echo zrz_time_ago(); ?></span>
                    </div>
                </div>
            <?php } ?>
        </a>
        <div class="post-info pos-r <?php echo $thumb ? 'post-side' : ''; ?>">
            <?php if(get_the_title()){ ?>
                <h2 class="entry-title"><?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?><span class="status mar10-l"><?php echo $status ? '['.$status.']' : '' ; ?></span></h2>
            <?php }else{ ?>
                <h2 class="entry-title"><a href="<?php echo get_permalink(); ?>"><span class="red">*</span> 此文暂未设置标题</a><span class="status mar10-l"><?php echo $status ? '['.$status.']' : ''; ?></span></h2>
            <?php } ?>
            <div class="mar10-b post-ex mar10-t mobile-hide"><?php echo zrz_get_content_ex(); ?></div>
            <div class="post-meta meta mar10-t clearfix"><span><?php echo $join_count; ?>人参与</span></div>
                <div class="edit pos-a">
                    <?php if((zrz_check_edit_time('labs',$id) > 0 && $status) || $can){ echo '<a class="empty mar10-r button" href="'.home_url('/add-labs?pid='.$id).'">编辑</a>'; } ?>
                    <?php if($status || $can){ echo '<button class="empty mar10-r delete-post" data-id="'.$id.'">删除</button>'; } ?>
                    <?php if(!$status && $can){ echo '<button class="empty set-hd" data-id="'.$id.'">'.(zrz_is_id_in_swipe($id) ? '取消幻灯' : '设为幻灯').'</button>';}; ?>
                </div>
        </div>
    </div>
 </div>
