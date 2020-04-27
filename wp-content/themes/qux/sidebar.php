<?php if( _hui('layout') == '1' || get_term_meta( get_queried_object_id(), '_widthfull', true)) return; ?>
<aside class="sidebar">
<?php 
	_moloader('mo_notice', false);
	if(get_post_type() == 'forum' || is_page_template('pages/forum-newpost.php') || is_page_template('pages/forum.php')){
		$new_url = is_user_logged_in() ? '<a class="q-btn-new" href="'.get_permalink(_hui('forum_new_page')).'"><i class="fa fa-edit"></i> 发布新帖</a>' : '<a href="javascript:;" class="user-reg q-btn-new" data-sign="0"><i class="fa fa-edit"></i> 发布新帖</a>' ;
		echo '<div id="qapress-new-2" class="widget widget_qapress_new">'.$new_url.'</div>';
	}
		if (function_exists('dynamic_sidebar')){
		
		dynamic_sidebar('gheader');       
		if (is_home()){
			dynamic_sidebar('home'); 
		}		
		elseif (is_category()){
			dynamic_sidebar('cat'); 
		}
		else if (is_tag() ){
			dynamic_sidebar('tag'); 
		}
		else if (is_search()){
			dynamic_sidebar('search'); 
		}
		else if (is_single() && !(get_post_type() == 'forum')){
			dynamic_sidebar('single'); 
		}
		else if(get_post_type() == 'forum' || is_page_template('pages/forum-newpost.php') || is_page_template('pages/forum.php')){
			dynamic_sidebar('forum'); 
		}else if(is_page_template('pages/page-zhuanti.php')){
			dynamic_sidebar('topic'); 
		}

		dynamic_sidebar('gfooter');
	}  	
?>
</aside>
