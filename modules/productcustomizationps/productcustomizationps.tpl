 
<style type="text/css" media="screen">
    object:focus { outline:none; }
</style>

<script type="text/javascript"
    src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script type="text/javascript" src="/js/lib/flash_detect_min.js"></script>
 <div id="ig-root"></div>
<script type="text/javascript">
    $(document).ready(function() {

        embedSwf();

        function embedSwf(){ 
                   
            // These flashvars pass various pieces of information from the php to the photobook swf:
            var flashvars = {
                config: '{$base_dir}modules/productcustomizationps/swf/photobook/config.xml',
                designId: '{$design_id}',
                startEditing: 'false',
                appId: '{$fb_app_id}',
                orderId: '1',
                price: '{$product_price}'
            };
           
            var params = {
                allowScriptAccess: 'always', 
                wmode:'transparent', 
                allowFullScreen:'true'
            };

            var attributes = {
                name: 'personera-swf', 
                id: 'personera-swf'
            };
            {if ( $logged )}
            swfobject.embedSWF("{$base_dir}modules/productcustomizationps/swf/photobook/PersoneraPhotoBook-v2-2.swf", "personera-swf", "100%", "600", "10.2", "{$base_dir}modules/productcustomizationps/swf/photobook/playerProductInstall.swf", flashvars, params, attributes);

            swfobject.createCSS("#personera-swf", "display:block;text-align:left;");
            {/if}
        }
    
        window.igAsyncInit = function() {   
            IG.init({
                client_id: 'b33717df438d4272a49b99b8d5cb2ee1',
                logging: true,
                check_status: false
            });
        };
    
        (function() {
            var e = document.createElement('script'); e.async = true;
            e.src = 'http://personera-public-images.s3.amazonaws.com/photobook/swf/instagram-min.js';
            document.getElementById('ig-root').appendChild(e);
        }()
        );

    });

</script>
{if $logged}
<div id="personera-swf">
    <noscript>
        <h1>This app requires javascript. </h1>
        <p>Please enable javascript in your browser and reload the page.</p>
        <br>
        <br>
    </noscript>
</div>
{/if}
<br>
<div id="flashErrorDiv" style="display: none;">
    <p>
        To view this page ensure that Adobe Flash Player version 
        10.2.0 or greater is installed. 
        <a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a> 
    </p>
</div>


<div id="pleaseWaitDiv" style="display:none;float:right;">
        <div style="float:right">Please Wait... <img width="20px" height="20px" src="{$base_dir}modules/productcustomizationps/images//pleaseWaitSpinner.gif"/></div>
        <br>
        <p id="progressMessage" style="font-size:12px;float:right;"></p>
</div>  
<script type ="text/javascript">
    <!--
        var selectedCoverType = 1;
    -->
    function doInstagramLogin() {
        IG.login(function (response) {
            if (response.session) {
                document.getElementById('personera-swf').instagramLoginResultHandler(response.session.access_token);
            } else {
                document.getElementById('personera-swf').instagramLoginResultHandler(null);
             }
         }, { response_type: 'token' });
    }
</script>
