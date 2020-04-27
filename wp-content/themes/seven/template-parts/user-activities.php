<?php
//用户动态，相关函数在 inc/functions-user.php 文件
$user_id = get_query_var('author');
$msg = zrz_get_user_activities_fn($user_id,1,true);
$ipages = zrz_get_user_activities($user_id,1,true);
if($msg){
    echo '<div id="user-activities">
    <div class="box-header pd10 b-b"><span v-text="uName"></span>的动态</div>
              <div class="activities-list" ref="activitiesList">'.$msg.'</div>
              <page-nav class="b-t" nav-type="user-activities" :paged="paged" :pages="'.$ipages.'" :show-type="\'p\'"></page-nav>
          </div>';
}else{
    echo '<div class="loading-dom pos-r"><div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">没有动态</p></div></div>';
}
