<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 12:42:34
         compiled from "/web/presta/modules/blocktopmenu/blocktopmenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2444452284fe44c1a071611-39035702%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3552ea01393d0482cc405124a216e8b503952bea' => 
    array (
      0 => '/web/presta/modules/blocktopmenu/blocktopmenu.tpl',
      1 => 1338999768,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2444452284fe44c1a071611-39035702',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MENU' => 0,
    'MENU_SEARCH' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe44c1a092a52_91600889',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe44c1a092a52_91600889')) {function content_4fe44c1a092a52_91600889($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/web/presta/tools/smarty/plugins/modifier.escape.php';
?><?php if ($_smarty_tpl->tpl_vars['MENU']->value!=''){?>
	</div>

	<!-- Menu -->
	<div class="sf-contener clearfix">
		<ul class="sf-menu clearfix">
			<?php echo $_smarty_tpl->tpl_vars['MENU']->value;?>

			<?php if ($_smarty_tpl->tpl_vars['MENU_SEARCH']->value){?>
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="search.php" method="get">
						<input type="hidden" value="position" name="orderby"/>
						<input type="hidden" value="desc" name="orderway"/>
						<input type="text" name="search_query" value="<?php if (isset($_GET['search_query'])){?><?php echo smarty_modifier_escape($_GET['search_query'], 'htmlall', 'UTF-8');?>
<?php }?>" />
					</form>
				</li>
			<?php }?>
		</ul>
		<div class="sf-right">&nbsp;</div>

	<!--/ Menu -->
<?php }?><?php }} ?>