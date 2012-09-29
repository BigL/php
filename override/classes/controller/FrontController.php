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
     
		
		$fb_user_data = CustomerAuthentication::isfbLoggedin(
                                            $this->context->shop->id_shop_group,
                                            $this->context->shop->id,
                                            $this->context->customer->id,
                                            $this->context->shop->domain
                                          );

    if( is_object($fb_user_data) && isset( $fb_user_data->id )){
      #logout the user partially
      $this->context->customer->logged=1;
      $this->context->cookie->logged = 1;
      $show_fb_connect = 0;
    }else{
      $this->context->customer->logged=0;
      $this->context->cookie->logged = 0;
      $show_fb_connect = 1; 
    }
    
    $this->context->smarty->assign(array(
                                          "show_fb_connect" => $show_fb_connect,
                                          "logged" =>$this->context->customer->logged
                                          ));

    // Tools::dieObject($fb_user_data,false);
    // Tools::dieObject($this->context->cookie);
	}

	public function setMedia()
	{
		/*
        * Use Google Libraries API to host jQuery
        * this will load a new jquery version and not the 1.4.4 from google
        */
        parent::setMedia();

        $index = array_search(_PS_JS_DIR_ . 'jquery/jquery-1.4.4.min.js', $this->context->controller->js_files);

       # if ($index !== false){
       #     array_splice($this->context->controller->js_files, $index, 1, array('//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'));
       # } else {
       #     $this->context->controller->js_files[] = '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';

        #        }
        #load add jquery ui for goodies we need
        // $this->context->controller->js_files[] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js';
        // Tools::dieObject($this->context->smarty);


    }

  
}
