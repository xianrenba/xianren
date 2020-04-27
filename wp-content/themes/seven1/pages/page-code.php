<?php
/**
 *Template Name: 代码高亮
 *
 *这里可用作友情链接，网址导航等功能
 *
 */

get_header(); ?>

	<div id="primary" class="content-area w100">
		<main id="main" class="site-main box page">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header pd20 b-b">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<style id='ziranzhi2-style-inline-css' type='text/css'>
					.entry-code {
						padding: 10px;
					}
					.code-h {
						font-size: 15px;
						font-weight: bold;
						margin: 15px auto;
						padding: 0 0 0 5px;
						border-left:5px solid #000;
						
					}
					.code-box  {
						margin: 20px 10px 10px 0;
					}
					.entry-code textarea {
						background: #fff;
						padding: 10px;
						border: 1px solid #ebebeb;
						resize:auto;
						overflow-y:auto;
					}
					.options {
						background: #f8f8f8;
						margin: 10px 10px 10px 0;
						padding: 10px 15px;
						border: 1px solid #ccc;
						border-radius: 2px;
						box-shadow: 0 1px 1px rgba(0, 0, 0, 0.03);
					}
					.entry-code button {
						color: #fff;
						line-height:inherit;
						padding: 0 18px;
						background: #2C74E6;
						border: 1px solid #2C74E6;
						cursor:pointer;
						border-radius: 2px;
						margin-right: 10px;
						box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
					}
					.entry-code button:hover {
						background: #666;
						border: 1px solid #555;
					}
					.entry-code select {
						border: 1px solid #ccc;
						width: 75px;
						padding:2px;
					}
					.entry-code p {
						color: #888;
						text-indent: 0em;
						margin: 0 0 5px 0;
					}
					.options_no {
						display: none;
					}
					</style>
				</header><!-- .entry-header -->
				<div class="entry-content pd20">
					<div class="entry-code">
						<div class="code-box">
							<div class="code-h">输入源代码</div>
							<!-- <div id="copypaste">
								<a href="#" onclick="docopy('source')">&nbsp;复制&nbsp;</a>
								|<a href="#" onclick="dopasted('source')">&nbsp;粘贴&nbsp;</a>
								|<a href="#" onclick="doclear('source')">&nbsp;清除&nbsp;</a>
							</div> -->
							<textarea title="输入源代码." class=java id=sourceCode style="width: 100%" name=sourceCode rows=6></textarea>
						</div>
						<div class="code-box">
							<div class="code-h">转换设置</div>
							<span class="options">选择语言:&nbsp;&nbsp;
								<select onchange="document.getElementById('sourceCode').className=this.value">
									<option value=java selected>java</option>
									<option value=xml>xml</option>
									<option value=sql>sql</option>
									<option value=jscript>jscript</option>
									<option value=groovy>groovy</option>
									<option value=css>css</option>
									<option value=cpp>cpp</option>
									<option value=c#>c#</option>
									<option value=python>python</option
									<option value=vb>vb</option>
									<option value=perl>perl</option>
									<option value=php>php</option>
									<option value=ruby>ruby</option>
									<option value=delphi>delphi</option>
								</select>
							</span>
							<span class="options">选项：&nbsp;
								<input id=showGutter type=checkbox checked> 显示行号
								<input id=firstLine type=checkbox checked> 起始为1
								<span class="options_no">
									<input id=showControls type=checkbox> 工具栏
									<input id=collapseAll type=checkbox> 折叠
									<input id=showColumns type=checkbox> 显示列数
								</span>
							</span>
							<span class="render">
								<button onclick=generateCode()>转&nbsp;&nbsp;换</button>
								<button onclick=clearText()>清&nbsp;&nbsp;除</button>
							</span>
						</div>
						<div class="code-box">
							<div class="code-h">HTML 代码</div>
							<p>在WordPress文本编辑模式，将下面代码复制粘贴进去</p>
							<textarea id=htmlCode style="width: 100%" name=htmlCode rows=6></textarea>
						</div>
						<div class="code-box">
							<div class="code-h">HTML 预览</div>
							<div id="preview"></div>
						</div>
					</div> <!-- .entry-code -->
				</div><!-- .entry-content -->
			</article><!-- #post-<?php the_ID(); ?> -->
		</main><!-- #main -->
		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		?>
	</div>
	<div class="h20"></div>
<?php
get_footer();