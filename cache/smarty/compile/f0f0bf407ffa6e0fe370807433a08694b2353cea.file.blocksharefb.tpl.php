<?php /* Smarty version Smarty-3.1.8, created on 2012-06-26 23:36:21
         compiled from "/web/presta/modules/blocksharefb/blocksharefb.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18317487084fea2b55350414-47710267%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f0f0bf407ffa6e0fe370807433a08694b2353cea' => 
    array (
      0 => '/web/presta/modules/blocksharefb/blocksharefb.tpl',
      1 => 1334677800,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18317487084fea2b55350414-47710267',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product_link' => 0,
    'product_title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fea2b553f4997_98934522',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fea2b553f4997_98934522')) {function content_4fea2b553f4997_98934522($_smarty_tpl) {?>

<li id="left_share_fb">
	<a href="http://www.facebook.com/sharer.php?u=<?php echo $_smarty_tpl->tpl_vars['product_link']->value;?>
&amp;t=<?php echo $_smarty_tpl->tpl_vars['product_title']->value;?>
" class="js-new-window"><?php echo smartyTranslate(array('s'=>'Share on Facebook','mod'=>'blocksharefb'),$_smarty_tpl);?>
</a>
</li><?php }} ?>