<?php /*%%SmartyHeaderCode:7505721134fe44c1ad8b0e6-76184076%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3d0ef1ce3139b6341037e80daef7e50c45830a74' => 
    array (
      0 => '/web/presta/modules/blockcategories/blockcategories_footer.tpl',
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
  'nocache_hash' => '7505721134fe44c1ad8b0e6-76184076',
  'variables' => 
  array (
    'widthColumn' => 0,
    'isDhtml' => 0,
    'blockCategTree' => 0,
    'child' => 0,
    'numberColumn' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe44c1ade0357_70665498',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe44c1ade0357_70665498')) {function content_4fe44c1ade0357_70665498($_smarty_tpl) {?>
<!-- Block categories module -->
<div class="blockcategories_footer">
	<h4>Categories</h4>
<div class="category_footer" style="float:left;clear:none;width:100%">
	<div style="float:left" class="list">
		<ul class="tree dhtml">
	
									
<li >
	<a href="http://presta.localhost/index.php?id_category=3&amp;controller=category"  title="Now that you can buy movies from the iTunes Store and sync them to your iPod, the whole world is your theater.">iPods</a>
	</li>

					
													
<li >
	<a href="http://presta.localhost/index.php?id_category=4&amp;controller=category"  title="Wonderful accessories for your iPod">Accessories</a>
	</li>

					
													
<li class="last">
	<a href="http://presta.localhost/index.php?id_category=5&amp;controller=category"  title="The latest Intel processor, a bigger hard drive, plenty of memory, and even more new features all fit inside just one liberating inch. The new Mac laptops have the performance, power, and connectivity of a desktop computer. Without the desk part.">Laptops</a>
	</li>

					
								</ul>
	</div>
</div>
<br class="clear"/>
</div>
<!-- /Block categories module -->
<?php }} ?>