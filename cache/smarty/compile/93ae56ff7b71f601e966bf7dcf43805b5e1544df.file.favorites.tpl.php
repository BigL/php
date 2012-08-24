<?php /* Smarty version Smarty-3.1.8, created on 2012-06-26 23:59:31
         compiled from "/web/presta/admin75149/themes/default/template/controllers/modules/favorites.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8905155484fea30c30505a8-96308721%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '93ae56ff7b71f601e966bf7dcf43805b5e1544df' => 
    array (
      0 => '/web/presta/admin75149/themes/default/template/controllers/modules/favorites.tpl',
      1 => 1337460904,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8905155484fea30c30505a8-96308721',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'modules' => 0,
    'km' => 0,
    'module' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fea30c31bb967_72617994',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fea30c31bb967_72617994')) {function content_4fea30c31bb967_72617994($_smarty_tpl) {?><div id="productBox">

	<?php echo $_smarty_tpl->getSubTemplate ('controllers/modules/header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<ul class="view-modules">
		<li class="button normal-view"><a href="index.php?controller=<?php echo htmlentities($_GET['controller']);?>
&token=<?php echo htmlentities($_GET['token']);?>
"><img src="themes/default/img/modules_view_layout_sidebar.png" alt="<?php echo smartyTranslate(array('s'=>'Normal view'),$_smarty_tpl);?>
" border="0" /><span><?php echo smartyTranslate(array('s'=>'Normal view'),$_smarty_tpl);?>
</span></a></li>
		<li class="button favorites-view-disabled"><img src="themes/default/img/modules_view_table_select_row.png" alt="<?php echo smartyTranslate(array('s'=>'Favorites view'),$_smarty_tpl);?>
" border="0" /><span><?php echo smartyTranslate(array('s'=>'Favorites view'),$_smarty_tpl);?>
</span></li>
	</ul>


	<div id="container">

		<div id="moduleContainer" style="padding:0px;margin:0px;padding-top:15px">

			<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table" id="">
				<col width="30px">
				<col width="240px">
				<col width="">
				<col width="140px">
				<col width="180px">
				<col width="70px">
				<col width="70px">
				<col width="130px">
				</colgroup>
				<thead>
					<tr class="nodrag nodrop">
						<th class="center"><?php echo smartyTranslate(array('s'=>'Logo'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Module Name'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Description'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Categories'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Interest'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Favorite'),$_smarty_tpl);?>
</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_smarty_tpl->tpl_vars['km'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['km']->value = $_smarty_tpl->tpl_vars['module']->key;
?>
					<tr height="32" <?php if ($_smarty_tpl->tpl_vars['km']->value%2==0){?> class="alt_row"<?php }?>>
						<td><img src="<?php if (isset($_smarty_tpl->tpl_vars['module']->value->image)){?><?php echo $_smarty_tpl->tpl_vars['module']->value->image;?>
<?php }else{ ?>../modules/<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
/<?php echo $_smarty_tpl->tpl_vars['module']->value->logo;?>
<?php }?>" width="16" height="16" /></td>
						<td><span class="moduleName"><?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>
</span></td>
						<td><span class="moduleFavDesc"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['module']->value->description,80,'…');?>
</span></td>
						<td><?php if (isset($_smarty_tpl->tpl_vars['module']->value->id)&&$_smarty_tpl->tpl_vars['module']->value->id>0){?><span class="setup"><?php echo smartyTranslate(array('s'=>'Installed'),$_smarty_tpl);?>
</span><?php }else{ ?><span class="setup non-install"><?php echo smartyTranslate(array('s'=>'Not Installed'),$_smarty_tpl);?>
</span><?php }?></td>
						<td><?php echo $_smarty_tpl->tpl_vars['module']->value->categoryName;?>
</td>
						<td>
						<select name="i_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" class="moduleFavorite" style="width:50px">
							<option value="" selected="selected">---</option>
							<option value="1" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['interest'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['interest']=='1'){?>selected="selected"<?php }?>>Yes</option>
							<option value="0" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['interest'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['interest']=='0'){?>selected="selected"<?php }?>>No</option>
						</select>
						</td>
						<td>
						<select name="f_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" class="moduleFavorite" style="width:50px">
							<option value="" selected="selected">---</option>
							<option value="1" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['favorite'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['favorite']=='1'){?>selected="selected"<?php }?>>Yes</option>
							<option value="0" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['favorite'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['favorite']=='0'){?>selected="selected"<?php }?>>No</option>
						</select>
						</td>
						<td id="r_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
">&nbsp;</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

		</div>
	</div>
</div><?php }} ?>