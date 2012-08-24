<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 12:42:33
         compiled from "/web/presta/modules/favoriteproducts/views/templates/hook/favoriteproducts-header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6537142544fe44c19cda123-67637415%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
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
  'nocache_hash' => '6537142544fe44c19cda123-67637415',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe44c19d39fa5_48049304',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe44c19d39fa5_48049304')) {function content_4fe44c19d39fa5_48049304($_smarty_tpl) {?>
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