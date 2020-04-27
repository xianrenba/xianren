<?php
//任务页面
get_header();
$user_id = get_current_user_id();
$task = new ZRZ_TASK($user_id);
?>
<div id="primary" class="content-area fd">
	<main id="top" class="site-main task-page pos-r" role="main" ref="gold">
        <article class="box page-top">
            <div class="b-b pd10 clearfix bg-d">
                <span class="fs12 fl">您已经完成了今天任务的<?php echo $task->task_finish(); ?></span><span class="dot fr"><?php echo date("Y-m-d"); ?></span>
            </div>
            <div class="my-task">
                <?php
                    echo $task->get_task_list();
                ?>
            </div>
        </article>
    </main>
</div><?php
get_sidebar();
get_footer();
