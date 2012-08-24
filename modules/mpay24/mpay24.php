<?php
/**
 * @author              support@mpay24.com
 * @filesource          mpay24.php
 * @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */

// check if the order status is defined
if (!defined('_MPAY24_RESERVED_ORDER_STATUS_')) {
	// order status is not defined - check if, it exists in the table
	$rq = Db::getInstance()->getRow('
							SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state_lang`
							WHERE id_lang = \''.pSQL('1').'\' AND  name = \''.pSQL('mPAY24 Reserved Order Status').'\'');
	if ($rq && isset($rq['id_order_state']) && intval($rq['id_order_state']) > 0) {
		define('_MPAY24_RESERVED_ORDER_STATUS_', $rq['id_order_state']);
	} else {
		Db::getInstance()->Execute('
									INSERT INTO `'._DB_PREFIX_.'order_state` (`unremovable`, `color`, `send_email`) VALUES(1, \'lightblue\', 1)');
		$stateid = Db::getInstance()->Insert_ID();
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`)
									VALUES(' . intval($stateid) . ', 1, \'mPAY24 Reserved Order Status\', \'mpay24\')');
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`)
															VALUES(' . intval($stateid) . ', 3, \'mPAY24 Reserved Order Status\', \'mpay24\')');
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`)
																					VALUES(' . intval($stateid) . ', 2, \'mPAY24 Reserved Order Status\', \'mpay24\')');
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`)
																					VALUES(' . intval($stateid) . ', 4, \'mPAY24 Authorisierungsbestellstatus\', \'mpay24\')');
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`)
																					VALUES(' . intval($stateid) . ', 5, \'mPAY24 Reserved Order Status\', \'mpay24\')');
		define('_MPAY24_RESERVED_ORDER_STATUS_', $stateid);
	}
}

class mpay24 extends PaymentModule
{
   
    private $_html = '';
    private $_postErrors = array();
   
    function __construct()
    {
        $this->name = 'mpay24';
        $this->tab = 'payments_gateways';
        $this->version = 1;
        $this->author = 'mPAY24 GmbH';
        $this->module_key = "3ecd4bf2b8196af77363c46962880d48";

        parent::__construct(); // The parent construct is required for translations

        $this->page = basename(__FILE__, '.php');
        $this->displayName = "mPAY24";
        $this->description = $this->l('mPAY24 Payment Service Provider Module');

    }
    
