<?php
/**
* The AuthSocialFacebookFrontController Class to hanlde authentication via social networks
*
* @category   AuthSocial
* @package    Auth Social, Personera2.0
* @author     Shadley Wentzel <shadley@personera.com>
* @copyright  2012 Personera
* @license    http://www.personera.com/license/4_0.txt   
* @version    1.0
*
*/

class AuthsocialpsFacebookModuleFrontController extends ModuleFrontController
{
	public $AUTHENTICATION_METHOD_ID_FACEBOOK = 1;
	public $GENDER_MALE = 1;
	public $GENDER_FEMALE = 2;

	/**
	 * @see FrontController::initContent()
	 */
	public function init()
	{	

		parent::init();
		
		require_once($this->module->getLocalPath().'/classes/CustomerAuthentication.php');

		$this->processLogin();

	}
	public function printpre($data)
	{
		echo "<pre>";
			print_r($data);
		echo "</pre>";
	}
	/**
	 * Function to process a login from facebook connect
	 *
	 * @param void
	 * @return void
	 */
	public function processLogin()
	{		
		# User logged into Facebook

		require_once($this->module->getLocalPath().'/src/facebook.php');
		
		$app_id = (Configuration::get('FB_APPID', null, $this->context->shop->id_group_shop, $this->context->shop->id));
		$app_secret = (Configuration::get('FB_SECRET', null, $this->context->shop->id_group_shop, $this->context->shop->id));
		
		//$response = $this->parse_signed_request($explod_signed_Request[1],$app_secret);
		

		$facebook = new Facebook(array(
		  'appId'  => "{$app_id}",
		  'secret' => "{$app_secret}",
		  'cookie' => true,
          'domain' => "http://presta.localhost"
		));
		
		// Get User ID
		$user = $facebook->getUser();
	
		if ($user) {
			try {
			    // Proceed knowing you have a logged in user who's authenticated.
			    $fb_user_data = $facebook->api('/me');
			    $fb_user_data = (object) $fb_user_data;

		  	} catch (FacebookApiException $e) {
		    	error_log($e);
		    	$user = null;
		  	}
		}
		 
		$redirect_url = "http://".$_SERVER['HTTP_HOST']."/authentication.php";

		# Create Customer instance to query Customer table
		$customer = new Customer();
		$retrieved_customer = $customer->getByEmail($fb_user_data->email);

		# Add new customer if not in the Customer DB
		if (empty($retrieved_customer)){
			
			$newfb_customer = self::addCustomerFromFacebookUser($fb_user_data, $this->context->shop->id);
			
			# Add Customer authentication for facebook
			$authentication_method_id = self::addCustomerAuthentication(
												$newfb_customer->id, 
												$fb_user_data->id,
												$this->AUTHENTICATION_METHOD_ID_FACEBOOK,
												$this->context->shop->id,
												$this->context->shop->id_group_shop);
		}

		# Add data to cookie
		$this->addCookieData($retrieved_customer);
		
		# Redirect back
		$back_url = Tools::getValue('callling_url');
		$this->printpre($back_url);
		$this->printpre($this->context->customer);
		$mod_rewrite_active = Tools::modRewriteActive();
		$this->printpre("mod_rewrite_active has : ".$mod_rewrite_active);
		$shopurl = new ShopUrl('','',$this->context->customer->id_shop);
		$this->printpre($shopurl);
		$this->printpre(Tools::getValue('shop_name'));
		// die();
		Tools::redirect(html_entity_decode($back_url));
		# Tools::redirect('index.php?controller=order');
	}

	/**
	 * Function to add a new customer from a facebook signed request into the db
	 *
	 * @param string $response The signed request
	 * @param integer $shop_id The ID of the current shop
	 * @return Customer new customer object
	 */
	private function addCustomerFromFacebookUser($fb_user_data, $shop_id)
	{	
	
		# Add new Customer
		$customer = new Customer();

		$customer->id_shop = (int)$shop_id;
		$customer->firstname = $fb_user_data->first_name;
		$customer->lastname = $fb_user_data->last_name;
		$customer->email = $fb_user_data->email;
		$customer->passwd = self::generatePassword($customer->firstname.' '.$customer->lastname.$customer->email);
		$customer_birthday = new DateTime($fb_user_data->birthday);
		$customer->birthday = $customer_birthday->format('Y-m-d');
		$customer->active = 1;
		
		# Set gender based on FB
		switch($fb_user_data->gender){
			case "female":
				$customer->gender = $this->GENDER_FEMALE;
				break;
			default:
				$customer->gender = $this->GENDER_MALE;
				break;
		}
	
		$customer->add();	
		
		return $customer;
	}

	/**
	 * Function to add a new customer authentication
	 *
	 * @param integer $customer_id The ID of customer who authenticated
	 * @param integer $fb_uid Facebook User Id
	 * @param integer $authentication_method_id The ID authentication method used
	 * @param integer $shop_id The ID of the current shop
	 * @param integer $shop_group_id The ID of the current shop group
	 * @return customer id of customer object
	 */
	private function addCustomerAuthentication($customer_id, $fb_uid, $authentication_method_id, $shop_id, $shop_group_id)
	{	
		# Add new Customer Authentication Method
		$customer_authentication = new CustomerAuthentication();
		$customer_authentication->id_customer = $customer_id;
		$customer_authentication->id_authentication_method = $authentication_method_id;
		$customer_authentication->id_shop = $shop_id;
		$customer_authentication->id_group_shop = $shop_group_id;
		$customer_authentication->uid = $fb_uid;

		$customer_authentication->add();
		
		return $customer_id;
	}

	/**
	 * Function to add a new customer data into cookie
	 *
	 * @param customer $customer The customer object that needs to be added to cookie
	 * @return Id of customer object in cookie
	 */
	private function addCookieData($customer)
	{	
		$this->context->cookie->id_customer = intval($customer->id);
		$this->context->cookie->customer_lastname = $customer->lastname;
		$this->context->cookie->customer_firstname = $customer->firstname;
		$this->context->cookie->passwd = $customer->passwd;
		$this->context->cookie->logged = 1;
		$this->context->cookie->email = $customer->email;

		return $this->context->cookie->id_customer;
	}

	
	/**
	 * Function to generate a sha1 password
	 *
	 * @param string $password User passwod
	 * @return string generated password
	 */	
	private static function generatePassword($password){
	  $salt = md5(rand(100000, 999999).$password);
	  return substr(sha1($salt.$password), 0, 32);
	}
}
