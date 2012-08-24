<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 13:35:13
         compiled from "/web/presta/admin75149/themes/default/template/controllers/stats/menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4026041974fe45871ba11b4-74290153%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0167f78b735f3d452c6e5c3d331d2e422998c14c' => 
    array (
      0 => '/web/presta/admin75149/themes/default/template/controllers/stats/menu.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4026041974fe45871ba11b4-74290153',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'modules' => 0,
    'module' => 0,
    'module_instance' => 0,
    'current' => 0,
    'token' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe45871bd8492_66482655',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe45871bd8492_66482655')) {function content_4fe45871bd8492_66482655($_smarty_tpl) {?>
<div id="container">
	<div class="sidebar navigation">
	<h3><?php echo smartyTranslate(array('s'=>'Navigation'),$_smarty_tpl);?>
</h3>
		<ul class="categorieList">
		<?php if (count($_smarty_tpl->tpl_vars['modules']->value)){?>
			<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['module_instance']->value[$_smarty_tpl->tpl_vars['module']->value['name']]){?>
					<li>
						<a href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&module=<?php echo $_smarty_tpl->tpl_vars['module']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_instance']->value[$_smarty_tpl->tpl_vars['module']->value['name']]->displayName;?>
</a>
					</li>
				<?php }?>
			<?php } ?>
		<?php }else{ ?>
			<?php echo smartyTranslate(array('s'=>'No module installed'),$_smarty_tpl);?>

		<?php }?>
		</ul>



<?php }} ?>