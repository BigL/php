<?php /* Smarty version Smarty-3.1.8, created on 2012-08-29 17:10:59
         compiled from "/web/presta/modules/authsocialps/views/templates/hook/authsocialps_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:414846757503e3103ca0865-90949286%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd869fb04ffd1ff0fea83909fa85cbc581e6d691c' => 
    array (
      0 => '/web/presta/modules/authsocialps/views/templates/hook/authsocialps_top.tpl',
      1 => 1346251612,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '414846757503e3103ca0865-90949286',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logged' => 0,
    'appid' => 0,
    'link' => 0,
    'come_from' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_503e3103cca3a1_55476276',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_503e3103cca3a1_55476276')) {function content_503e3103cca3a1_55476276($_smarty_tpl) {?><!-- Block authsocialps -->
<div id="header_authsocial" style="float:right;clear:both;padding-top:10px;">
        <div id="fb-root"></div>
        <?php if (!$_smarty_tpl->tpl_vars['logged']->value){?>
        <div id="oauth_button" style="display:block;">
          <fb:login-button scope="user_about_me,user_photos,user_birthday,friends_photos,email" onlogin="clickFB();" show-faces="false" width="200" max-rows="1"></fb:login-button>
          
        </div>
      <?php }?>
</div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo $_smarty_tpl->tpl_vars['appid']->value;?>
',
      channelUrl : 'http://presta.localhost/channel.html',
      status     : true,
      cookie     : true,
      xfbml      : true,
      oauth      : true
    });

    // Additional initialization code here
    /*FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
          $("#oauth_button").hide();
          $("#presta_login").show();
        }else{
          $("#oauth_button").show();
          $("#presta_login").hide();
        }
     }, true);    
    */
    console.log('Not authorized into Facebook');
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) return;
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));

  function clickFB() 
  {
    
    FB.getLoginStatus(function(response) 
    {
      if (response.status === 'connected') {
        console.log(response);

        var uid = response.authResponse.userID;
        var accessToken = response.authResponse.accessToken;
        document.location = "<?php echo $_smarty_tpl->tpl_vars['link']->value->getModuleLink('authsocialps','facebook',array(),true);?>
?callling_url=<?php echo $_smarty_tpl->tpl_vars['come_from']->value;?>
&signed_request=" + response.authResponse.signedRequest  + '&accessToken=' + accessToken;
      } else if (response.status === 'not_authorized') {
        // the user is logged in to Facebook, 
        // but has not authenticated your app
      } else {
        // the user isn't logged in to Facebook.
      }
    });
    
  };
</script>


<?php }} ?>