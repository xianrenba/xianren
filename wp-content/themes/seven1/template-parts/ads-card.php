<div class="pos-r pd10 post-list content-card grid-item">
    <div class="pos-r cart-list">
        <?php
            $ads = zrz_get_ads_settings('home_card');
            echo zrz_get_html_code($ads['str']);
        ?>
    </div>
</div>
