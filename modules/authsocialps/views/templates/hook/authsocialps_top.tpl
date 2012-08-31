<!-- Block authsocialps -->
<div id="header_authsocial" style="float:right;clear:both;padding-top:10px;">
  <div id="fb-root"></div>
  {if !$logged}
    <div id="oauth_button" style="display:block;">
      <fb:login-button scope="user_about_me,user_photos,user_birthday,friends_photos,email" onlogin="clickFB();" show-faces="false" width="200" max-rows="1"></fb:login-button>
      
    </div>
  {else}
    <img src="http://graph.facebook.com/{fb_uid}/picture" />
  {/if}
</div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '{$appid}',
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
        document.location = "{$link->getModuleLink('authsocialps', 'facebook', [], true)}?callling_url={$come_from}&signed_request=" + response.authResponse.signedRequest  + '&accessToken=' + accessToken;
      } else if (response.status === 'not_authorized') {
        // the user is logged in to Facebook, 
        // but has not authenticated your app
      } else {
        // the user isn't logged in to Facebook.
      }
    });
    
  };
</script>


