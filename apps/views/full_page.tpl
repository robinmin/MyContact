<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset={$sys_encoding}" />
	<title>{$sys_title}</title>
	<base href='{$sys_base_url}' />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

{foreach from=$sys_css item=tmp_css}{if $tmp_css ne ''}	<link rel="stylesheet" type="text/css" href="{$tmp_css}" />
{/if}{/foreach}
{if $this_css ne ''}	<style type="text/css"><!--/*--><![CDATA[/*><!-- */
{$this_css}
/*]]>*/--></style>{/if}

{foreach from=$sys_js item=tmp_js}{if $tmp_js ne ''}	<script type="text/javascript" src="{$tmp_js}"></script>
{/if}{/foreach}
{if $this_js ne ''}	<script type="text/javascript"><!--//--><![CDATA[//><!--
{$this_js}
/*]]>*/--></script>{/if}
</head><body {if !empty($sys_body_ext)}$sys_body_ext{/if}><span style='display:none'>Version : {$sys_version}</span>
{if $this_body ne ''}{$this_body}{/if}
</body></html>