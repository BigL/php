<?php /*%%SmartyHeaderCode:11734868474fe44c1a4c73d3-86703669%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b7cdef2c2278e0ae058a81f60e7cb3940e0f3a26' => 
    array (
      0 => '/web/presta/modules/blockcategories/blockcategories.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
    '1d10e38cf60d0bdc287d9e70d500bed56422fb90' => 
    array (
      0 => '/web/presta/modules/blockcategories/category-tree-branch.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11734868474fe44c1a4c73d3-86703669',
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe9e251de9655_73640846',
  'has_nocache_code' => false,
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe9e251de9655_73640846')) {function content_4fe9e251de9655_73640846($_smarty_tpl) {?>
<!-- Block categories module -->
<div id="categories_block_left" class="block">
	<h4>Categories</h4>
	<div class="block_content">
		<ul class="tree dhtml">
									
<li >
	<a href="http://presta.localhost/photobook/3-music-ipods"  title="Now that you can buy movies from the iTunes Store and sync them to your iPod, the whole world is your theater.">iPods</a>
	</li>

												
<li >
	<a href="http://presta.localhost/photobook/4-accessories-ipod"  title="Wonderful accessories for your iPod">Accessories</a>
	</li>

												
<li class="last">
	<a href="http://presta.localhost/photobook/5-laptops"  title="The latest Intel processor, a bigger hard drive, plenty of memory, and even more new features all fit inside just one liberating inch. The new Mac laptops have the performance, power, and connectivity of a desktop computer. Without the desk part.">Laptops</a>
	</li>

							</ul>
		
		<script type="text/javascript">
		// <![CDATA[
			// we hide the tree only if JavaScript is activated
			$('div#categories_block_left ul.dhtml').hide();
		// ]]>
		</script>
	</div>
</div>
<!-- /Block categories module -->
<?php }} ?>