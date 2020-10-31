<script type="text/javascript">
/* <![CDATA[ */
    function grin(tag) {
      var myField;
      tag = ' ' + tag + ' ';
        if (document.getElementById('comment') && document.getElementById('comment').type == 'textarea') {
        myField = document.getElementById('comment');
      } else {
        return false;
      }
      if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = tag;
        myField.focus();
      }
      else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        var cursorPos = startPos;
        myField.value = myField.value.substring(0, startPos)
                + tag
                + myField.value.substring(endPos, myField.value.length);
        cursorPos += tag.length;
        myField.focus();
        myField.selectionStart = cursorPos;
        myField.selectionEnd = cursorPos;
      }      else {
        myField.value += tag;
        myField.focus();
      }
    }
/* ]]> */
</script>
<div id="smiley" style="display: none;">
<a href="javascript:grin(':?:')"      ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_question.gif"  alt="" /></a>
<a href="javascript:grin(':razz:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_razz.gif"      alt="" /></a>
<a href="javascript:grin(':sad:')"    ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_sad.gif"       alt="" /></a>
<a href="javascript:grin(':evil:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_evil.gif"      alt="" /></a>
<a href="javascript:grin(':!:')"      ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_exclaim.gif"   alt="" /></a>
<a href="javascript:grin(':smile:')"  ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_smile.gif"     alt="" /></a>
<a href="javascript:grin(':oops:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_redface.gif"   alt="" /></a>
<a href="javascript:grin(':grin:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_biggrin.gif"   alt="" /></a>
<a href="javascript:grin(':eek:')"    ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_surprised.gif" alt="" /></a>
<a href="javascript:grin(':shock:')"  ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_eek.gif"       alt="" /></a>
<a href="javascript:grin(':???:')"    ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_confused.gif"  alt="" /></a>
<a href="javascript:grin(':cool:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_cool.gif"      alt="" /></a>
<a href="javascript:grin(':lol:')"    ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_lol.gif"       alt="" /></a>
<a href="javascript:grin(':mad:')"    ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_mad.gif"       alt="" /></a>
<a href="javascript:grin(':twisted:')"><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_twisted.gif"   alt="" /></a>
<a href="javascript:grin(':roll:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_rolleyes.gif"  alt="" /></a>
<a href="javascript:grin(':wink:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_wink.gif"      alt="" /></a>
<a href="javascript:grin(':idea:')"   ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_idea.gif"      alt="" /></a>
<a href="javascript:grin(':arrow:')"  ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_arrow.gif"     alt="" /></a>
<a href="javascript:grin(':neutral:')"><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_neutral.gif"   alt="" /></a>
<a href="javascript:grin(':cry:')"    ><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_cry.gif"       alt="" /></a>
<a href="javascript:grin(':mrgreen:')"><img src="<?php bloginfo('template_url'); ?>/img/smilies/icon_mrgreen.gif"   alt="" /></a>
</div>
<div id="fontcolor" style="display: none;">
<a href="javascript:SIMPALED.Editor.red()" style="background-color: red"></a>
<a href="javascript:SIMPALED.Editor.green()" style="background-color: green"></a>
<a href="javascript:SIMPALED.Editor.blue()" style="background-color: blue"></a>
<a href="javascript:SIMPALED.Editor.magenta()" style="background-color: magenta"></a>
<a href="javascript:SIMPALED.Editor.yellow()" style="background-color: yellow"></a>
<a href="javascript:SIMPALED.Editor.chocolate()" style="background-color: chocolate"></a>
<a href="javascript:SIMPALED.Editor.black()" style="background-color: black"></a>
<a href="javascript:SIMPALED.Editor.aquamarine()" style="background-color: aquamarine"></a>
<a href="javascript:SIMPALED.Editor.lime()" style="background-color: lime"></a>
<a href="javascript:SIMPALED.Editor.fuchsia()" style="background-color: fuchsia"></a>
<a href="javascript:SIMPALED.Editor.orange()" style="background-color: orange"></a>
<a href="javascript:SIMPALED.Editor.thistle()" style="background-color: thistle"></a>
<a href="javascript:SIMPALED.Editor.brown()" style="background-color: brown"></a>
<a href="javascript:SIMPALED.Editor.peru()" style="background-color: peru"></a>
<a href="javascript:SIMPALED.Editor.deeppink()" style="background-color: deeppink"></a>
<a href="javascript:SIMPALED.Editor.purple()" style="background-color: purple"></a>
<a href="javascript:SIMPALED.Editor.slategray()" style="background-color: slategray"></a>
<a href="javascript:SIMPALED.Editor.tomato()" style="background-color: tomato"></a>
</div>