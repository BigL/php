<?php /* Smarty version Smarty-3.1.8, created on 2012-06-22 12:42:34
         compiled from "/web/presta/modules/blocksocial/blocksocial.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7463629204fe44c1ade9b20-48996371%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dc49589a7eb52126cd23f4da67f679612e085166' => 
    array (
      0 => '/web/presta/modules/blocksocial/blocksocial.tpl',
      1 => 1330011190,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7463629204fe44c1ade9b20-48996371',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'facebook_url' => 0,
    'twitter_url' => 0,
    'rss_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe44c1ae1bfb4_79623601',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe44c1ae1bfb4_79623601')) {function content_4fe44c1ae1bfb4_79623601($_smarty_tpl) {?>

<div id="social_block">
	<h4><?php echo smartyTranslate(array('s'=>'Follow us','mod'=>'blocksocial'),$_smarty_tpl);?>
</h4>
	<ul>
		<?php if ($_smarty_tpl->tpl_vars['facebook_url']->value!=''){?><li class="facebook"><a href="<?php echo $_smarty_tpl->tpl_vars['facebook_url']->value;?>
"><?php echo smartyTranslate(array('s'=>'Facebook','mod'=>'blocksocial'),$_smarty_tpl);?>
</a></li><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['twitter_url']->value!=''){?><li class="twitter"><a href="<?php echo $_smarty_tpl->tpl_vars['twitter_url']->value;?>
"><?php echo smartyTranslate(array('s'=>'Twitter','mod'=>'blocksocial'),$_smarty_tpl);?>
</a></li><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['rss_url']->value!=''){?><li class="rss"><a href="<?php echo $_smarty_tpl->tpl_vars['rss_url']->value;?>
"><?php echo smartyTranslate(array('s'=>'RSS','mod'=>'blocksocial'),$_smarty_tpl);?>
</a></li><?php }?>
	</ul>
</div><?php }} ?>