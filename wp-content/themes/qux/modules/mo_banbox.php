<!-- Ban模态框 -->
<div id="banBox" class="js-ban ban-form ban-form-modal fadeScale" role="dialog" aria-hidden="true">
    <div class="ban-header">
        <h2><?php _e('账户管理', 'um'); ?></h2>
    </div>
    <div class="ban-content">
        <div class="ban-inner">
            <input class="ban-nonce" type="hidden" value="<?php echo wp_create_nonce('um_ban_nonce'); ?>">
            <textarea class="ban-text mt10" placeholder="<?php _e('写点理由...', 'um'); ?>" tabindex="1" required></textarea>
        </div>
    </div>
    <div class="ban-btns mt20">
        <button class="cancel btn btn-default" tabindex="3"><?php _e('取消', 'um'); ?></button>
        <button class="confirm btn btn-danger ml10" data-box-type="modal" tabindex="2"><?php _e('确认', 'um'); ?></button>
        <a class="cancel close-btn"><i class="tico tico-close"></i></a>
    </div>
</div>