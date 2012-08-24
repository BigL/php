<?php
/**
* The Paypal Direct Payments module
*
* @copyright  Personera
* @see        
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Paypal Direct PS
*/

if (!defined('_PS_VERSION_'))
  exit;

require_once(_PS_MODULE_DIR_.'/paypaldirectps/paypalcore/paypalwpp.php');

class PaypalDirectPS extends PaymentModule
{
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'paypaldirectps';
		$this->tab = 'payments_gateways';
		$this->version = 1.0;
    	$this->author = 'Shadley Wentzel';
		
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

        parent::__construct();

        /* The parent construct is required for translations */
		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('PayPal Direct Payment');
        $this->description = $this->l('Accepts credit card payments directly through PayPal API');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
		
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module');
	}
	
	function install()
	{
		if (!parent::install() OR 
		    !Configuration::updateValue('WPP_PAYPAL_DIRECT_APISIG', WPP_PAYPAL_SANDBOX_APISIG) OR
			!Configuration::updateValue('WPP_PAYPAL_DIRECT_APIUSER', WPP_PAYPAL_SANDBOX_APIUSER) OR 
			!Configuration::updateValue('WPP_PAYPAL_DIRECT_APIPASSWORD', WPP_PAYPAL_SANDBOX_APIPASSWORD) OR
			!Configuration::updateValue('WPP_PAYPAL_DIRECT_SANDBOX', 1) OR 
			!$this->registerHook('payment')
			)
			return false;
		return true;
	}

	function uninstall()
	{
		if (!parent::uninstall() OR
		    !Configuration::deleteByName('WPP_PAYPAL_DIRECT_APISIG') OR
			!Configuration::deleteByName('WPP_PAYPAL_DIRECT_APIUSER') OR
			!Configuration::deleteByName('WPP_PAYPAL_DIRECT_APIPASSWORD') OR 
			!Configuration::deleteByName('WPP_PAYPAL_DIRECT_SANDBOX')
			)
			return false;
		return true;
	}

	private function _postValidation()
	{
		// Validate the configuration screen in the Back Office
	}

	private function _postProcess()
	{
		// Called after validated configuration screen submit in Back Office
	}

	public function displayPayPal()
	{
		$this->_html .= '
		<img src="../modules/paypaldirect/paypal.gif" style="float:left; margin-right:15px;" />
		<b>'.$this->l('This module allows you to accept payments by PayPal directly from your site.').'</b><br /><br />
		'.$this->l('If the client chooses this payment mode, your PayPal account will be automatically credited.').'<br />
		'.$this->l('You need to configure your PayPal account first before using this module.').'
		<br /><br /><br />';
	}

	public function displayFormSettings()
	{
		$conf = Configuration::getMultiple(array('WPP_PAYPAL_DIRECT_APISIG', 'WPP_PAYPAL_DIRECT_APIUSER', 'WPP_PAYPAL_DIRECT_APIPASSWORD', 'WPP_PAYPAL_DIRECT_SANDBOX'));
		
		$user      = array_key_exists('user', $_POST) ? $_POST['user'] : (array_key_exists('WPP_PAYPAL_DIRECT_APIUSER', $conf) ? $conf['WPP_PAYPAL_DIRECT_APIUSER'] : '');
		$pass      = array_key_exists('pass', $_POST) ? $_POST['pass'] : (array_key_exists('WPP_PAYPAL_DIRECT_APIPASSWORD', $conf) ? $conf['WPP_PAYPAL_DIRECT_APIPASSWORD'] : '');
		$signature = array_key_exists('signature', $_POST) ? $_POST['signature'] : (array_key_exists('WPP_PAYPAL_DIRECT_APISIG', $conf) ? $conf['WPP_PAYPAL_DIRECT_APISIG'] : '');
		$sandbox   = array_key_exists('sandbox', $_POST) ? $_POST['sandbox'] : (array_key_exists('WPP_PAYPAL_DIRECT_SANDBOX', $conf) ? $conf['WPP_PAYPAL_DIRECT_SANDBOX'] : '');

		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Settings').'</legend>
			<label>'.$this->l('PayPal API User').'</label>
			<div class="margin-form"><input type="text" size="25" name="user" value="'.$user.'" /></div>
			<label>'.$this->l('PayPal API Password').'</label>
			<div class="margin-form"><input type="text" size="25" name="pass" value="'.$pass.'" /></div>
			<label>'.$this->l('PayPal API Signature').'</label>
			<div class="margin-form"><input type="text" size="75" name="signature" value="'.$signature.'" /></div>

			<label>'.$this->l('Sandbox mode').'</label>
			<div class="margin-form">
				<input type="radio" name="sandbox" value="1" '.($sandbox ? 'checked="checked"' : '').' /> '.$this->l('Yes').'
				<input type="radio" name="sandbox" value="0" '.(!$sandbox ? 'checked="checked"' : '').' /> '.$this->l('No').'
			</div>
			<br /><center><input type="submit" name="submitPaypal" value="'.$this->l('Update settings').'" class="button" /></center>
		</fieldset>
		</form><br /><br />
		';
	}

	public function getContent()
	{
		$this->_html = '<h2>Paypal Web Payments Pro Direct - US</h2>';
		if (isset($_POST['submitPaypal']))
		{
			if (empty($_POST['user']))
				$this->_postErrors[] = $this->l('Paypal API User is required.');
			if (empty($_POST['pass']))
				$this->_postErrors[] = $this->l('Paypal API Password is required.');
			if (empty($_POST['signature']))
				$this->_postErrors[] = $this->l('Paypal API Signature is required.');
			if (!isset($_POST['sandbox']))
				$_POST['sandbox'] = 1;
			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('WPP_PAYPAL_DIRECT_APIUSER', $_POST['user']);
				Configuration::updateValue('WPP_PAYPAL_DIRECT_APIPASSWORD', $_POST['pass']);
				Configuration::updateValue('WPP_PAYPAL_DIRECT_APISIG', $_POST['signature']);
				Configuration::updateValue('WPP_PAYPAL_DIRECT_SANDBOX', intval($_POST['sandbox']));
				$this->displayConf();
			}
			else
				$this->displayErrors();
		}

		$this->displayPayPal();
		$this->displayFormSettings();
		return $this->_html;
	}

	public function displayConf()
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
			'.$this->l('Settings updated').'
		</div>';
	}

	public function getPaypalApiEndPoint()
	{
		return Configuration::get('WPP_PAYPAL_DIRECT_SANDBOX') ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';
	}

	public function loadPayPalAPI()
	{
		$ppAPI = new wppPayment(Configuration::get('WPP_PAYPAL_DIRECT_APIUSER'),
				                    Configuration::get('WPP_PAYPAL_DIRECT_APIPASSWORD'),
									Configuration::get('WPP_PAYPAL_DIRECT_APISIG'),
									self::getPaypalApiEndPoint());
		
		return $ppAPI;	
	}

	public function execPayment($cart)
	{
		global $cookie, $smarty;

		$this->context->controller->addCSS($this->_path.'css/paypaldirectps.css');
		
		if(isset($_POST['paymentSubmit']))
		{
			$this->_postErrors = array();
			// Validate input
			if(!$this->validateCard($_POST['cardNumber']))
				$this->_postErrors[] = $this->l('Card number is invalid.');
			if(strlen($_POST['cardCVC']) < 3 || strlen($_POST['cardCVC']) > 4)
				$this->_postErrors[] = $this->l('Card CVC number is invalid.');
			
			$SelectedCurrency = new Currency(intval(isset($_POST['payment_currency']) ? $_POST['payment_currency'] : $cookie->id_currency));
				
			if(sizeof($this->_postErrors))
			{				
					$smarty->assign(array(
						'invoice'			=> new Address($cart->id_address_invoice),
						'cardNumber'		=> $_POST['cardNumber'],
						'cardCVC'			=> $_POST['cardCVC'],
						'nbProducts' 		=> $cart->nbProducts(),
						'currency_default' 	=> $SelectedCurrency,
						'currencies' 		=> $this->GetCurrency(),
						'total' 			=> number_format($cart->getOrderTotal(true, 3), 2, '.', ''),
						'this_path' 		=> $this->_path,
						'this_path_ssl' 	=> Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/",
						'ip_address'		=> $_SERVER['REMOTE_ADDR'],
						'errors'			=> $this->_postErrors));
		
				return $this->display(__FILE__, '/views/templates/front/payment_execution.tpl');
			}
			else
			{
				// Populate payment object with pament details
				$wpp = self::loadPayPalAPI();
				$wpp->cardType = $_POST['creditCardType'];
				$wpp->cardNumber = $_POST['cardNumber'];
				$wpp->expDateMonth = $_POST['expDateMonth'];
				$wpp->expDateYear = $_POST['expDateYear'];
				$wpp->cvv2Number = $_POST['cardCVC'];
				
				// Perform Payment action	
				if ($wpp->DoWPPDirectPayment($cart, DP_TYPE_SALE, $_SERVER['REMOTE_ADDR'])==false)
				{				
					$smarty->assign(array(
						'invoice'			=> new Address($cart->id_address_invoice),
						'cardNumber'		=> $_POST['cardNumber'],
						'cardCVC'			=> $_POST['cardCVC'],
						'nbProducts' 		=> $cart->nbProducts(),
						'currency_default' 	=> $SelectedCurrency,
						'currencies' 		=> $this->GetCurrency(),
						'total' 			=> number_format($cart->getOrderTotal(true, 3), 2, '.', ''),
						'this_path' 		=> $this->_path,
						'this_path_ssl' 	=> Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/",
						'ip_address'		=> $_SERVER['REMOTE_ADDR'],
						'errors'			=> $wpp->getErrorResult())
						);
		
						return $this->display(__FILE__, '/views/templates/front/payment_execution.tpl');
				} else
					self::validateOrder($cart->id, _PS_OS_PAYMENT_, number_format($cart->getOrderTotal(true, 3), 2, '.', ''), $wpp->cardType, $wpp->transactionID);
					$currentOrder = new Order($cart->currentOrder);
					// Once complete, redirect to order-confirmation.php
					Tools::redirectLink(__PS_BASE_URI__."order-confirmation.php?id_cart=".$cart->id."&id_module=".$this->id."&key=".$currentOrder->secure_key);
			}
		}
		else
		{
				$smarty->assign(array(
					'invoice'			=> new Address($cart->id_address_invoice),
					'nbProducts' 		=> $cart->nbProducts(),
					'currency_default' 	=> new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
					'currencies' 		=> $this->GetCurrency(),
					'total' 			=> number_format($cart->getOrderTotal(true, 3), 2, '.', ''),
					'this_path' 		=> $this->_path,
					'this_path_ssl' 	=> Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/",
					'ip_address'		=> $_SERVER['REMOTE_ADDR'],
					'errors'			=> $this->_postErrors));
	
			return $this->display(__FILE__, '/views/templates/front/payment_execution.tpl');
		}
	}

	public function hookHeader()
	{
		Tools::addCSS($this->_path.'css/paypaldirectps.css', 'all');
	}

	public function hookPayment($params)
	{
      	global $smarty, $cookie; 

      	$this->context->controller->addCSS($this->_path.'css/paypaldirectps.css');

		$smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/'
            ));
		return $this->display(__FILE__, '/views/templates/hooks/paypaldirectps_payment.tpl');
	}
	
	private function validateCard($cardnumber)
	{
		$cardnumber = preg_replace("/\D|\s/", "", $cardnumber);  # strip any non-digits
		$cardlength = strlen($cardnumber);
		if ($cardlength != 0)
		{
			$parity = $cardlength % 2;
			$sum = 0;
			for ($i = 0; $i < $cardlength; $i++)
			{
				$digit = $cardnumber[$i];
				if ($i % 2 == $parity) $digit = $digit * 2;
					if ($digit > 9) $digit = $digit-9;
						$sum = $sum + $digit;
			}
			$valid = ($sum % 10 == 0);
			return $valid;
		}
		return false;
	}

}
?>