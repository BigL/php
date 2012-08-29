<?php /* Smarty version Smarty-3.1.8, created on 2012-08-29 17:10:59
         compiled from "/web/presta/modules/favoriteproducts/views/templates/hook/favoriteproducts-header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:592306056503e31039855b7-04539516%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1986f0333c6cf22e8aa1eb6daef676b05a5af05a' => 
    array (
      0 => '/web/presta/modules/favoriteproducts/views/templates/hook/favoriteproducts-header.tpl',
      1 => 1337174344,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '592306056503e31039855b7-04539516',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_503e31039dd1c9_01091754',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_503e31039dd1c9_01091754')) {function content_503e31039dd1c9_01091754($_smarty_tpl) {?>
<script type="text/javascript">
	var favorite_products_url_add = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getModuleLink('favoriteproducts','actions',array('process'=>'add'),true);?>
';
	var favorite_products_url_remove = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getModuleLink('favoriteproducts','actions',array('process'=>'remove'),true);?>
';
<?php if (isset($_GET['id_product'])){?>
	var favorite_products_id_product = '<?php echo intval($_GET['id_product']);?>
';
<?php }?> 
</script>
<?php }} ?>