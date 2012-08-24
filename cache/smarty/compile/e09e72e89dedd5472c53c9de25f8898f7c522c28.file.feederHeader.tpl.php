<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 12:42:33
         compiled from "/web/presta/modules/feeder/feederHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12387967734fe44c19d520f1-98108410%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e09e72e89dedd5472c53c9de25f8898f7c522c28' => 
    array (
      0 => '/web/presta/modules/feeder/feederHeader.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12387967734fe44c19d520f1-98108410',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'meta_title' => 0,
    'feedUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe44c19d600e1_88290662',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe44c19d600e1_88290662')) {function content_4fe44c19d600e1_88290662($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/web/presta/tools/smarty/plugins/modifier.escape.php';
?>

<link rel="alternate" type="application/rss+xml" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['meta_title']->value, 'html', 'UTF-8');?>
" href="<?php echo $_smarty_tpl->tpl_vars['feedUrl']->value;?>
" /><?php }} ?>