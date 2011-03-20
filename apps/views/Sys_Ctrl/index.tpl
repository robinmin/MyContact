{literal}<style>
	body{padding: 0; margin: 0;} 
	body,html{height: 100%;} 
	#outer {height: 100%; overflow: hidden; position: relative;width: 100%;} 
	#outer[id] {display: table; position: static;} 
	#middle {position: absolute; top: 50%;} /* for explorer only*/ 
	#middle[id] {display: table-cell; vertical-align: middle; position: static;} 
	#inner {position: relative; top: -50%;width: 500px;margin: 0 auto;}
	#inner ul li{float:left;}
</style>{/literal}
<div id="outer"> 
	<div id="middle"> 
		<div id="inner">
			<ul style="list-style-type:none;">
				<li><a href="{$url_prefix}Sys_Ctrl/desktop/" title="myContact"><img alt="myContact" src="{$sys_asset}icon2/contact96.png" /></a></li>
				<li><a href="{$url_prefix}wordpress/" title="Blog"><span align="center"><img alt="Blog" src="{$sys_asset}icon2/blog96.png" /></li>
				<li><a href="{$url_prefix}Survey/" title="Survey"><span align="center"><img alt="Survey" src="{$sys_asset}icon2/survey96.png" /></li>
				<li><a href="{$url_prefix}gallery/" title="Gallery"><span align="center"><img alt="Gallery" src="{$sys_asset}icon2/gallery96.png" /></li>
				<li><a href="{$url_prefix}wiki/" title="Wiki"><span align="center"><img  alt="Wiki" src="{$sys_asset}icon2/wiki96.png" /></li>
			</ul>
			<div class="x-clean" />
		</div>
	</div>
</div>
