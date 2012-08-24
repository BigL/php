<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 13:05:35
         compiled from "/web/presta/themes/default/category-count.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11692412644fe4517f7fe082-91568026%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e58934aae2295a9360a17c79351409073b4767ed' => 
    array (
      0 => '/web/presta/themes/default/category-count.tpl',
      1 => 1336746324,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11692412644fe4517f7fe082-91568026',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
    'nb_products' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe4517f824f89_23533592',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe4517f824f89_23533592')) {function content_4fe4517f824f89_23533592($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['category']->value->id==1||$_smarty_tpl->tpl_vars['nb_products']->value==0){?>
	<?php echo smartyTranslate(array('s'=>'There are no products.'),$_smarty_tpl);?>

<?php }else{ ?>
	<?php if ($_smarty_tpl->tpl_vars['nb_products']->value==1){?>
		<?php echo smartyTranslate(array('s'=>'There is %d product.','sprintf'=>$_smarty_tpl->tpl_vars['nb_products']->value),$_smarty_tpl);?>

	<?php }else{ ?>
		<?php echo smartyTranslate(array('s'=>'There are %d products.','sprintf'=>$_smarty_tpl->tpl_vars['nb_products']->value),$_smarty_tpl);?>

	<?php }?>
<?php }?><?php }} ?>