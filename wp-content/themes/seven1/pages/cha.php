<?php
/*
* Template name:cha
*/
get_header();

?>
<style>
.idlist{
	margin-right:10px;
	border:1px solid #ccc;
	position: relative;
	display: inline-block;
    margin-bottom: 10px;
}
.idlist b{
	position: absolute;
	top:-5px;
	right:-5px;
	
}
</style>
<div id="primary" class="content-area w100">
	<main id="cha" class="site-main box page entry-content">
		<div class="pd20">
			<h2>输入ID</h2>
			<p><input type="text" class="pd10" v-model="id"/></p>
			<p class="mar20-b"><button @click="addNew()">添加ID</button></p>
			<div class="mar20-b"></div>
			<p><span v-for="(id,index) in ids" class="pd10 idlist">{{id}}<b @click="remove(id,index)" class="mouh">x</b></span></p>
		</div>
		<div class="pd20">
			<ul>
				<li v-for="item in list" v-html="item['html']+'<p>'+item['id']+'</p>'"></li>
			</ul>
		</div>
		<div class="pd20">
			<button @click="search()">{{searchLocked ? '查询中，请稍后...' : '查询'}}</button>
		</div>
	</main>
</div>
<?php
get_footer();