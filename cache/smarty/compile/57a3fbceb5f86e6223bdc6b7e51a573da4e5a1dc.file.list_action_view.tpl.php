<?php /* Smarty version Smarty-3.1.8, created on 2012-08-29 17:09:48
         compiled from "/web/presta/admin75149/themes/default/template/helpers/list/list_action_view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1208854542503e30bc3125e1-79725603%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '57a3fbceb5f86e6223bdc6b7e51a573da4e5a1dc' => 
    array (
      0 => '/web/presta/admin75149/themes/default/template/helpers/list/list_action_view.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1208854542503e30bc3125e1-79725603',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_503e30bc31e0e5_69351966',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_503e30bc31e0e5_69351966')) {function content_503e30bc31e0e5_69351966($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" >
	<img src="../img/admin/details.gif" alt="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" />
</a><?php }} ?>