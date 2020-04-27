<?php
$status = get_post_field('post_status',get_the_id());
$att_author_id = get_post_field('post_author',get_the_id());
$current_user_id = get_current_user_id();
if($status == 'publish'){
    echo '<div class="box mar16-b pd20" id="relay'.get_the_id().'">
        <h2 class="pos-r pd10-b b-b"><span class="relay-title-in">'.get_the_title(). '</span></h2>
        <div class="clearfix pd10-t att-meta">
            <span class="fl"><a href="'.zrz_get_user_page_url().'">'.get_avatar($att_author_id,30).get_the_author_meta('display_name',$att_author_id).'</a></span>
            <span class="fr">'.zrz_time_ago().'</span>
            '.(current_user_can('delete_users') ? '<button class="relay-del mar10-l empty" data-id="'.get_the_id().'">删除</button>' : '').'
        </div>
        <p><img class="relay-img mar20-t mar20-b" src="'.zrz_get_thumb(get_the_guid(),851,'full').'" /></p>
        <p>'.get_the_excerpt().'</p></div>';
}elseif($att_author_id == $current_user_id || current_user_can('delete_users')){
    echo '<div class="box mar16-b pd20" id="relay'.get_the_id().'">
        <h2 class="pos-r pd10-b b-b"><span class="relay-title-in">'.get_the_title(). '</span></h2>
        <div class="clearfix pd10-t att-meta">
            <span class="fl">
                <a href="'.zrz_get_user_page_url().'">'.get_avatar($att_author_id,30).get_the_author_meta('display_name',$att_author_id).'</a>
                '.zrz_time_ago().'
            </span>
            <div class="fr"><span class="fs12 red" id="wating">正在审核中...</span>'.(current_user_can('delete_users') ? '<button class="relay-sh mar10-l" data-id="'.get_the_id().'">审核</button>' : '').'
            <button class="relay-del mar10-l empty" data-id="'.get_the_id().'">删除</button></div>
        </div>
        <p><img class="relay-img mar20-t mar20-b" src="'.zrz_get_thumb(get_the_guid(),851,'full').'" /></p>
        <p>'.get_the_excerpt().'</p></div>';
}
