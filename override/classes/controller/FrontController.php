<?php
/**
* Overide class for the front controller. Added custom hooks, for the moment
* only added customizationBlock
*
* @copyright  Personera
* @see        
* @author     Shadley Wentzel <shadley@personera.com>
* @package    
*/

class FrontController extends FrontControllerCore
{


	/**
	 * Function to overide the default initContent function so we can add custon hooks
	 * Added Hooks:
	 *              customizationBlock - 	Hook for product customization 
	 *				paymentConfirmation -	Hook for displaying content after order has been confirmed
	 *
	 * @param void
	 * @return void
	 *
	 */
	public function initContent()
	{
		parent::initContent();
		$this->process();
		if ($this->context->getMobileDevice() == false)
		{
			// These hooks aren't used for the mobile theme.
			// Needed hooks are called in the tpl files.
			// Tools::fd("we got here, " . __METHOD__);
			$this->context->smarty->assign(array(
				'HOOK_HEADER' => Hook::exec('displayHeader'),
				'HOOK_TOP' => Hook::exec('displayTop'),
				'HOOK_LEFT_COLUMN' => ($this->display_column_left ? Hook::exec('displayLeftColumn') : ''),
				'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
				'HOOK_CUSTOMIZATION_BLOCK' => Hook::exec('customizationBlock'),
			));
		}
		else
		{
			$this->context->smarty->assign(array(
				'HOOK_MOBILE_HEADER' => Hook::exec('displayMobileHeader'),
			));
		}
    // Tools::dieObject($this->context->smarty);
		$verify_fbclient = $this->isfbLoggedin();
		// Tools::dieObject($this->context->smarty);
    
	}

	public function setMedia() 
	{
		/*
        * Use Google Libraries API to host jQuery
        * this will load a new jquery version and not the 1.4.4 from google
        */
        parent::setMedia();

        $index = array_search(_PS_JS_DIR_ . 'jquery/jquery-1.4.4.min.js', $this->context->controller->js_files);

        if ($index !== false){
            array_splice($this->context->controller->js_files, $index, 1, array('//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'));
        } else {
            $this->context->controller->js_files[] = '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';

        }
        
        #load add jquery ui for goodies we need
        $this->context->controller->js_files[] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js';
        
                
               
    }

private function isfbLoggedin()
    {

    $app_id = (Configuration::get('FB_APPID', null, $this->context->shop->id_shop_group, $this->context->shop->id));
    $app_secret = (Configuration::get('FB_SECRET', null, $this->context->shop->id_shop_group, $this->context->shop->id));

    $fbClient = new Facebook(array(
            'appId'  => "{$app_id}",
            'secret' => "{$app_secret}",
            'cookie' => true,
            'domain' => "{$this->context->shop->domain}"
        ));

      $customer_authentication = CustomerAuthentication::getByCustomerId($this->context->customer->id, $this->context->customer->id_shop);

      if($customer_authentication)
        $fbClient->setAccessToken($customer_authentication->access_token);

      $user = $fbClient->getUser();
      
      if ($user) {
        try {
          // Proceed knowing you have a logged in user who's authenticated.
          $fb_user_data = $fbClient->api('/me');
          $fb_user_data = (object) $fb_user_data;
        } catch (FacebookApiException $e) {
          error_log($e);

          $user = null;
        }
      }


      if(!isset($fb_user_data->id)){
        #logout the user partially
        $this->context->customer->logged=0;
        $this->context->cookie->logged=0;
      }else{
        $this->context->customer->logged=1;
      }

      return $this->context->customer->logged;
  }


}