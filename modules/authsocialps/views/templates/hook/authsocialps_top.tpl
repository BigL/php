<!-- Block authsocialps -->
<div id="header_authsocial" style="float:right;clear:both;padding-top:10px;">
	  	
	  	
	      {if !$logged}
		
				<div id="oauth_button" style="display:block;">
					<fb:login-button scope="user_about_me,user_photos,user_birthday,friends_photos,email" onlogin="clickFB();"/>{l s='Connect' mod='authsocialps'}</fb:login-button>
				</div>
		  {/if}
</div>
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
    	FB.init({
			appId		: '{$appid}',
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
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId={$appid}";
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
		    document.location = "{$link->getModuleLink('authsocialps', 'facebook', [], true)}?callling_url={$come_from}&signed_request=" + response.authResponse.signedRequest  + '&accessToken=' + accessToken;
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
