<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ziranzhi2
 */
do_action( 'content_after_7b2' );
?>
	</div><!-- #content -->
	<footer id="colophon" class="site-footer mar20-t pd20-b pd20-t">
		<div class="site-info fs12">

			<?php do_action( 'footer_content_7b2' ); ?>

		</div>
	</footer>
</div><!-- #page -->

<?php do_action( 'footer_before_7b2' ); ?>

<?php wp_footer(); ?>
<script type="text/javascript">
/* 鼠标特效 */
var a_idx = 0;
jQuery(document).ready(function($) {
    $("body").click(function(e) {
        var a = new Array("富强", "民主", "文明", "和谐", "自由", "平等", "公正" ,"法治", "爱国", "敬业", "诚信", "友善");
        var $i = $("<span/>").text(a[a_idx]);
        a_idx = (a_idx + 1) % a.length;
        var x = e.pageX,
        y = e.pageY;
        $i.css({
            "z-index": 999999999999999999999999999999999999999999999999999999999999999999999,
            "top": y - 20,
            "left": x,
            "position": "absolute",
            "font-weight": "bold",
            "color": "#ff6651"
        });
        $("body").append($i);
        $i.animate({
            "top": y - 180,
            "opacity": 0
        },
        1500,
        function() {
            $i.remove();
        });
    });
});
<?php do_action( 'footer_after_7b2' ); ?>
</body>
</html>
