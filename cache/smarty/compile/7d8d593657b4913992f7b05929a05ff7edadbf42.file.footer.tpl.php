<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 12:42:35
         compiled from "/web/presta/themes/default/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1530868144fe44c1b361ce0-14712667%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7d8d593657b4913992f7b05929a05ff7edadbf42' => 
    array (
      0 => '/web/presta/themes/default/footer.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1530868144fe44c1b361ce0-14712667',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content_only' => 0,
    'HOOK_RIGHT_COLUMN' => 0,
    'HOOK_FOOTER' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe44c1b370181_24749095',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe44c1b370181_24749095')) {function content_4fe44c1b370181_24749095($_smarty_tpl) {?>

		<?php if (!$_smarty_tpl->tpl_vars['content_only']->value){?>
				</div>

<!-- Right -->
				<div id="right_column" class="column grid_2 omega">
					<?php echo $_smarty_tpl->tpl_vars['HOOK_RIGHT_COLUMN']->value;?>

				</div>
			</div>

<!-- Footer -->
			<div id="footer" class="grid_9 alpha omega clearfix"><?php echo $_smarty_tpl->tpl_vars['HOOK_FOOTER']->value;?>
</div>
		</div>
	<?php }?>
	</body>
</html>
<?php }} ?>