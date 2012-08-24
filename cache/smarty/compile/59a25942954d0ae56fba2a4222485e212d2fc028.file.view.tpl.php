<?php /* Smarty version Smarty-3.1.8, created on 2012-06-25 13:14:50
         compiled from "/web/presta/admin75149/themes/default/template/controllers/search/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17934229544fe8482a68b4b1-65586585%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '59a25942954d0ae56fba2a4222485e212d2fc028' => 
    array (
      0 => '/web/presta/admin75149/themes/default/template/controllers/search/helpers/view/view.tpl',
      1 => 1334219678,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17934229544fe8482a68b4b1-65586585',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'query' => 0,
    'show_toolbar' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'features' => 0,
    'feature' => 0,
    'key' => 0,
    'val' => 0,
    'categories' => 0,
    'category' => 0,
    'products' => 0,
    'customers' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe8482a855311_13910301',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe8482a855311_13910301')) {function content_4fe8482a855311_13910301($_smarty_tpl) {?>

<script type="text/javascript">
$(function() {
	$('body').highlight('<?php echo $_smarty_tpl->tpl_vars['query']->value;?>
');
});
</script>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

	<div class="leadin"></div>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['features']->value)){?>
	<?php if (!$_smarty_tpl->tpl_vars['features']->value){?>
		<h3><?php echo smartyTranslate(array('s'=>'No features matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
	<?php }else{ ?>
		<h3><?php echo smartyTranslate(array('s'=>'Features matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
		<table class="table" cellpadding="0" cellspacing="0">
			<?php  $_smarty_tpl->tpl_vars['feature'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['feature']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['features']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['feature']->key => $_smarty_tpl->tpl_vars['feature']->value){
$_smarty_tpl->tpl_vars['feature']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['feature']->key;
?>
				<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['feature']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['val']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value){
$_smarty_tpl->tpl_vars['val']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['val']->key;
 $_smarty_tpl->tpl_vars['val']->index++;
 $_smarty_tpl->tpl_vars['val']->first = $_smarty_tpl->tpl_vars['val']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['feature_list']['first'] = $_smarty_tpl->tpl_vars['val']->first;
?>
					<tr>
						<th><?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['feature_list']['first']){?><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php }?></th>
						<td>
							<a href="<?php echo $_smarty_tpl->tpl_vars['val']->value['link'];?>
"><?php echo $_smarty_tpl->tpl_vars['val']->value['value'];?>
</a>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>
		<div class="clear">&nbsp;</div>
	<?php }?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['categories']->value)){?>
	<?php if (!$_smarty_tpl->tpl_vars['categories']->value){?>
		<h3><?php echo smartyTranslate(array('s'=>'No categories matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
	<?php }else{ ?>
		<h3><?php echo smartyTranslate(array('s'=>'Categories matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
		<table cellspacing="0" cellpadding="0" class="table">
			<?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value){
$_smarty_tpl->tpl_vars['category']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['category']->key;
?>
				<tr class="alt_row">
					<td><?php echo $_smarty_tpl->tpl_vars['category']->value;?>
</td>
				</tr>
			<?php } ?>
		</table>
		<div class="clear">&nbsp;</div>
	<?php }?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['products']->value)){?>
	<?php if (!$_smarty_tpl->tpl_vars['products']->value){?>
		<h3><?php echo smartyTranslate(array('s'=>'No products matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
	<?php }else{ ?>
		<h3><?php echo smartyTranslate(array('s'=>'Products matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
		<?php echo $_smarty_tpl->tpl_vars['products']->value;?>

	<?php }?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['customers']->value)){?>
	<?php if (!$_smarty_tpl->tpl_vars['customers']->value){?>
		<h3><?php echo smartyTranslate(array('s'=>'No customers matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
	<?php }else{ ?>
		<h3><?php echo smartyTranslate(array('s'=>'Customer matching your query'),$_smarty_tpl);?>
 : <?php echo $_smarty_tpl->tpl_vars['query']->value;?>
</h3>
		<?php echo $_smarty_tpl->tpl_vars['customers']->value;?>

	<?php }?>
<?php }?><?php }} ?>