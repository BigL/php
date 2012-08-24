<?php /* Smarty version Smarty-3.1.8, created on 2012-06-25 23:43:19
         compiled from "/web/presta/modules/authsocialps/views/templates/hook/authsocialps_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15009667264fe456f52014d7-07680209%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd869fb04ffd1ff0fea83909fa85cbc581e6d691c' => 
    array (
      0 => '/web/presta/modules/authsocialps/views/templates/hook/authsocialps_top.tpl',
      1 => 1340660586,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15009667264fe456f52014d7-07680209',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fe456f52628d9_05975742',
  'variables' => 
  array (
    'logged' => 0,
    'appid' => 0,
    'link' => 0,
    'come_from' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fe456f52628d9_05975742')) {function content_4fe456f52628d9_05975742($_smarty_tpl) {?><!-- Block authsocialps -->
<div id="header_authsocial" style="float:right;clear:both;padding-top:10px;">
	  	
	  	
	      <?php if (!$_smarty_tpl->tpl_vars['logged']->value){?>
		
				<div id="oauth_button" style="display:block;">
					<fb:login-button scope="user_about_me,user_photos,user_birthday,friends_photos,email" onlogin="clickFB();"/><?php echo smartyTranslate(array('s'=>'Connect','mod'=>'authsocialps'),$_smarty_tpl);?>
</fb:login-button>
				</div>
		  <?php }?>
</div>
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
    	FB.init({
			appId		: '<?php echo $_smarty_tpl->tpl_vars['appid']->value;?>
',
			channelUrl	: 'http://presta.localhost/channel.html',
			status		: true, 
			cookie		: true,
			xfbml		: true,
			oauth		: true

		     
		});

		FB.getLoginStatus(function(response) {
				if (response.status === 'connected') {
					$("#oauth_button").hide();
					$("#presta_login").show();
				}else{
					$("#oauth_button").show();
					$("#presta_login").hide();
				}
		 }, true);
	}
	// Load the SDK Asynchronously
	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  // js.src = "//connect.facebook.net/en_US/all.js";
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $_smarty_tpl->tpl_vars['appid']->value;?>
";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk')); 

 

	
	function clickFB() 
	{	
		FB.getLoginStatus(function(response) {

		  if (response.status === 'connected') {
		    // the user is logged in and has authenticated your
		    // app, and response.authResponse supplies
		    // the user's ID, a valid access token, a signed
		    // request, and the time the access token 
		    // and signed request each expire
		    var uid = response.authResponse.userID;
		    var accessToken = response.authResponse.accessToken;
		    console.log(response);
		    document.location = "<?php echo $_smarty_tpl->tpl_vars['link']->value->getModuleLink('authsocialps','facebook',array(),true);?>
?callling_url=<?php echo $_smarty_tpl->tpl_vars['come_from']->value;?>
&signed_request=" + response.authResponse.signedRequest  + '&accessToken=' + accessToken;
		  } else if (response.status === 'not_authorized') {
		    // the user is logged in to Facebook, 
		    // but has not authenticated your app
		    console.log('Not authorized into Facebook');
		  } else {
		    // the user isn't logged in to Facebook.
		    console.log('Not Logged into Facebook');
		  }
		 }, true);
	};
</script>
<?php }} ?>