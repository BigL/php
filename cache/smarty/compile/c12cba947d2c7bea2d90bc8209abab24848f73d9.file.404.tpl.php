<?php /* Smarty version Smarty-3.1.8, created on 2012-06-26 23:35:23
         compiled from "/web/presta/themes/default/404.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4940803854fea2b1b363d33-24625560%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c12cba947d2c7bea2d90bc8209abab24848f73d9' => 
    array (
      0 => '/web/presta/themes/default/404.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4940803854fea2b1b363d33-24625560',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'base_dir' => 0,
    'img_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fea2b1b3f1d86_09707668',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fea2b1b3f1d86_09707668')) {function content_4fea2b1b3f1d86_09707668($_smarty_tpl) {?>
<div class="pagenotfound">
	<h1><?php echo smartyTranslate(array('s'=>'Page not available'),$_smarty_tpl);?>
</h1>

	<p>
		<?php echo smartyTranslate(array('s'=>'We\'re sorry, but the Web address you entered is no longer available'),$_smarty_tpl);?>

	</p>

	<h3><?php echo smartyTranslate(array('s'=>'To find a product, please type its name in the field below'),$_smarty_tpl);?>
</h3>
	<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('search.php');?>
" method="post" class="std">
		<fieldset>
			<p>
				<label for="search"><?php echo smartyTranslate(array('s'=>'Search our product catalog:'),$_smarty_tpl);?>
</label>
				<input id="search_query" name="search_query" type="text" />
				<input type="submit" name="Submit" value="OK" class="button_small" />
			</p>
		</fieldset>
	</form>

	<p><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" title="<?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif" alt="<?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
" class="icon" /> <?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></p>
</div><?php }} ?>