<?php /* Smarty version Smarty-3.1.8, created on 2012-06-26 23:23:45
         compiled from "/web/presta/admin75149/themes/default/template/controllers/products/multishop/check_fields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15233502394fea2861e5fbe6-94928940%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c195ca4a7d47d5e142a448aef4acb71cec74c933' => 
    array (
      0 => '/web/presta/admin75149/themes/default/template/controllers/products/multishop/check_fields.tpl',
      1 => 1336637232,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15233502394fea2861e5fbe6-94928940',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'display_multishop_checkboxes' => 0,
    'product_tab' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fea2861ea57b8_43475291',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fea2861ea57b8_43475291')) {function content_4fea2861ea57b8_43475291($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value)&&$_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value){?>
	<label style="float: none">
		<input type="checkbox" style="vertical-align: text-bottom" onclick="$('#product-tab-content-<?php echo $_smarty_tpl->tpl_vars['product_tab']->value;?>
 input[name^=\'multishop_check[\']').attr('checked', this.checked); ProductMultishop.checkAll<?php echo $_smarty_tpl->tpl_vars['product_tab']->value;?>
()" />
		<?php echo smartyTranslate(array('s'=>'Check / uncheck all (you are editing this page for several shops, some fields like "name" or "price" are disabled, you have to check these fields in order to edit them for these shops)'),$_smarty_tpl);?>

	</label>
<?php }?><?php }} ?>