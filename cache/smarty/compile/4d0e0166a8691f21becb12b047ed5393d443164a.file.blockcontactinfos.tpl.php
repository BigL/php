<?php /* Smarty version Smarty-3.1.8, created on 2012-08-29 17:11:00
         compiled from "/web/presta/modules/blockcontactinfos/blockcontactinfos.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1902588162503e310481ad37-61945863%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4d0e0166a8691f21becb12b047ed5393d443164a' => 
    array (
      0 => '/web/presta/modules/blockcontactinfos/blockcontactinfos.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1902588162503e310481ad37-61945863',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'blockcontactinfos_company' => 0,
    'blockcontactinfos_address' => 0,
    'blockcontactinfos_phone' => 0,
    'blockcontactinfos_email' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_503e3104861868_54797187',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_503e3104861868_54797187')) {function content_503e3104861868_54797187($_smarty_tpl) {?><?php if (!is_callable('smarty_function_mailto')) include '/web/presta/tools/smarty/plugins/function.mailto.php';
?>

<!-- MODULE Block contact infos -->
<div id="block_contact_infos">
	<h4><?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcontactinfos'),$_smarty_tpl);?>
</h4>
	<ul>
		<?php if ($_smarty_tpl->tpl_vars['blockcontactinfos_company']->value!=''){?><li><strong><?php echo $_smarty_tpl->tpl_vars['blockcontactinfos_company']->value;?>
</strong></li><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['blockcontactinfos_address']->value!=''){?><li><pre><?php echo $_smarty_tpl->tpl_vars['blockcontactinfos_address']->value;?>
</pre></li><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['blockcontactinfos_phone']->value!=''){?><li><?php echo smartyTranslate(array('s'=>'Tel:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['blockcontactinfos_phone']->value;?>
</li><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['blockcontactinfos_email']->value!=''){?><li><?php echo smartyTranslate(array('s'=>'Email:'),$_smarty_tpl);?>
 <?php echo smarty_function_mailto(array('address'=>$_smarty_tpl->tpl_vars['blockcontactinfos_email']->value,'encode'=>"hex"),$_smarty_tpl);?>
</li><?php }?>
	</ul>
</div>
<!-- /MODULE Block contact infos -->
<?php }} ?>