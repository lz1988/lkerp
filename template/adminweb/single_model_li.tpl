<div class="contents_body_height_li">
	<!--{if ($datalist)}-->
	<ul>
		<!--{foreach ($datalist as $key=>$r)}-->
		<li>
			<!--{for ($i=0;$i<count($displaykey);$i++)}-->
			<span style="<!--{echo $r[$displaykey[$i]]['style']}-->"><!--{echo $r[$displaykey[$i]]['text']}--></span>
			<!--{/for}-->			
		</li>			
		<!--{/for}-->
	</ul>
	<!--{else}-->
	<ul>
		<li>
			暂未发布公告！！！
		</li>			
	</ul>
	<!--{/if}-->
	<div class="contents_body_height_li_more"><!--{echo $more;}--></div>
</div>
