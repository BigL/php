<?php
/**
* The Paypal Direct Module to allow for Paypal payment via the API
*
* @copyright  Personera
* @see        
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Product Customization PS
*/

if (!isset($paypalDefined)) {
$paypalDefined = true;

// Testing and Debug
define('WPP_PAYPAL_SANDBOX_APISIG', 'A2SoeeH3oya7l2r-AmPAQ5Us76SyAe2yzOTlzSZ7FeZ6KAU8eauGuUej');
define('WPP_PAYPAL_SANDBOX_APIUSER', 'shadle_1337591710_biz_api1.personera.com');
define('WPP_PAYPAL_SANDBOX_APIPASSWORD', '1337591734');

// WPP Direct Payment 
define('DP_TYPE_SALE', 'Sale');
define('DP_TYPE_AUTHORIZATION', 'Authorization');

// WPP Express Checkout
define('EC_TYPE_SALE', 'Sale');
define('EC_TYPE_AUTHORIZATION', 'Authorization');
define('EC_TYPE_ORDER', 'Order');

// Errors
define ('ERR_INT_APICONFIG', 1);

class nvpRequest
{
	public $nvpValues= array();

	
	public function setNVP($param, $value)
	{
		$this->nvpValues[$param] = $value;
	}
	
	public function getNVP($param)
	{
		return $this->nvpValues[$param];
	}
	
	public function getNVPStr()
	{
		$nvpstr = '';
		
		foreach ($this->nvpValues as $param => $value)
            $nvpstr .= '&'.strtoupper($param).'='.urlencode($value);
		
		return $nvpstr;
	}

}
class wppPayment
{
	// API Interface
	private $apiUser;
	private $apiPassword;
	private $apiSignature;
	private $apiEndPoint;
	private $apiVersion = '53.0'; // This is currently the only valid version, and should not be changed

	// Proxy Settings
	private $bUseProxy;
    private $proxyHost;
    private $proxyPort;
	
	// Returned Properties, valid after successful transaction
	public $transactionID = '';
	public $EC_Token = '';
	public $EC_PayerID = '';
	private $id_cart;
	public $id_address;	
	
	//Internal, generated from the input details
	private $expDate;
	
	// Internal transaction error/messages 
	public $resArray;
	private $curlErrorNum = 0;
	private $curlErrorMsg = '';
	

	// These are the payment details from the cart payment form
	public $cardType;
	public $cardNumber; 
	public $expDateMonth; 
	public $expDateYear; 
	public $cvv2Number; 
		
	function __construct($User = NULL, $Pwd = NULL, $Sig = NULL, $apiEndPoint)
	{
		$this->apiUser = $User;
		$this->apiPassword = $Pwd;
		$this->apiSignature = $Sig;
		$this->apiEndPoint = $apiEndPoint;
		
		self::DisableProxy();
	}
	
	private function _DP_PreProcess()
	{
		// Try and validate the config to prevent unnecessary errors
		if ( (!isset($this->apiUser)) OR (!isset($this->apiPassword)) OR (!isset($this->apiSignature)) ) {
			$this->resArray['INTERNAL_ERROR'] = ERR_INT_APICONFIG;
			return false;
		}
					
		$this->expDate = str_pad($this->expDateMonth, 2, '0', STR_PAD_LEFT).$this->expDateYear;
		
		return true;
	}
	
	private function hash_call($methodName,$nvpStr)
	{
		// setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->apiEndPoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
    	
		// Check if we need to use a proxy server (e.g. we are behind a firewall)
		if($this->bUseProxy)
			curl_setopt ($ch, CURLOPT_PROXY, $this->proxyHost.":".$this->proxyPort); 

		// NVPRequest to submit  to server
		$nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($this->apiVersion)."&PWD=".urlencode($this->apiPassword)."&USER=".urlencode($this->apiUser)."&SIGNATURE=".urlencode($this->apiSignature).$nvpStr;

		// setting the nvpreq as POST FIELD to curl
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

		// getting response from server
		$response = curl_exec($ch);

		// converting NVPResponse to an Associative Array
		$this->resArray = array();
		$this->resArray = self::deformatNVP($response);
		$this->nvpReqArray = self::deformatNVP($nvpreq);

		if (curl_errno($ch)) {
			// moving to display page to display curl errors
		  	$this->curlErrorNum = curl_errno($ch) ;
		  	$this->curlErrorMsg = curl_error($ch);
	 	} else {
		 	// closing
			curl_close($ch);
	  	}

	}

	private function deformatNVP($nvpstr)
	{
		$intial=0;
 		$nvpArray = array();

		while(strlen($nvpstr)) {
			// postion of Key
			$keypos = strpos($nvpstr,'=');
			// position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			// getting the Key and Value values and storing in a Associative Array
			$keyval = substr($nvpstr,$intial,$keypos);
			$valval = substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			// decoding the respose
			$nvpArray[urldecode($keyval)] = urldecode($valval);
			$nvpstr = substr($nvpstr,$valuepos+1,strlen($nvpstr));
     	}
		
		return $nvpArray;
	}
	
	public function DoWPPDirectPayment($cart, $paymentType, $srcIP)
	{
		// Required: IPADDRESS, CREDITCARDTYPE, ACCT, EXPDATE, CVV2, STREET, CITY, STATE, COUNTRYCODE, ZIP, AMT
		// Required (to avoid Account Config): NOTIFYURL
		
		if (!self::_DP_PreProcess())
			return false;
			
		// Construct the request string that will be sent to PayPal.
   		// The variable $nvpstr contains all the variables and is a
        // name value pair string with & as a delimiter */
		
		$nvpRequest = new nvpRequest();
		
		// Mandatory
		$nvpRequest->setNVP('IPADDRESS' , $srcIP);
		$nvpRequest->setNVP('CREDITCARDTYPE' , $this->cardType);
		$nvpRequest->setNVP('ACCT' , $this->cardNumber);
		$nvpRequest->setNVP('EXPDATE' , $this->expDate);
		$nvpRequest->setNVP('CVV2' , $this->cvv2Number);
		
		// Get the address from the database
		$billing_address = new Address($cart->id_address_invoice);
		$billing_country = new Country($billing_address->id_country);
		$billing_state = new State($billing_address->id_state);
		
		$nvpRequest->setNVP('STREET' , $billing_address->address1);
		$nvpRequest->setNVP('CITY' , $billing_address->city);
		$nvpRequest->setNVP('STATE' , $billing_state->iso_code);
		$nvpRequest->setNVP('COUNTRYCODE' , $billing_country->iso_code);
		$nvpRequest->setNVP('ZIP' , $billing_address->postcode);
		
		$Cart_Products = $cart->getProducts();
		$LineItem=0;
		$taxTotal=0;
		$itemTotal=0;
		
		foreach ($Cart_Products AS $Product)
		{
			$nvpRequest->setNVP('L_NAME'.$LineItem , $Product['name']);
			$nvpRequest->setNVP('L_AMT'.$LineItem , floatval($Product['price']));
			$nvpRequest->setNVP('L_NUMBER'.$LineItem , intval($Product['id_product']));
			$nvpRequest->setNVP('L_QTY'.$LineItem , intval($Product['quantity']));
			$nvpRequest->setNVP('L_TAXAMT'.$LineItem , floatval($Product['price_wt']-$Product['price']));
			$taxTotal += floatval($Product['total_wt']-$Product['total']);
			$itemTotal += floatval($Product['total']);
			$LineItem++;
		}
		
		// Calculated values
		$shippingFee = number_format($cart->getOrderTotal(true, 5), 2, '.', '');
		$nvpRequest->setNVP('TAXAMT' , $taxTotal);
		$nvpRequest->setNVP('ITEMAMT' , $itemTotal);
		$nvpRequest->setNVP('SHIPPINGAMT' , $shippingFee);

		$totalAmount = $itemTotal + $taxTotal + $shippingFee;  
		$nvpRequest->setNVP('AMT' , $totalAmount);

		// Optional
		$currency = new Currency($cart->id_currency);
		$customer = new Customer($cart->id_customer);
		
		$nvpRequest->setNVP('PAYMENTACTION' , $paymentType);
		$nvpRequest->setNVP('FIRSTNAME' , $billing_address->firstname);
		$nvpRequest->setNVP('LASTNAME' , $billing_address->lastname);
		$nvpRequest->setNVP('CURRENCYCODE' , $currency->iso_code);
		$nvpRequest->setNVP('EMAIL' , $customer->email);
		
		// Shipping Details
		$delivery_address = new Address($cart->id_address_delivery);
		$delivery_country = new Country($delivery_address->id_country);
		$delivery_state = new State($delivery_address->id_state);

		$nvpRequest->setNVP('SHIPTONAME' , $delivery_address->firstname . ' ' . $delivery_address->lastname);
		$nvpRequest->setNVP('SHIPTOSTREET' , $delivery_address->address1);
		$nvpRequest->setNVP('SHIPTOCITY' , $delivery_address->city);
		$nvpRequest->setNVP('SHIPTOSTATE' , $delivery_state->iso_code);
		$nvpRequest->setNVP('SHIPTOCOUNTRYCODE' , $delivery_country->iso_code);
		$nvpRequest->setNVP('SHIPTOZIP' , $delivery_address->postcode);
		
		$nvpstr=$nvpRequest->getNVPStr();
		
		// Make the API call to PayPal, using API signature.
   		// The API response is stored in an associative array called $resArray */
		self::hash_call("doDirectPayment",$nvpstr);

		// Return the API response.
   		//
		if ((strtoupper($this->resArray['ACK'])!='SUCCESS') AND (strtoupper($this->resArray['ACK'])!='SUCCESSWITHWARNING'))
			return false;
		
		$this->transactionID = $this->resArray['TRANSACTIONID'];
		
		return true;
	}
	
	public function SetExpressCheckout($type, $CartID)
	{
		// Required: RETURNURL, CANCELURL, AMT
		// Required (to avoid Account Config): NOTIFYURL
		
		$baseurl = 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
		
		// Get the shopping cart we're processing
		$StoreCart = new Cart($CartID);
		$CartCurrency = new Currency($StoreCart->id_currency);
		$nvpRequest = new nvpRequest();
		
		// Mandatory
		$nvpRequest->setNVP('RETURNURL' , $baseurl.'modules/paypalexpress/checkout.php?currencyCodeType='.$CartCurrency->iso_code.'&paymentType='.$type.'&paymentAmount='.number_format($StoreCart->getOrderTotal(true, 3), 2, '.', ''));
		$nvpRequest->setNVP('CANCELURL' , $baseurl.'order.php');
		
		// Optional
		$nvpRequest->setNVP('PAYMENTACTION' , $type);		
		$nvpRequest->setNVP('CURRENCYCODE' , $CartCurrency->iso_code);
		$nvpRequest->setNVP('CUSTOM' , $StoreCart->id); 
		
		// Cart Contents
		$StoreCart_Products = $StoreCart->getProducts();
		$LineItem=0;
		$TAX_Total=0;
		$ITEM_Total=0;
		
		foreach ($StoreCart_Products AS $Product)
		{
			$nvpRequest->setNVP('L_NAME'.$LineItem , $Product['name']);
			$nvpRequest->setNVP('L_AMT'.$LineItem , floatval($Product['price']));
			$nvpRequest->setNVP('L_NUMBER'.$LineItem , intval($Product['id_product']));
			$nvpRequest->setNVP('L_QTY'.$LineItem , intval($Product['quantity']));
			$nvpRequest->setNVP('L_TAXAMT'.$LineItem , floatval($Product['price_wt']-$Product['price']));
			$TAX_Total += floatval($Product['total_wt']-$Product['total']);
			$ITEM_Total += floatval($Product['total']);
			$LineItem++;
		}
		
		// Calculated values
		$nvpRequest->setNVP('TAXAMT' , $TAX_Total);
		$nvpRequest->setNVP('ITEMAMT' , $ITEM_Total);  
		$nvpRequest->setNVP('AMT' , $ITEM_Total + $TAX_Total);  // , number_format($StoreCart->getOrderTotal(true, 3), 2, '.', ''));
		
		//echo '<br /><br /><pre>';
		//print_r($nvpRequest->nvpValues);
		//echo '</pre>';
		//die ('Debug Halt');
		
		$nvpstr=$nvpRequest->getNVPStr();

		self::hash_call("SetExpressCheckout",$nvpstr);

		if(strtoupper($this->resArray['ACK'])!='SUCCESS')
			return false;
		 
		$this->EC_Token = urldecode($this->resArray['TOKEN']);
		
		return true;		 	
	}
	
	public function GetExpressCheckoutDetails($token)
	{		
		// Required: TOKEN
		
		// Mandatory
		$nvpstr="&TOKEN=".urlencode($token);

		self::hash_call("GetExpressCheckoutDetails",$nvpstr);

		if (strtoupper($this->resArray['ACK'])!='SUCCESS')
			return false;
		
		$this->EC_PayerID = $payerID;
		$this->EC_Token = urldecode($this->resArray['TOKEN']);
		
		$this->CreateUser();
		
		return true;
	}
	
	public function DoExpressCheckoutPayment($amount, $currency_iso, $type)
	{
		// Required: TOKEN, PAYMENTACTION, PAYERID, AMT
		$nvpRequest = new nvpRequest();
		
		// Mandatory
		$nvpRequest->setNVP('TOKEN' , $this->EC_Token);
		$nvpRequest->setNVP('PAYMENTACTION' , $type);
		$nvpRequest->setNVP('PAYERID' , $this->EC_PayerID);
		$nvpRequest->setNVP('AMT' , $amount);
		
		//Optional
		$nvpRequest->setNVP('CURRENCYCODE' , $currency_iso);
		$nvpRequest->setNVP('IPADDRESS' , $_SERVER['SERVER_NAME']);
		
		$nvpstr='&TOKEN='.urlencode($this->EC_Token).'&PAYERID='.urlencode($this->EC_PayerID).'&PAYMENTACTION='.urlencode($type).'&AMT='.urlencode($amount).'&CURRENCYCODE='.urlencode($currency_iso).'&IPADDRESS='.urlencode($_SERVER['SERVER_NAME']);

		self::hash_call("DoExpressCheckoutPayment",$nvpstr);

		if (strtoupper($this->resArray['ACK'])!='SUCCESS')
			return false;
		
		return true;
	}
		
	public function getErrorResult()
	{
		$errors = array();
		if($this->curlErrorNum) { 
			$errors[] = 'Communication error. Please try later.'; //'Error Number: ' . $this->curlErrorNum . 'Error Message: ' . $this->curlErrorMsg;		
		} else {
			//$errors[] = 'Timestamp: ' . $this->resArray['TIMESTAMP'];
			//$errors[] = 'Ack: ' . $this->resArray['ACK'];
			//$errors[] = 'Correlation ID: ' . $this->resArray['CORRELATIONID'];
            //$errors[] = 'Version ' . $this->resArray['VERSION'];
			
			$num = 0;
			while (isset($this->resArray['L_ERRORCODE'.$num])) {
				//$errors[] = 'Error Number: ' . $this->resArray['L_ERRORCODE'.$num];
				//$errors[] = 'Message (short): ' . $this->resArray['L_SHORTMESSAGE'.$num];
				$errors[] = $this->resArray['L_LONGMESSAGE'.$num];// 'Message (long): ' . $this->resArray['L_LONGMESSAGE'.$num];
				$num++;
			}
		}// end else

		return $errors;
	}
	
	public function EnableProxy($host='127.0.0.1', $port='808')
	{
		$this->bUseProxy = true;
		$this->proxyHost = $host;
		$this->proxyPort = $port;	
	}
	
	public function DisableProxy()
	{
		$this->bUseProxy = false;
	}
	
	public function CreateUser()
	{
		global $cookie;
		
		$customer = new Customer();
		
		// Required fields
		$customer->lastname = $this->resArray['LASTNAME'];
		$customer->firstname = $this->resArray['FIRSTNAME'];
		$customer->passwd = Tools::encrypt('changeme');
		$customer->email = $this->resArray['EMAIL'];
		$customer->active = 1;
		
		// Create customer record
		$customer->add();
		
		// Add the address (billing)
		$address = new Address();
		$address->id_customer = intval($customer->id);
		
		$address->id_country = Country::getIdByName(NULL, $this->resArray['SHIPTOCOUNTRYNAME']);
		$address->alias = 'PayPal';
		$address->lastname = $this->resArray['LASTNAME'];
		$address->firstname = $this->resArray['FIRSTNAME'];
		$address->address1 = $this->resArray['SHIPTOSTREET'];
		$address->postcode = $this->resArray['SHIPTOZIP'];
		$address->city = $this->resArray['SHIPTOCITY'];
				
		$address->add();
		
		// Save this for use in the Express checkout
		$this->id_address = $address->id;		
	}

}
} // $payPalDefined
?>