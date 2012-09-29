
<!-- Block authsocialps -->
<div id="header_authsocial" style="float:right;clear:both;padding-top:10px;">
	  	
  	<script src="//connect.facebook.net/en_US/all.js"></script>
  	
  	{if (!$logged)  }
	  	{if ( $show_fb_connect )}

			<div id="oauth_button" style="display:block;">
				<p> {l s='We need you to authenticate with Facebook so that we can fetch your details to create your product, thank you' mod='authsocial'}</p> 

				<fb:login-button class="fb_button_medium" scope="user_about_me,user_photos,user_birthday,friends_photos,email" onlogin="clickFB();" show-faces="false" width="200" max-rows="1">{$fbButtonString}</fb:login-button>
			</div>
		{/if}
	{/if}
</div>	

<div id="fb-root"></div>        
<script>
	$(document).ready(function() {

		{if (!$logged )}
			
			{if ( $show_fb_connect )}
				$('#oauth_button').dialog({  position: "center", width:460, modal: true,title:"{l s='please use facebook to log in'}" });
				$('#oauth_button').slideDown("fast");
			{/if}
		{/if}
	});
	window.fbAsyncInit = function() {
		FB.init({
		    appId      : '{$appid}',
		    status     : true,
		    cookie     : true,
		    xfbml      : true,
		    oauth      : true,
		});

		FB.getLoginStatus(function(response) {
				if (response.status === 'connected') {
					// $("#oauth_button").hide();
					// $("#presta_login").show();
				}else{
					// $("#oauth_button").show();
					// $("#presta_login").hide();
				}
		 }, true);
	}

	
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
		   
		    document.location = "{$link->getModuleLink('authsocialps', 'facebook', [], true)}?signed_request=" + response.authResponse.signedRequest + '&accessToken=' + accessToken + '&callling_url={$come_from}';
		  } else if (response.status === 'not_authorized') {
		    // the user is logged in to Facebook, 
		    // but has not authenticated your app
		  } else {
		    // the user isn't logged in to Facebook.
		  }
		});
	};
</script>
