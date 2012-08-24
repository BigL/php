<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 12:47:48
         compiled from "/web/presta/modules/favoriteproducts/views/templates/hook/my-account.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5048392984fe44d541d8053-81719685%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f2b008a06d3efea3c8a9d28b015691de8dbc777b' => 
    array (
      0 => '/web/presta/modules/favoriteproducts/views/templates/hook/my-account.tpl',
      1 => 1337432012,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5048392984fe44d541d8053-81719685',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'in_footer' => 0,
    'module_template_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe44d54234946_32967559',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe44d54234946_32967559')) {function content_4fe44d54234946_32967559($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/web/presta/tools/smarty/plugins/modifier.escape.php';
?>

<li class="favorite products">
	<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getModuleLink('favoriteproducts','account'), 'htmlall', 'UTF-8');?>
" title="<?php echo smartyTranslate(array('s'=>'My favorite products','mod'=>'favoriteproducts'),$_smarty_tpl);?>
">
		<?php if (!$_smarty_tpl->tpl_vars['in_footer']->value){?><img src="<?php echo $_smarty_tpl->tpl_vars['module_template_dir']->value;?>
img/favorites.png" class="icon" /><?php }?>
		<?php echo smartyTranslate(array('s'=>'My favorite products','mod'=>'favoriteproducts'),$_smarty_tpl);?>

	</a>
</li>
<?php }} ?>