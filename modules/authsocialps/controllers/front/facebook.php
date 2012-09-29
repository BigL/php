<?php
/**
* The AuthSocialFacebookFrontController Class to hanlde authentication via social networks
* 
*
* @category   AuthSocial
* @package    Authsocial PS
* @see        overide/classes/CustomerAuthentications.php, overide/classes/AuthenticationMethod.php,
* @author     Shadley Wentzel <shadley@personera.com>
* @copyright  2012 Personera
* @license    http://www.personera.com/license/4_0.txt   
* @version    1.0
*
*/

class AuthsocialpsFacebookModuleFrontController extends ModuleFrontController
{
	const AUTHENTICATION_METHOD_ID_FACEBOOK = 1;
	const GENDER_MALE = 1;
	const GENDER_FEMALE = 2;
	const GENDER_UNKNOWN = 4;
	const CUSTOMER_ACTIVE_STATE = 1;

	/**
	 * @see FrontController::initContent()
	 */
	public function init()
	{	
		parent::init();

		$this->processLogin();
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
		
		//$code_from_facebook = $response['code'];

		$app_id = (Configuration::get('FB_APPID', null, $this->context->shop->id_shop_group, $this->context->shop->id));
		$app_secret = (Configuration::get('FB_SECRET', null, $this->context->shop->id_shop_group, $this->context->shop->id));
		$redirect_url = "http://".$_SERVER['HTTP_HOST']."/authentication.php";
		
		$fbClient = new Facebook(array(
		        'appId'  => "{$app_id}",
		        'secret' => "{$app_secret}",
		        'cookie' => true,
		        'domain' => "{$this->context->shop->domain}"
		    ));
		
		//$fbClient->setAccessToken($_REQUEST['accessToken']);
		 
		# Get data about user
		// Get User ID
		$user = $fbClient->getUser();
		if ($user) {
		 try {
		     // Proceed knowing you have a logged in user who's authenticated.
		     $fb_user_data = $fbClient->api('/me');
		     $fb_user_data = (object) $fb_user_data;
		     // die( $this->printpre( $fb_user_data ) );
		   } catch (FacebookApiException $e) {
		     error_log($e);
		     $user = null;
		   }
		}
		
		$result = $fbClient->setExtendedAccessToken();

		#get signed request issued time
		$signed_request=$fbClient->getsignedRequest();

		# Create Customer instance to query Customer table
		$customer = new Customer();
		$customer = $customer->getByEmail($fb_user_data->email);
		
		if (empty($customer)){
			# Add new customer if not in the Customer DB
			$customer = self::addCustomerFromFacebookUser($fb_user_data, 
														  	$this->context->shop->id,
															$fbClient->getAccessToken(),
															$signed_request['issued_at']);
			
		}else{

			# update customer if already in db
			self::updateCustomerFromFacebookUser($customer,
												$fb_user_data,
												$fbClient->getAccessToken(),
												$signed_request['issued_at']);
		}

		# Add data to cookie
		self::addCookieData($customer);

		# Redirect back
		$back_url = Tools::getValue('callling_url');
		Tools::redirect(html_entity_decode($back_url));
	}

	/**
	 * Function to add a new customer from a facebook signed request into the db
	 * it also adds a new associated customer authentication record for hte customer
	 *
	 * @param string $response The signed request
	 * @param integer $shop_id The ID of the current shop
	 * @param string $fb_access_token New facebook access token
	 * @param string $fb_access_token_expiry New facebook access token expiry
	 * @return Customer new customer object
	 */
	private function addCustomerFromFacebookUser($fb_user_data, $shop_id, $fb_access_token, $fb_access_token_expiry)
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
		$customer->active = self::CUSTOMER_ACTIVE_STATE;
		
		# Set gender based on FB
		switch($fb_user_data->gender){
			case "female":
				$customer->gender = self::GENDER_FEMALE;
				break;
			case "male":
				$customer->gender = self::GENDER_MALE;
				break;
			default:
				$customer->gender = self::GENDER_UNKNOWN;
				break;
		}
	
		$customer->save();	

		# Add Customer authentication for facebook
		$authentication_method_id = self::addCustomerAuthentication(
											$customer->id, 
											$fb_user_data->id,
											self::AUTHENTICATION_METHOD_ID_FACEBOOK,
											$this->context->shop->id,
											$this->context->shop->id_shop_group,
											$fb_access_token,
											$fb_access_token_expiry);
		