	public function install()
	{
      /* Install and register on hook */
        if (!parent::install()
            OR !$this->registerHook('payment')
            OR !$this->registerHook('paymentReturn')
            OR !$this->registerHook('shoppingCartExtra')
            OR !$this->registerHook('backBeforePayment')
            OR !$this->registerHook('rightColumn')
            OR !$this->registerHook('cancelProduct')
            OR !$this->registerHook('adminOrder')
            OR !$this->registerHook('invoice'))
            return false;
        
        /* Set database */
        if (!Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mpay24_order` (
            `MPAYTID` VARCHAR( 255 ) NOT NULL ,
            `TID` VARCHAR( 255 ) NOT NULL ,
            `STATUS` VARCHAR( 255 ) NOT NULL ,
            `AMOUNT_RESERVED` INT NOT NULL ,
            `AMOUNT_BILLED` INT NOT NULL ,
            `AMOUNT_CREDITED` INT NOT NULL ,
            `CURRENCY` VARCHAR( 3 ) NOT NULL ,
            `P_TYPE` VARCHAR( 255 ) NOT NULL ,
            `BRAND` VARCHAR( 255 ) NOT NULL ,
            `CUSTOMER` VARCHAR( 255 ) NOT NULL ,
            `APPR_CODE` VARCHAR( 255 ) NOT NULL ,
            `CREATED_AT` TIMESTAMP NULL DEFAULT NULL ,
            `UPDATED_AT` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            UNIQUE (
            `MPAYTID`
            )
            ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;"))
            return false;
            
            if (!Db::getInstance()->Execute("
            CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mpay24_debug` (
                  `debug_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `calledMethod` text,
                  `type` text,
                  `data` text,
                  PRIMARY KEY (`debug_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        "))
            return false;
        
        return true;
	}
	
    public function getContent()
    {
        global $currentIndex, $cookie;
        include("api/prestaShop.php");
        
        if (Tools::isSubmit('submitMpay24Checkout'))
        {
            $errors = array();
            $mode = Tools::getValue('mpay24_test_mode');
            if ($mode == "true")
                $mode = true;
            else
                $mode = false;
            Configuration::updateValue('MPAY24_TEST_MODE', Tools::getValue('mpay24_test_mode'));
            if (($merchant_id = Tools::getValue('mpay24_merchant_id')) AND (preg_match('/7[0-9]{4}/', $merchant_id) 
                                                                          OR preg_match('/9[0-9]{4}/', $merchant_id))){

                $prestaShop = new prestaShop($merchant_id, Tools::getValue('mpay24_soap_pass'), $mode, Tools::getValue('mpay24_proxy_host'), Tools::getValue('mpay24_proxy_port'));
                $result = $prestaShop->getPaymentMethods();  
                $mpay24PaymentSystems = '';  
                for($i=0; $i<$result->getAll(); $i++){
                    $mpay24PaymentSystems.= $result->getBrand($i) . ",";
                }
                if($result->getGeneralResponse()->getStatus() == 'OK'){
                	Configuration::updateValue('MPAY24_MERCHANT_ID', $merchant_id);
                    Configuration::updateValue('MPAY24_SOAP_PASS', Tools::getValue('mpay24_soap_pass'));
                    Configuration::updateValue('MPAY24_ACTIVE_PAYMENT_SYSTEMS', substr($mpay24PaymentSystems, 0, -1));
                    Configuration::updateValue('MPAY24_PAYMENT_SYSTEMS_ENABLED', 'false');
                    Configuration::updateValue('MPAY24_BILLING_ADDRESS_MODE', 'ReadOnly');
                    Configuration::updateValue('MPAY24_ALL_ACTIVE_PS', $result->getAll());
	                if ($proxy_host = Tools::getValue('mpay24_proxy_host') AND $proxy_port = Tools::getValue('mpay24_proxy_port')){
		                Configuration::updateValue('MPAY24_PROXY_HOST', $proxy_host);
		                Configuration::updateValue('MPAY24_PROXY_PORT', $proxy_port);
		            }
		            if (!sizeof($errors)){
		                Tools::redirectAdmin($currentIndex.'&configure=mpay24&token='.Tools::getValue('token').'&conf=4
		                &resultStatus='.$result->getGeneralResponse()->getStatus().
		                '&resultReturnCode='.urlencode($result->getGeneralResponse()->getReturnCode()).
                        '&resultAll='.$result->getAll());
		            }
                }
                else
                    $errors['mpay24'] = '<div class="warning error"><h3>'.$this->l($result->getGeneralResponse()->getReturnCode()).'</h3></div>';
            } else
                $errors[] = '<div class="warning error"><h3>'.$this->l('The merchant ID seems to be wrong! It should be a 5 digit number, 
                starting with 7 or 9.').'</h3></div>';
            
            foreach ($errors as $error)
                echo $error;
        }
        
        if (Tools::isSubmit('submitPaymentSystems')){
        	Configuration::updateValue('MPAY24_PAYMENT_SYSTEMS_ENABLED', Tools::getValue('MPAY24_PAYMENT_SYSTEMS_ENABLED'));
        	Configuration::updateValue('MPAY24_BILLING_ADDRESS_MODE', Tools::getValue('MPAY24_BILLING_ADDRESS_MODE'));
        	$paymentSystems = '';
            foreach ($_POST as $key => $value){ 
                if(substr($key, 0, 3) == "ps_")
                    $paymentSystems.= $value . ",";
                }
            Configuration::updateValue('MPAY24_PAYMENT_SYSTEMS_CHECKED', substr($paymentSystems, 0, -1));
            if(Tools::getValue('MPAY24_PAYMENT_SYSTEMS_ENABLED') == 'true')
                Configuration::updateValue('MPAY24_PAYMENT_SYSTEMS_SHOP', substr($paymentSystems, 0, -1));
            else{
            	foreach(explode(",", Configuration::get('MPAY24_ACTIVE_PAYMENT_SYSTEMS')) as $value)
                    $activePaymentSystems[$value] = $value;
            
	            $checkedPaymentSystems = explode(",", Configuration::get('MPAY24_PAYMENT_SYSTEMS_CHECKED'));
            	foreach($checkedPaymentSystems as $cps){
	                foreach($activePaymentSystems as $aps){
	                    if($cps == $aps && $cps!=''){
	                        unset($activePaymentSystems[$cps]);
	                    }
	                }
	             }
	             
	             $shopActivePaymentSystems = "";
	             foreach($activePaymentSystems as $aps){
	                $shopActivePaymentSystems.= $aps . ","; 
	             }
	             Configuration::updateValue('MPAY24_PAYMENT_SYSTEMS_SHOP', substr($shopActivePaymentSystems, 0, -1));
            }
        }
        
        if (Tools::isSubmit('submitDesignSettings')){
        	foreach($_REQUEST as $key => $value)
        	   if(substr($key, 0, 12) == 'MPAY24_ORDER' || substr($key, 0, 9) == 'MPAY24_SC' || substr($key, 0, 21) == 'MPAY24_SHIPPING_COSTS'
        	                      || substr($key, 0, 16) == 'MPAY24_SUB_TOTAL' || substr($key, 0, 15) == 'MPAY24_DISCOUNT' || substr($key, 0, 10) == 'MPAY24_TAX'
        	                      || substr($key, 0, 11) == 'MPAY24_ITEM' || substr($key, 0, 12) == 'MPAY24_PRICE')
        	       Configuration::updateValue($key, $value);
        }
        
        $html = $post.'<h2>'.$this->displayName.'</h2>
        <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset>
            <legend><img src="'.__PS_BASE_URI__.'modules/mpay24/logo.gif" />'.$this->l('Settings').'</legend>
                <p>'.$this->l('Use the test mode to test the module. Later use the live mode if there were no problems during test mode. Remember to change your merchant ID and soap password according to the mode (test or live).').'</p>
                <label>
                    '.$this->l('Mode').'
                </label>
                <div class="margin-form">
                    <select name="mpay24_test_mode">
                        <option value="false"';
                        if(Configuration::get('MPAY24_TEST_MODE') == 'false')
                          $html.= ' selected="selected"';
                        $html.= '>'.$this->l('Live').'&nbsp;&nbsp;</option>
                        <option value="true"';
                        if(Configuration::get('MPAY24_TEST_MODE') == 'true')
                        $html.= ' selected="selected"';
                        $html.= '>'.$this->l('Test').'&nbsp;&nbsp;</option>
                    </select>
                </div>
                <label>
                    '.$this->l('Merchant ID').'
                </label>
                <div class="margin-form">
                    <input type="text" name="mpay24_merchant_id" value="'.Tools::getValue('mpay24_merchant_id', Configuration::get('MPAY24_MERCHANT_ID')).'" />
                </div>
                <label>
                    '.$this->l('SOAP Password').'
                </label>
                <div class="margin-form">
                    <input type="password" name="mpay24_soap_pass" value="'.Tools::getValue('mpay24_soap_pass', Configuration::get('MPAY24_SOAP_PASS')).'" />
                </div>
                <p>'.$this->l('In case your server is behind a proxy you should give the proxy host and port.').'</p>               
                <label>
                    '.$this->l('Proxy host').'
                </label>
                <div class="margin-form">
                    <input type="text" name="mpay24_proxy_host" value="'.Tools::getValue('mpay24_proxy_host', Configuration::get('MPAY24_PROXY_HOST')).'" />
                </div>
                <label>
                    '.$this->l('Proxy port').'
                </label>
                <div class="margin-form">
                    <input type="text" name="mpay24_proxy_port" value="'.Tools::getValue('mpay24_proxy_port', Configuration::get('MPAY24_PROXY_PORT')).'" />
                </div>
                <div class="clear center"><input type="submit" name="submitMpay24Checkout" class="button" value="'.$this->l('   Save   ').'" /></div>
            </fieldset>
        </form>
        <br /><br />';
        $brands = explode(",", Configuration::get('MPAY24_ACTIVE_PAYMENT_SYSTEMS'));

        if((Tools::isSubmit('submitMpay24Checkout') && $_REQUEST["resultStatus"] == 'OK') || Configuration::get("MPAY24_ACTIVE_PAYMENT_SYSTEMS") != ''){
	        $html.= '<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'"> 
            <script type="text/javascript">
                var pos_select = '.(($tab = (int)Tools::getValue('tabs')) ? $tab : '0').';
            </script>
            <script type="text/javascript" src="'._PS_BASE_URL_._PS_JS_DIR_.'tabpane.js"></script>
            <link type="text/css" rel="stylesheet" href="'._PS_BASE_URL_._PS_CSS_DIR_.'tabpane.css" />
            <input type="hidden" name="tabs" id="tabs" value="0" />
            <div class="tab-pane" id="tab-pane-1" style="width:100%;">
                 <div class="tab-page" id="step1">
                    <h4 class="tab"><img src="../img/admin/payment.gif" /> '.$this->l('Activate/deactivate mPAY24 payment systems').'</h2>
                    <table style="border-spacing: 30px 30px;">
                        <tr>
                            <td><input type="radio" value="true" name="MPAY24_PAYMENT_SYSTEMS_ENABLED" title="psActive"';
                               if (Configuration::get("MPAY24_PAYMENT_SYSTEMS_ENABLED") == 'true') 
                                   $html.= ' checked';
                               $html.= '></td>
                            <td>'.$this->l('Activate the checked payment systems').'</td>
                            <td><input type="radio" value="false" name="MPAY24_PAYMENT_SYSTEMS_ENABLED" title="psInactive"';
                               if (Configuration::get("MPAY24_PAYMENT_SYSTEMS_ENABLED") == 'false') 
                                   $html.= ' checked';
                               $html.= '></td>
                            <td colspan="2">'.$this->l('Deactivate the checked payment systems').'</td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <table style="border-spacing: 80px 30px;">';
                                    for($i=0; $i<Configuration::get("MPAY24_ALL_ACTIVE_PS"); $i++){
                                        if($i % 4 == 0):
                                        $html.= '<tr>
                                            <td>
                                                <div style="clear:both;">
                                                    <img src="../modules/mpay24/images/'.$brands[$i].'.png" 
                                                        alt="'.$brands[$i].'" />';
                                        if(substr($brands[$i], 0, 5) == 'HOBEX')
                                            $html.= '<img src="../modules/mpay24/images/'.substr($brands[$i], 6).'.png"" />';
                                        $html.= '<input align="left" type="checkbox" name="ps_'.$brands[$i].'" 
                                                                                     value="'.$brands[$i].'"';
                                        if(in_array($brands[$i], explode(",", Configuration::get("MPAY24_PAYMENT_SYSTEMS_CHECKED"))))
                                          $html.= ' checked';
                                        $html.= '>
                                                </div>
                                            </td>';
                                    elseif($i % 4 == 1):
                                        $html.= '<td>
                                            <div style="clear:both;">
                                                <img src="../modules/mpay24/images/'.$brands[$i].'.png" 
                                                    alt="'.$brands[$i].'" />';
                                        if(substr($brands[$i], 0, 5) == 'HOBEX')
                                            $html.= '<img src="../modules/mpay24/images/'.substr($brands[$i], 6).'.png" />';
                                        $html.= '<input align="left" type="checkbox" name="ps_'.$brands[$i].'" 
                                                                                     value="'.$brands[$i].'"';
                                        if(in_array($brands[$i], explode(",", Configuration::get("MPAY24_PAYMENT_SYSTEMS_CHECKED")))) 
                                            $html.= ' checked';
                                        $html.= '>
                                            </div>
                                        </td>';
                                    elseif($i % 4 == 2):
                                        $html.= '<td>
                                            <div style="clear:both;">
                                                <img src="../modules/mpay24/images/'.$brands[$i].'.png" 
                                                    alt="'.$brands[$i].'" />';
                                        if(substr($brands[$i], 0, 5) == 'HOBEX')
                                            $html.= '<img src="../modules/mpay24/images/'.substr($brands[$i], 6).'.png" />';
                                        $html.= '<input align="left" type="checkbox" name="ps_'.$brands[$i].'" 
                                                                                     value="'.$brands[$i].'"';
                                        if(in_array($brands[$i], explode(",", Configuration::get("MPAY24_PAYMENT_SYSTEMS_CHECKED")))) 
                                            $html.= ' checked';
                                        $html.= '>
                                            </div>
                                        </td> ';   
                                    elseif($i % 4 == 3):
                                        $html.= '<td>
                                            <div style="clear:both;">
                                                <img src="../modules/mpay24/images/'.$brands[$i].'.png" 
                                                    alt="'.$brands[$i].'" />';
                                        if(substr($brands[$i], 0, 5) == 'HOBEX')
                                            $html.= '<img src="../modules/mpay24/images/'.substr($brands[$i], 6).'.png" />';
                                        $html.= '<input align="left" type="checkbox" name="ps_'.$brands[$i].'" 
                                                                                     value="'.$brands[$i].'"';
                                        if(in_array($brands[$i], explode(",", Configuration::get("MPAY24_PAYMENT_SYSTEMS_CHECKED"))))
                                            $html.= ' checked';
                                        $html.= '>
                                            </div>
                                        </td>'; 
                                    else:
                                        $html.= '<td>
                                            <div style="clear:both;">
                                                <img src="images/'.$brands[$i].'.png" 
                                                    alt="'.$brands[$i].'" />';
                                        if(substr($brands[$i], 0, 5) == 'HOBEX') 
                                           $html.= '<img src="../modules/mpay24/images/'.substr($brands[$i], 6).'.png"/>';
                                        $html.= '<input align="left" type="checkbox" name="ps_'.$brands[$i].'" 
                                                                                     value="'.$brands[$i].'"';
                                        if(in_array($brands[$i], explode(",", Configuration::get("MPAY24_PAYMENT_SYSTEMS_CHECKED")))) 
                                            $html.= ' checked';
                                        $html.= '>
                                            </div>
                                        </td>
                                    </tr>';
                                endif;
                            }
                            $html.= '</table>
                        </td>
                    </tr>
                    <tr>
                        <td>'.$this->l('Billing address mode:').'</td>                                          
                        <td>ReadOnly <input type="radio" value="ReadOnly" name="MPAY24_BILLING_ADDRESS_MODE" title="billingReadOnly"';
                        if (Configuration::get("MPAY24_BILLING_ADDRESS_MODE") == "ReadOnly") 
                            $html.= ' checked';
                        $html.= '></td>
                        <td colspan="2">ReadWrite <input type="radio" value="ReadWrite" name="MPAY24_BILLING_ADDRESS_MODE" title="billingReadWrite"';
                        if (Configuration::get("MPAY24_BILLING_ADDRESS_MODE") == "ReadWrite") 
                            $html.= ' checked';
                        $html.= '></td>
                    </tr>
                    </table>
                    <p class="center"><input class="button" type="submit" name="submitPaymentSystems" value="'.$this->l('Save settings').'" /></p>
                </div>
            <div class="tab-page" id="step2">
                <h4 class="tab"><img src="../img/admin/appearance.gif" /> '.$this->l('Design settings for the mPAY24 pay page').'</h2>
                    <fieldset>
                        <legend>Order design settings for the mPAY24 pay page</legend>
                        <table>
                            <tr>
                                <td>Order Description:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_DESCR" value="'.Configuration::get("MPAY24_ORDER_DESCR").'"></td>
                            </tr>
                            <tr>
                                <td>Order Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_S" value="'.Configuration::get("MPAY24_ORDER_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Logo Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_LOGO_S" value="'.Configuration::get("MPAY24_ORDER_LOGO_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Page Header Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_PAGE_HS" value="'.Configuration::get("MPAY24_ORDER_PAGE_HS").'"></td>
                            </tr>
                            <tr>
                                <td>Order Page Caption Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_PAGE_CS" value="'.Configuration::get("MPAY24_ORDER_PAGE_CS").'"></td>
                            </tr>
                            <tr>
                                    <td>Order Page Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_PAGE_S" value="'.Configuration::get("MPAY24_ORDER_PAGE_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Input Fields Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_IF_S" value="'.Configuration::get("MPAY24_ORDER_IF_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Drop-Down Lists Style:</td>                                              
                                <td><input size="70" type="text" name="MPAY24_ORDER_DD_LISTS_S" value="'.Configuration::get("MPAY24_ORDER_DD_LISTS_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Buttons Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_BUTTONS_S" value="'.Configuration::get("MPAY24_ORDER_BUTTONS_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Errors Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_ERRORS_S" value="'.Configuration::get("MPAY24_ORDER_ERRORS_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Success Title Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_ST_S" value="'.Configuration::get("MPAY24_ORDER_ST_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Error Title Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_ET_S" value="'.Configuration::get("MPAY24_ORDER_ET_S").'"></td>
                            </tr>
                            <tr>
                                <td>Order Footer Style:</td>                                          
                                <td><input size="70" type="text" name="MPAY24_ORDER_FOOTER_S" value="'.Configuration::get("MPAY24_ORDER_FOOTER_S").'"></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br /><br />
                    <fieldset>
                                <legend>Shopping cart design settings for the mPAY24 pay page</legend>
                                <table>
                                    <tr>
                                        <td>Shopping Cart Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_H" value="'.Configuration::get("MPAY24_SC_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Header Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_HS" value="'.Configuration::get("MPAY24_SC_HS").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_S" value="'.Configuration::get("MPAY24_SC_S").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Caption Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_CS" value="'.Configuration::get("MPAY24_SC_CS").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Number Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_NUMBER_H" value="'.Configuration::get("MPAY24_SC_NUMBER_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Number Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_NUMBER_S" value="'.Configuration::get("MPAY24_SC_NUMBER_S").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Product Number Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_PRODUCT_NUMBER_H" value="'.Configuration::get("MPAY24_SC_PRODUCT_NUMBER_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Product Number Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_PRODUCT_NUMBER_S" value="'.Configuration::get("MPAY24_SC_PRODUCT_NUMBER_S").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Description Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_DESCRIPTION_H" value="'.Configuration::get("MPAY24_SC_DESCRIPTION_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Description Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_DESCRIPTION_S" value="'.Configuration::get("MPAY24_SC_DESCRIPTION_S").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Package Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_PACKAGE_H" value="'.Configuration::get("MPAY24_SC_PACKAGE_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Package Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_PACKAGE_S" value="'.Configuration::get("MPAY24_SC_PACKAGE_S").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Quantity Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_QUANTITY_H" value="'.Configuration::get("MPAY24_SC_QUANTITY_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Quantity Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_QUANTITY_S" value="'.Configuration::get("MPAY24_SC_QUANTITY_S").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Item Price Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_ITEM_PRICE_H" value="'.Configuration::get("MPAY24_SC_ITEM_PRICE_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Item Price Style:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_ITEM_PRICE_S" value="'.Configuration::get("MPAY24_SC_ITEM_PRICE_S").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Price Header:</td>                                              
                                        <td><input size="70" type="text" name="MPAY24_SC_PRICE_H" value="'.Configuration::get("MPAY24_SC_PRICE_H").'"></td>
                                    </tr>
                                    <tr>
                                        <td>Shopping Cart Price Style:</td>                                              
                                        <td><input size="70" type="text" name=MPAY24_SC_PRICE_S value="'.Configuration::get("MPAY24_SC_PRICE_S").'"></td>
                                    </tr>
                                </table>
                            </fieldset>
                            <br /><br />
                            <fieldset>
                                    <legend>Other costs esign settings for the mPAY24 pay page</legend>
                                    <table>
                                        <tr>
                                            <td>Sub Total Header:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_SUB_TOTAL_H" value="'.Configuration::get("MPAY24_SUB_TOTAL_H").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Sub Total Header Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_SUB_TOTAL_HS" value="'.Configuration::get("MPAY24_SUB_TOTAL_HS").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Sub Total Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_SUB_TOTAL_S" value="'.Configuration::get("MPAY24_SUB_TOTAL_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Discount Header:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_DISCOUNT_H" value="'.Configuration::get("MPAY24_DISCOUNT_H").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Discount Header Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_DISCOUNT_HS" value="'.Configuration::get("MPAY24_DISCOUNT_HS").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Discount Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_DISCOUNT_S" value="'.Configuration::get("MPAY24_DISCOUNT_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Costs Header:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_SHIPPING_COSTS_H" value="'.Configuration::get("MPAY24_SHIPPING_COSTS_H").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Costs Header Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_SHIPPING_COSTS_HS" value="'.Configuration::get("MPAY24_SHIPPING_COSTS_HS").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Costs Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_SHIPPING_COSTS_S" value="'.Configuration::get("MPAY24_SHIPPING_COSTS_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Tax Header:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_TAX_H" value="'.Configuration::get("MPAY24_TAX_H").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Tax Header Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_TAX_HS" value="'.Configuration::get("MPAY24_TAX_HS").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Tax Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_TAX_S" value="'.Configuration::get("MPAY24_TAX_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Number Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_NUMBER_S" value="'.Configuration::get("MPAY24_ITEM_NUMBER_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Product Number Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_PRODUCT_NUMBER_S" value="'.Configuration::get("MPAY24_ITEM_PRODUCT_NUMBER_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Description Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_DESCRIPTION_S" value="'.Configuration::get("MPAY24_ITEM_DESCRIPTION_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Package Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_PACKAGE_S" value="'.Configuration::get("MPAY24_ITEM_PACKAGE_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Quantity Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_QUANTITY_S" value="'.Configuration::get("MPAY24_ITEM_QUANTITY_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Item Price Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_ITEM_PRICE_S" value="'.Configuration::get("MPAY24_ITEM_ITEM_PRICE_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Price Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_PRICE_S" value="'.Configuration::get("MPAY24_ITEM_PRICE_S").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Style Odd:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_ITEM_PRICE_S_ODD" value="'.Configuration::get("MPAY24_ITEM_ITEM_PRICE_S_ODD").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Item Style Even:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_ITEM_ITEM_PRICE_S_EVEN" value="'.Configuration::get("MPAY24_ITEM_ITEM_PRICE_S_EVEN").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Price Header:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_PRICE_H" value="'.Configuration::get("MPAY24_PRICE_H").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Price Header Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_PRICE_HS" value="'.Configuration::get("MPAY24_PRICE_HS").'"></td>
                                        </tr>
                                        <tr>
                                            <td>Price Style:</td>                                              
                                            <td><input size="70" type="text" name="MPAY24_PRICE_S" value="'.Configuration::get("MPAY24_PRICE_S").'"></td>
                                        </tr>
                                    </table>
                                </fieldset>
                                <p class="center"><input class="button" type="submit" name="submitDesignSettings" value="'.$this->l('Save settings').'" /></p>
                </div>
            </div>
            <div class="clear"></div>
            <script type="text/javascript">
                function loadTab(id){}
                setupAllTabs();
            </script>
        </form>';
        }
        return $html;
    }
	
    public function uninstall()
    {       
    	/* Delete all configurations */
        Configuration::deleteByName('MPAY24_MERCHANT_ID');
        Configuration::deleteByName('MPAY24_SOAP_PASS');
        Configuration::deleteByName('MPAY24_TEST_MODE');
        Configuration::deleteByName('MPAY24_PROXY_HOST');
        Configuration::deleteByName('MPAY24_PROXY_PORT');
        Configuration::deleteByName('MPAY24_BILLING_ADDRESS_MODE');
        Configuration::deleteByName('MPAY24_ACTIVE_PAYMENT_SYSTEMS');
        Configuration::deleteByName('MPAY24_PAYMENT_SYSTEMS_CHECKED');
        Configuration::deleteByName('MPAY24_PAYMENT_SYSTEMS_SHOP');
        Configuration::deleteByName('MPAY24_ALL_ACTIVE_PS');
        Configuration::deleteByName('MPAY24_PAYMENT_SYSTEMS_ENABLED');
    	
        return parent::uninstall();
    }
    
	function hookPayment($params)
	{
		global $smarty;
		
		$smarty->assign(array(
		            'this_path' => $this->_path,
		            'this_path_ssl' => Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/"));
                            
        $ps = explode(",", Configuration::get('MPAY24_PAYMENT_SYSTEMS_SHOP'));   

        $FEpaymentSystems = "";
        foreach ($ps as $p)
            if($p != '')
                $FEpaymentSystems.= '<img src="modules/mpay24/images/'.$p.'.png" hspace="2" vspace="2" alt="'.$p.'">';
                            
		$smarty->assign('payment_systems', $FEpaymentSystems);
		
		return $this->display(__FILE__, 'payment.tpl');
	}
    
   function hookAdminOrder($params)
    {
    	$typeAmount = $this->l('You must type an amount!');
    	$rightSyntax = $this->l('The right syntax of the amount is 12345.67!');
    	$maxAmount1 = $this->l('The maximum amount you are able to ');
    	$maxAmount2 = $this->l(' is ');

    	$msg = "";
    	$html = "<SCRIPT LANGUAGE=\"JavaScript\">
	        function checkAmount(maxAmount, operation){
		        var amount=document.getElementsByName('postedAmount')[0].value;
		        var fmaxAmount = parseFloat(maxAmount);
                var famount = parseFloat(amount);
		        var bool=true; 
		        if(amount=='') {
		            alert('".$typeAmount."');
		            bool=false;
		        }
		
		        if(!amount.match(/\d+\.\d+/)){
		            alert('".$rightSyntax."');
		            bool=false;
		        }
		                
		        if(famount > fmaxAmount){
		            alert('".$maxAmount1."'+operation+'".$maxAmount2."' +maxAmount);
		            bool=false;
		        }
		
		        return bool;
            }
        </SCRIPT>";
    	include("api/prestaShop.php");
        if(Configuration::get('MPAY24_TEST_MODE') == 'true') 
            $mode = true;
        else
            $mode = false;

        $prestaShop = new prestaShop(Configuration::get('MPAY24_MERCHANT_ID'), Configuration::get('MPAY24_SOAP_PASS'), $mode, Configuration::get('MPAY24_PROXY_HOST'), Configuration::get('MPAY24_PROXY_PORT'));
                    
        $result = Db::getInstance()->getRow(' SELECT * FROM '._DB_PREFIX_.'orders WHERE `id_order` = '.intval($params['id_order']));
        $trans = Db::getInstance()->getRow(' SELECT * FROM `'._DB_PREFIX_.'mpay24_order` WHERE `TID` = '.$result['id_order']);

        $customer = new Customer((int)$result['id_customer']);
        $secure_key = md5($result['id_order'].$result['total_paid'].$trans['CURRENCY'].$result['id_customer']);
        	
        $module = Db::getInstance()->getRow(' SELECT `module` FROM '._DB_PREFIX_.'orders WHERE `id_order` = '.intval($params['id_order']));
        
        $html .= '<br />
                 <fieldset style="width: 400px">
                 <legend>'.$this->l('mPAY24 API').'</legend><br>';
        if ($module['module'] == 'mpay24') {
        	$url = $_SERVER['PHP_SELF'] . "?tab=".Tools::getValue('tab')."&token=" . Tools::getValue('token') . "&vieworder&id_order=" . $result['id_order'];
            if (isset($_POST['clear'])) {
                $clearamount = $_POST['postedAmount']*100;
                $r = $prestaShop->clearAmount($result['id_order'], $clearamount);
                if($r->getGeneralResponse()->getStatus() != "OK")
                    $url .= "&error=" . urlencode($r->getGeneralResponse()->getReturnCode());
                header("Location: " . $url);
             }
             if (isset($_POST['credit'])) {
                $creditamount = $_POST['postedAmount']*100;
                $r = $prestaShop->creditAmount($result['id_order'], $creditamount);
                if($r->getGeneralResponse()->getStatus() != "OK")
                    $url.= "&error=" . urlencode($r->getGeneralResponse()->getReturnCode());
                header("Location: " . $url);
              }
              if (isset($_POST['cancel'])) {
                $r = $prestaShop->cancelTransaction($result['id_order']);
                if($r->getGeneralResponse()->getStatus() != "OK")
                    $url .= "&error=" . urlencode($r->getGeneralResponse()->getReturnCode());
                header("Location: " . $url);
              }
            if (isset($_POST['updateStatus'])) {
                $r = $prestaShop->updateTransactionStatus($result['id_order']);
                if($r->getGeneralResponse()->getStatus() != "OK")
                    $url .= "&error=" . urlencode($r->getGeneralResponse()->getReturnCode());
                if($r->getParam('USER_FIELD') == 'Prestashop 1.0.1 ' . $secure_key)
                    $prestaShop->updateTransaction($result['id_order'], $r->getParams(), $r->getParam("shippingConfirmed"));
                else{
                	$args = array();
                	$args['STATUS'] = 'OK';
                	$args['TSTATUS'] = 'NOT FOUND';
                	$args['MPAYTID'] = $this->l('The transaction does not exist!') . " - ". $secure_key;
                	$args['P_TYPE'] = $this->l('The transaction does not exist!');
                	$args['BRAND'] = $this->l('The transaction does not exist!');
                	$args['CURRENCY'] = $trans['CURRENCY'];
                	$args['CUSTOMER'] = $customer->firstname . " " . $customer->lastname;
                	$args['APPR_CODE'] = $this->l('The transaction does not exist!');
                	$prestaShop->updateTransaction($result['id_order'], $args, true);
                }
                header("Location: " . $url);
              }
                            
              if (isset($_GET['error']))
                $html .= '<h2><font color="#FF0000">'.urldecode($_GET['error']).'</font></h2>';
              $html .= '
              '.$this->l('State:').'
              ';
              switch($trans['STATUS']){
                case 'RESERVED':
                    $msg = '<b>'.$this->l('Authorized with amount: ').''. number_format(number_format($trans['AMOUNT_RESERVED'],2,'.','')/100,2,'.','') .' '.$trans['CURRENCY'].'</b>';
                    $msg .='<br><br><font color="#FF0000">'.$this->l('The amount must have the following format 12345.67!').'</font>';
                    $msg .='<br><br><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="clear_credit" style="text-align:center;">
                         <label>'.$this->l('Amount to capture: ').'</label><input type="text" name="postedAmount" value="'. number_format(number_format($trans['AMOUNT_RESERVED'],2,'.','')/100,2,'.','') .'"/>
                         <input type="submit" name="clear" value="'.$this->l('Capture').'" class="button" onclick="return checkAmount(\''. number_format(number_format($trans['AMOUNT_RESERVED'],2,'.','')/100,2,'.','') .'\', \''.$this->l('capture').'\')"/>';
                            $msg .='</form><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="cancel" style="text-align:center;">
                            <br /><center><input type="submit" name="cancel" value="'.$this->l('Cancel the transaction!').'" class="button" onclick="return confirm(\''.$this->l("Are you sure you want to cancel the transaction?") .'"/></center>';
                         $msg .='</form><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="updateStatus" style="text-align:center;">
                            <br /><center><input type="submit" name="updateStatus" value="'.$this->l('Update transaction status').'" class="button"/></center>';
                         $msg .='</form>';
                    break;
                case 'BILLED':
                    $msg = '<b>'.$this->l('Captured with amount: ').''. number_format(number_format($trans['AMOUNT_BILLED'],2,'.','')/100,2,'.','') .' '.$trans['CURRENCY'].'</b>';
                    $msg .='<br><br><font color="#FF0000">'.$this->l('The amount must have the following format 12345.67!').'</font>';
                    $msg .='<br><br><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="clear_credit" style="text-align:center;">
                         <label>'.$this->l('Amount to refund: ').'</label><input type="text" name="postedAmount" value="'. number_format(number_format($trans['AMOUNT_BILLED'],2,'.','')/100,2,'.','') .'"/>
                         <input type="submit" name="credit" value="'.$this->l('Refund').'" class="button" onclick="return checkAmount(\''. number_format(number_format($trans['AMOUNT_BILLED'],2,'.','')/100,2,'.','') .'\', \''.$this->l('refund').'\')"/>
                         <br /><br /><center></form><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="cancel" style="text-align:center;">
                         <input type="submit" name="updateStatus" value="'.$this->l('Update transaction status').'" class="button"/></center>';
                         $msg .='</form>';
                    break;
                case 'CREDITED':
                    $msg = '<b>'.$this->l('Refunded with amount: ').''. number_format(number_format($trans['AMOUNT_CREDITED'],2,'.','')/100,2,'.','') .' '.$trans['CURRENCY'].'</b>
                    <br /><br /><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="updateStatus" style="text-align:center;">
                    <center><input type="submit" name="updateStatus" value="'.$this->l('Update transaction status').'" class="button"/></center></form>';
                    break;  
                case 'REVERSED':
                    $msg = '<b>'.$this->l('Transaction canceled').'</b>
                    <br /><br /><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="updateStatus" style="text-align:center;">
                    <center><input type="submit" name="updateStatus" value="'.$this->l('Update transaction status').'" class="button"/></center></form>';
                    break;   
                case 'ERROR':
                    $msg = '<b>'.$this->l('Transaction error').'</b>
                    <br /><br /><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="updateStatus" style="text-align:center;">
                    <center><input type="submit" name="updateStatus" value="'.$this->l('Update transaction status').'" class="button"/></center></form>';
                    break;  
                case 'NOT FOUND':
                    $msg = '<b>'.$this->l('Not found').'</b>
                    <br /><br /><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="updateStatus" style="text-align:center;">
                    <center><input type="submit" name="updateStatus" value="'.$this->l('Update transaction status').'" class="button"/></center></form>';
                    break;          
                default:
                	$msg = '<b>'.$this->l('Unknown').'</b>
                    <br /><br /><form action="index.php?tab='.Tools::getValue('tab').'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getValue('token').'" method="post" name="updateStatus" style="text-align:center;">
                    <center><input type="submit" name="updateStatus" value="'.$this->l('Update transaction status').'" class="button"/></center></form>';
                    break;
              }          
                $html .= ''.$msg.'';
              }
              else { $html .= ''.$this->l('No transactions for this order.').'<br>'; }
              if($mode)
                $html .= '<br><a href="https://test.mpay24.com/web/de/mpay24-zahlungsloesung.html" target="_blank" style="color: blue;">'.$this->l('mPAY24 Merchant Interface').' - TEST</a></fieldset>';
              else
                $html .= '<br><a href="https://www.mpay24.com/web/de/mpay24-zahlungsloesung.html" target="_blank" style="color: blue;">'.$this->l('mPAY24 Merchant Interface').'</a></fieldset>';
              return $html;
    }
	
	public function execPayment($cart)
	{
		if (!$this->active)
		  return ;
		  
		global $cookie, $smarty;

	    $smarty->assign(array(
	        'cust_currency' => $cart->id_currency,
	        'currency' => (int)$cart->id_currency,
	        'total' => $cart->getOrderTotal(true, Cart::BOTH),
	        'this_path' => $this->_path,
            'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/',
	        'mode' => 'payment/'
	    ));
		
		return $this->display(__FILE__, 'payment_execution.tpl');
	}
	
	function hookInvoice($params)
	{
		$id_order = $params['id_order'];
		
		global $smarty;
		$mpay24OrderDetails = $this->readMpay24OrderDetails($id_order);
		
		if($mpay24OrderDetails){
			$smarty->assign(array(
			    'mpaytid' => $mpay24OrderDetails['MPAYTID'],
				'p_type' => $mpay24OrderDetails['P_TYPE'],
				'brand' => $mpay24OrderDetails['BRAND'],
				'appr_code' => $mpay24OrderDetails['APPR_CODE'],
				'this_page' => $_SERVER['REQUEST_URI'],
				'this_path' => $this->_path,
	            'this_path_ssl' => Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/"));
			return $this->display(__FILE__, 'invoice_block.tpl');
		} else
		  return $mpay24OrderDetails;
	}
	
	function readMpay24OrderDetails($id_order)
	{
		$db = Db::getInstance();
		$result = $db->ExecuteS('
		SELECT * FROM `'._DB_PREFIX_.'mpay24_order`
		WHERE `TID` ="'.intval($id_order).'";');

        if(array_key_exists(0, $result))
		  return $result[0];
		else
		  return false;
	}
	
    function preparePayment()
    {
        global $smarty, $cart, $cookie;
 
            $total = $cart->getOrderTotal();
    
            $smarty->assign(array(
                'this_path' => $this->_path,
                'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/',
                'total' => $total));
    }
    
    function executePayment($cookie)
    {
        global $smarty, $cart, $cookie;
        include("api/prestaShop.php");
                
        $currency = new Currency((int)($cart->id_currency));
        $customer = new Customer((int)$cart->id_customer);
        $secure_key = md5((int)$this->currentOrder.$cart->getOrderTotal().$currency->iso_code.(int)$cart->id_customer);
                                                        
        // Call payment validation method
        $this->validateOrder((int)($cart->id), _MPAY24_RESERVED_ORDER_STATUS_, (float)($cart->getOrderTotal(true, Cart::BOTH)), $this->displayName, '', array('transaction_id' => 'N/A', 'payment_status' => 'AUTHORIZATION', 'pending_reason' => ''), $cart->id_currency, false, $cart->secure_key);
        
        Db::getInstance()->Execute("
            INSERT INTO `"._DB_PREFIX_."mpay24_order` (
				`MPAYTID` ,
				`TID` ,
				`STATUS` ,
				`AMOUNT_RESERVED` ,
				`AMOUNT_BILLED` ,
				`AMOUNT_CREDITED` ,
				`CURRENCY` ,
				`P_TYPE` ,
				`BRAND` ,
				`CUSTOMER` ,
				`APPR_CODE` ,
				`CREATED_AT`
				)
				VALUES (
				'UNKNOWN - " . $secure_key . "', 
				'".(int)$this->currentOrder."', 
				'UNKNOWN', 
				'" . (float)($cart->getOrderTotal(true, Cart::BOTH))*100 . "', 
				'0', 
				'0', 
				'".$currency->iso_code."', 
				'UNKNOWN', 
				'UNKNOWN', 
				'".$customer->firstname . " " . $customer->lastname ."', 
				'UNKNOWN',
				NOW()
				);
        ");
        
        $designSettings = array();
        $shopSettings = array();
        
                                               
        $result = Db::getInstance()->ExecuteS("SELECT name, value FROM `"._DB_PREFIX_."configuration`;");
        
        foreach($result AS $row)
               if(substr($row['name'], 0, 12) == 'MPAY24_ORDER' || substr($row['name'], 0, 9) == 'MPAY24_SC' || substr($row['name'], 0, 21) == 'MPAY24_SHIPPING_COSTS'
                                  || substr($row['name'], 0, 16) == 'MPAY24_SUB_TOTAL' || substr($row['name'], 0, 15) == 'MPAY24_DISCOUNT' || substr($row['name'], 0, 10) == 'MPAY24_TAX'
                                  || substr($row['name'], 0, 11) == 'MPAY24_ITEM' || substr($row['name'], 0, 12) == 'MPAY24_PRICE')
                    $designSettings[$row['name']] = $row['value'];
               elseif(substr($row['name'], 0, 6) == 'MPAY24')
                    $shopSettings[$row['name']] = $row['value'];
        
        $currency = $this->getCurrency((int)$cart->id_currency);
                        
        if(Configuration::get('MPAY24_TEST_MODE') == "true")
            $mode = true;
        else
            $mode = false;

        $order = new Order((int)$this->currentOrder);
        $successURL = __PS_BASE_URI__.'order-confirmation.php?id_cart='.(int)($cart->id).'&amp;id_module='.(int)($this->id).
                                    '&amp;id_order='.(int)($this->currentOrder).'&amp;key='.$order->secure_key.'&amp;customerID='.$cookie->id_customer;        
            
        $prestaShop = new prestaShop(Configuration::get('MPAY24_MERCHANT_ID'), Configuration::get('MPAY24_SOAP_PASS'), $mode, Configuration::get('MPAY24_PROXY_HOST'), Configuration::get('MPAY24_PROXY_PORT'));
        $prestaShop->setPaymentVariables($cart, $designSettings, $shopSettings, (int)$this->currentOrder);
        $result = $prestaShop->pay();
        
	    if($result->getGeneralResponse()->getStatus() == "OK"){
	        header('Location: ' . $result->getLocation() );
	    }else{
	    	global $cookie, $smarty;

            include(dirname(__FILE__).'/../../header.php');
            $smarty->assign('status', $result->getGeneralResponse()->getStatus());
            $smarty->assign('returnCode', $result->getGeneralResponse()->getReturnCode());
            $smarty->assign('externalStatus', $result->getExternalStatus());
            echo $this->display(__FILE__, 'error.tpl');
            include_once(dirname(__FILE__).'/../../footer.php');
            die;
	   }                                        
    }
}
?>
