<?php
/**
 *这里可用作友情链接，网址导航等功能。
 */

get_header(); ?>

	<div id="primary" class="content-area" style="width:100%">
		<h2 class="pd10 t-c box mar10-b">导航链接</h2>
		<main id="link-add" class="site-main page pos-r">
            <div class="pos-r link-add pos-r box">
                <div id="zrz-links" class="zrz-links pos-r pd5 bg-b">
                    <?php
                        $cats = get_terms( 'link_category', array(
                                'hierarchical' => true,
								'hide_empty' => false,
								'orderby' =>  'meta_value_num',
 								'order' =>  'ASC',
								'meta_query' => array(
									array(
										'key' => 'orderby',
									   'type' => 'NUMERIC',
									)
								)
                            ) );

                        if(!empty($cats)){
							$i = 0;
                            foreach ( $cats as $cat ) {
								$i++;
                                $bookmarks = get_bookmarks( array('category'=>$cat->term_id,'orderby'=>'link_rating','order'=>'DESC') );
                                $html = '';
                                foreach ($bookmarks as $bookmark) {
									$user = get_userdata( $bookmark->link_owner );
                                    if($bookmark->link_image ){
                                        $img = $bookmark->link_image;
                                    }else{
                                        $avatar = zrz_get_avatar(md5(home_url()).'-'.$bookmark->link_name,30);
                                    }
                                    $img = $bookmark->link_image ? $bookmark->link_image : $avatar;
                                    $description = $bookmark->link_description ? $bookmark->link_description : '这个网站没有任何描述信息';
                                    $html .= '<li class="pd5 fd" ref="linklist">
                                        <div class="bg-w box">
                                            <div class="pd10">
												<img class="link-img pos-a" src="'.$img. '"/>
	                                            <a href="'.$bookmark->link_url.'" target="_blank" class="fd link-right">
	                                                <h2>'.$bookmark->link_name.'</h2>
	                                            </a>
											</div>
                                            <p class="link-description mobile-hide fs12 pd10">'.zrz_get_content_ex($description,30).'</p>
											<div class="link-tool pd10 b-t clearfix l1"><span class="fs12 fl">'.($user !== false ? zrz_get_user_page_link($bookmark->link_owner) : '未名').'</span><button class="fr mouh text" @click.stop="addRating(\''.$bookmark->link_id.'\')" ref="rating'.$bookmark->link_id.'"><i class="iconfont zrz-icon-font-fabulous"></i><b>'.$bookmark->link_rating.'</b></button></div>
                                        </div>
                                    </li>';}

                                echo '<div class="link-box pos-r bg-light mar20-b">
                                    <div class="link-title"><span class="fs12 bg-w l1 box" ref="cat'.$i.'">'.$cat->name.'</span></div>
                                    <div class="link-list mar10-t">
                                        <ul>'.$html.'</ul>
                                    </div>
                                </div>';
                            }
                        }else{
                            echo '<div class="loading-dom pos-r" style="background:#fafafa">
                                <div class="lm"><i class="iconfont zrz-icon-font-xiaoxi2"></i><p>没有链接</p></div>
                            </div>';
                        }
                    ?>
					<div class="pd10 t-r">
	                    <span class="fs12 mar10-r" v-cloak>{{!login ? '请先登陆' : ''}}</span><button class="button" @click="addLink" v-html="button">申请链接</button>
	                </div>

                </div>
                <?php if(is_user_logged_in()){ ?>
                    <div id="link-form" class="dialog link-form" ref="linkForm">
                        <div class="dialog__overlay" @click.stop="close"></div>
                		<div class="dialog__content">
                            <div class="clearfix fs14 b-b pd10 form-title box-header"><span class="fl">站点提交</span><button class="fr fs12 text" @click.stop="close">关闭</button></div>
                            <form @submit.stop.prevent="subLink">
                                <label class="mar10-b">
                                    <input type="text" name="name" value="" v-model="name" placeholder="网站名称">
                                </label>
                                <label class="mar10-b">
                                    <input type="text" name="url" value="" v-model="url" placeholder="网站地址，请带上 http 或 https">
                                </label>
								<label class="mar10-b">
                                    <input type="text" name="image" value="" v-model="image" placeholder="网站图标，请输入网址">
                                </label>
                                <label class="mar10-b">
                                    <select name="cat" v-model="cat">
                                        <option value="">请选择链接分类</option>
                                        <?php
                                            foreach ($cats as $cat) {
                                                echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
                                            }
                                        ?>
                                    </select>
                                </label>
                                <label class="mar10-b">
                                    <textarea id="textarea" class="textarea pd10" placeholder="网站的描述..." pellcheck="false" v-model="description"></textarea>
                                </label>
                                <div class="t-r"><button class="button" @click.stop.prevent="subLink" v-html="subText">提交申请</button></div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
			<div class="link-cat-list pos-a box" data-margin-top="70">
				<div class="fs12 pd10 b-b box-header">快速导航</div>
				<p class="pd5"></p>
				<?php
					if(!empty($cats)){
						$i = 0;
						foreach ($cats as $cat) {
							$i++;
							echo '<button class="text" @click="go(\'cat\'+'.$i.')">'.$cat->name.'</button>';
						}
					}else{
						echo '<div class="t-c">没有连接</div>';
					}
				?>
				<p class="pd5"></p>
			</div>
		</main><!-- #main -->

	</div>
<?php
get_footer();