		return $customer;
	}

	/**
	 * Function to updpate a customer from a facebook signed request into the db,
	 * it also adds or updates the related customer authentication
	 *
	 * @param customer $customer The customer to update
	 * @param mixed $fb_user_data Facebook user data
	 * @param string $fb_access_token New facebook access token
	 * @param string $fb_access_token_expiry New facebook access token expiry
	 * @return true
	 */
	private function updateCustomerFromFacebookUser($customer, $fb_user_data, $fb_access_token, $fb_access_token_expiry)
	{	 
		
		# update customer
		$customer->firstname = $fb_user_data->first_name;
		$customer->lastname = $fb_user_data->last_name;
		$customer->email = $fb_user_data->email;
		$customer_birthday = new DateTime($fb_user_data->birthday);
		$customer->birthday = $customer_birthday->format('Y-m-d');
		$customer->active = self::CUSTOMER_ACTIVE_STATE;
		$customer->save();	

		# find related customer authentication record
		$customer_authentication = CustomerAuthentication::getByCustomerId($customer->id, $customer->id_shop);

		if(empty($customer_authentication)){
			# retrieved customer does not have customer authentication associated
			# no authentication method found so create
			$authentication_method_id = self::addCustomerAuthentication(
											$customer->id, 
											$fb_user_data->id,
											self::AUTHENTICATION_METHOD_ID_FACEBOOK,
											$this->context->shop->id,
											$this->context->shop->id_shop_group,
											$fb_access_token,
											$fb_access_token_expiry);
		}else{
			# retrieved customer has customer authentication associated
			# update authenticaiton method
			$customer_authentication->access_token = $fb_access_token;
			$customer_authentication->date_exp = date('Y-m-d H:m:s', time() + $fb_access_token_expiry);
			$customer_authentication->save();
		}
		
		return true;
	}

	/**
	 * Function to add a new customer authentication
	 *
	 * @param integer $customer_id The ID of customer who authenticated
	 * @param integer $fb_uid Facebook User Id
	 * @param integer $authentication_method_id The ID authentication method used
	 * @param integer $shop_id The ID of the current shop
	 * @param integer $shop_group_id The ID of the current shop group
	 * @param string $access_token The Facebook Acces Token
	 * @param integer $fb_access_token_expiry The expiration of the access token in seconds from date of createion
	 * @return customer id of customer object
	 */
	private function addCustomerAuthentication($customer_id, $fb_uid, $authentication_method_id, $shop_id, $shop_group_id, $access_token, $fb_access_token_expiry)
	{	
		# Add new Customer Authentication Method
		$customer_authentication = new CustomerAuthentication();
		$customer_authentication->id_customer = $customer_id;
		$customer_authentication->id_authentication_method = $authentication_method_id;
		$customer_authentication->id_shop = $shop_id;
		$customer_authentication->id_shop_group = $shop_group_id;
		$customer_authentication->access_token = $access_token;
		$customer_authentication->uid = $fb_uid;
		$customer_authentication->date_exp = date('Y-m-d H:m:s', time() + $fb_access_token_expiry);
		$customer_authentication->save();
		
		return $customer_authentication->id;
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
	 * Function to parse the signed request from Facebook
	 *
	 * @param string $signed_request The signed request
	 * @param string $secret The app secret
	 * @return data from signed request
	 */
	private static function parse_signed_request($signed_request, $secret) 
	{
	  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
		
	  # decode the data
	  $sig = self::base64_url_decode($encoded_sig);echo '<pre>';
	  $data = json_decode(self::base64_url_decode($payload), true);

	  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
	    error_log('Unknown algorithm. Expected HMAC-SHA256');
	    return null;
	  }
	
	  # check sig
	  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	  if ($sig !== $expected_sig) {
	    error_log('Bad Signed JSON signature!');
	    return null;
	  }
		
	  return $data;
	}
	
	/**
	 * Function to base64_url_decode a tring
	 *
	 * @param string $input The encoded string
	 * @return string decoded string
	 */	
	private static function base64_url_decode($input) {
	    return base64_decode(strtr($input, '-_', '+/'));
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
