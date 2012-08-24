<?php
include_once("MPay24Shop.php");

    class prestaShop extends MPay24Shop {

    	var $orderID;
    	var $endAmount;
    	var $currency;
    	var $lang;
    	var $transaction;
    	var $designSettings = array();
    	var $shopSettings = array();
    	var $secureKey;
    	var $products = array();
    	var $subTotal;
    	var $shippingCosts;
    	var $tax;
    	var $discounts = array();
    	var $customer;
    	var $shippingAddress;
    	var $billingAddress;
    	var $addr_id;
    	var $cart;
    	
        function updateTransaction($tid, $args, $shippingConfirmed){
        	global $cookie;

            Db::getInstance()->Execute("
	            UPDATE `"._DB_PREFIX_."mpay24_order` SET 
	                `MPAYTID` = '".$args['MPAYTID']."',
	                `STATUS` = '".$args['TSTATUS']."',
	                `CURRENCY` = '".$args['CURRENCY']."',
	                `P_TYPE` = '".$args['P_TYPE']."',
	                `BRAND` = '".$args['BRAND']."',
	                `CUSTOMER` = '".$args['CUSTOMER']."',
	                `APPR_CODE` = '".$args['APPR_CODE']."'
	                WHERE `TID` = '".$tid."'
            ");
            
            $order = new Order((int)$tid);
            $errors = array();
            
            if($args['STATUS'] == 'OK'){
                if (Configuration::get("MPAY24_BILLING_ADDRESS_MODE") == "ReadWrite"){
                	if(array_key_exists("BILLING_ADDR", $args) && $args['BILLING_ADDR'] != ''){
                		$billingAddress = DOMDocument::loadXML(trim($args['BILLING_ADDR']));
                        $billingAddress->saveXML();
                                
		                $name = $billingAddress->getElementsByTagName("Name")->item(0)->nodeValue;
		                $street = $billingAddress->getElementsByTagName("Street")->item(0)->nodeValue;
		                $street2 = $billingAddress->getElementsByTagName("Street2")->item(0)->nodeValue;
		                $zip = $billingAddress->getElementsByTagName("Zip")->item(0)->nodeValue;
		                $city = $billingAddress->getElementsByTagName("City")->item(0)->nodeValue;
		                $email = $billingAddress->getElementsByTagName("Email")->item(0)->nodeValue;
		                $countryCode = $billingAddress->getElementsByTagName("Country")->item(0)->getAttribute("code");
		                
		                $addrAlreadySaved = false;
		                $addressID = 0;
		                $addresses = Db::getInstance()->ExecuteS('
					        SELECT * FROM `'._DB_PREFIX_.'address`
					        WHERE `id_customer` ="'.$this->customer->id.'";');
		                
		                foreach($addresses as $address)
		                	if($address['id_country'] == Country::getByIso($countryCode) && 
		                	     (($address['lastname'] == substr($name, strpos($name, " ")+1) && $address['firstname'] == substr($name, 0, strpos($name, " "))) 
		                	         || $address['lastname'] == $name) &&
		                	     $address['address1'] == $street &&
		                	     $address['address2'] == $street2 &&
		                	     $address['postcode'] == $zip &&
		                	     $address['city'] == $city
		                	     ){
		                	         $addrAlreadySaved = true;
		                	         $addressID = $address['id_address'];
		                	         break;
		                	     }
		                	     
		                
		                if(!$addrAlreadySaved){
			                $addr = new Address();		                
			                $addr->id_country = Country::getByIso($countryCode);
			                $addr->id_customer = $this->customer->id;
			               			                
			                $addr->alias = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')));
			                if(strpos($name, " ")){
			                  $addr->firstname = substr($name, 0, strpos($name, " "));
			                  $addr->lastname = substr($name, strpos($name, " ")+1);
			                } else
			                  $addr->lastname = $name;
			                $addr->address1 = $street;
			                $addr->address2 = $street2;
			                $addr->postcode = $zip;
			                $addr->city = $city;
			                
			                $errors = $addr->validateControler();
			                $test = '';
	                	   if(!empty($errors)){
		                	   foreach($errors as $key => $value){
		                          $test.= $value . "\n";
		                        }
		                        
	                            Configuration::updateValue("MPAY24_BILLING_ADDRESS_MODE", "ReadOnly");
	                            Mail::Send(
	                                  intval(4), 
	                                  'contact', 
	                                  'ATTENTION - The invoice address could not be saved!', 
	                                  array(
	                                      '{message}' => stripslashes($test)
	                                  ), 
	                                  Configuration::get("PS_SHOP_EMAIL")
	                            );
	                        }
			                
	                          
			                $addr->save();

			                $this->cart->id_address_invoice = (int)($addr->id);
	                        $this->cart->update();   
	
	                        $order->id_address_invoice = (int)($addr->id);
	                        $order->update();
		                } elseif($addressID !== 0) {
		                	$this->cart->id_address_invoice = (int)($addressID);
                            $this->cart->update();   
    
                            $order->id_address_invoice = (int)($addressID);
                            $order->update();
		                }
                	} else {
                		Configuration::updateValue("MPAY24_BILLING_ADDRESS_MODE", "ReadOnly");
                                Mail::Send(
                                      intval(4), 
                                      'contact', 
                                      'ATTENTION - The invoice address was not returned from mPAY24!!!', 
                                      array(
                                          '{message}' => stripslashes(
                                                "It is possible that the invoice address for the order with the ID '" . 
				                                  $tid . "' was changed from the customer on the mPAY24 pay page. However the new address was not saved into your shop! 
				                                  The invoice address mode was seted to 'ReadOnly'! In case you really want to be able to use the 'ReadWrite' mode 
				                                  the variable 'BILLING_ADDR' must be turned on for the 'TRANSACTIONSTATUS'-request by mPAY24.
				                                  Please refer to the mPAY24 support (support@mpay24.com) with your MERCHANT ID '" 
				                                  . Configuration::get("MPAY24_MERCHANT_ID"). "'!"
                                                                                
                                                                        )
                                      ), 
                                      Configuration::get("PS_SHOP_EMAIL")
                                );
                	}
                }
            
	            switch($args['TSTATUS']){
	            	case 'RESERVED':
	            		  Db::getInstance()->Execute("
			                UPDATE `"._DB_PREFIX_."mpay24_order` SET 
			                    `AMOUNT_RESERVED` = '".$args['PRICE']."'
			                    WHERE `TID` = '".$tid."'
			            ");
	            		  if($order->getCurrentState() != _MPAY24_RESERVED_ORDER_STATUS_)
	            		  	$order->setCurrentState(_MPAY24_RESERVED_ORDER_STATUS_);
	            		break;
	            	case 'BILLED':
	            		  Db::getInstance()->Execute("
	                        UPDATE `"._DB_PREFIX_."mpay24_order` SET 
	                            `AMOUNT_BILLED` = '".$args['PRICE']."'
	                            WHERE `TID` = '".$tid."'
	                    ");
	            		  if($order->getCurrentState() != _PS_OS_PAYMENT_)
	            		  	$order->setCurrentState(_PS_OS_PAYMENT_);
	            		break;
	            	case 'CREDITED':
	            		  Db::getInstance()->Execute("
	                        UPDATE `"._DB_PREFIX_."mpay24_order` SET 
	                            `AMOUNT_CREDITED` = '".$args['PRICE']."'
	                            WHERE `TID` = '".$tid."'
	                    ");
	            		  if($order->getCurrentState() != _PS_OS_REFUND_)
	            		  	$order->setCurrentState(_PS_OS_REFUND_);
	            		break;
	            	case 'REVERSED':
	            				if($order->getCurrentState() != _PS_OS_CANCELED_)
	                      $order->setCurrentState(_PS_OS_CANCELED_);
	                    break;
								case 'ERROR':
											if($order->getCurrentState() != _PS_OS_ERROR_)
												$order->setCurrentState(_PS_OS_ERROR_);
											break;
	            	default:
	            		break;
	            }
        } else
                $order->setCurrentState(_PS_OS_ERROR_);            
        	

        } 
        
        function getTransaction($tid){
	        $order = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'mpay24_order` WHERE `TID`LIKE "'.$tid.'"');
	        	        
	        $transaction = new Transaction($tid);
	        $transaction->MPAYTID = $order[0]['MPAYTID'];
	        $transaction->PRICE = $order[0]['AMOUNT_RESERVED'];
	        $transaction->CURRENCY = $order[0]['CURRENCY'];
	        $transaction->CUSTOMER = $order[0]['CUSTOMER'];
	
	        return $transaction;    
        }
        
        function createProfileOrder($tid){}
        function createExpressCheckoutOrder($tid){}
        function createFinishExpressCheckoutOrder($tid, $s, $a, $c){}
        function write_log($operation, $info_to_log){
        	global $cookie;
        	
        	Db::getInstance()->Execute(
        	"INSERT INTO `"._DB_PREFIX_."mpay24_debug` 
                       (
                       `calledMethod`,
                       `type`, 
                       `data`
                       ) VALUES
                   (
                   '".$operation."',
                   '".substr($info_to_log, 0, strpos($info_to_log," "))."',
                   '".str_replace(array("\"", "'"), array("\\\"", "\'"), utf8_encode(substr($info_to_log, strpos($info_to_log," ", strpos($info_to_log," ")+1))))."'
                                      );"
        	);
        	
        }
        function createSecret($tid, $amount, $currency, $timeStamp){}
        function getSecret($tid){}

        function createTransaction(){
        	global $cookie;
        	
	        $this->transaction = new Transaction($this->orderID);
	        $this->transaction->PRICE = $this->endAmount;
	        $this->transaction->CURRENCY = $this->currency;
	        $this->transaction->LANGUAGE = strtoupper($this->lang);
	        $this->transaction->CUSTOMER = $this->customer->firstname . " " . $this->customer->lastname;
	        
	        return $this->transaction;
        }
        
        function createMDXI($transaction){
        	global $cookie;
        	
            $mdxi = new ORDERXML();
            
            $mdxi->Order->setLogoStyle(utf8_encode($this->designSettings['MPAY24_ORDER_LOGO_S']));
	        $mdxi->Order->setPageHeaderStyle(utf8_encode($this->designSettings['MPAY24_ORDER_PAGE_HS']));
	        $mdxi->Order->setPageCaptionStyle(utf8_encode($this->designSettings['MPAY24_ORDER_PAGE_CS']));
	        $mdxi->Order->setPageStyle(utf8_encode($this->designSettings['MPAY24_ORDER_PAGE_S']));
	        $mdxi->Order->setFooterStyle(utf8_encode($this->designSettings['MPAY24_ORDER_FOOTER_S']));
	        $mdxi->Order->setStyle(utf8_encode($this->designSettings['MPAY24_ORDER_S']));
	        $mdxi->Order->setInputFieldsStyle(utf8_encode($this->designSettings['MPAY24_ORDER_IF_S']));
	        $mdxi->Order->setDropDownListsStyle(utf8_encode($this->designSettings['MPAY24_ORDER_DD_LISTS_S']));
	        $mdxi->Order->setButtonsStyle(utf8_encode($this->designSettings['MPAY24_ORDER_BUTTONS_S']));
	        $mdxi->Order->setErrorsStyle(utf8_encode($this->designSettings['MPAY24_ORDER_ERRORS_S']));
	        $mdxi->Order->setErrorTitleStyle(utf8_encode($this->designSettings['MPAY24_ORDER_ET_S']));
	        $mdxi->Order->setSuccessTitleStyle(utf8_encode($this->designSettings['MPAY24_ORDER_ST_S']));
            
	        $mdxi->Order->UserField = "Prestashop 1.1.3 ".md5($transaction->TID.number_format($transaction->PRICE,2,'.','').$this->transaction->CURRENCY.$this->customer->id);
	         
            $mdxi->Order->Tid = utf8_encode($transaction->TID);
        
            $mdxi->Order->TemplateSet = "WEB";
            $mdxi->Order->TemplateSet->setLanguage($transaction->LANGUAGE);
	                    
            $i = 1;
	        $paymentSystems = explode(",", $this->shopSettings['MPAY24_PAYMENT_SYSTEMS_CHECKED']) ;
	        
	        foreach($paymentSystems as $key => $value){
		        if($value == 'MASTERCARD' || $value == 'VISA' || $value == 'JCB' || $value == 'AMEX' || $value == 'DINERS'){
		            $mdxi->Order->PaymentTypes->Payment($i)->setType("CC");
		            $mdxi->Order->PaymentTypes->Payment($i)->setBrand($value);     
		        } elseif($value == 'BA' || $value == 'BAWAG' || $value == 'ERSTE' || $value == 'HYPO' || $value == 'RZB' || $value == 'VOLKSBANK' || $value == 'ARZ'){
		            $mdxi->Order->PaymentTypes->Payment($i)->setType("EPS");
		            $mdxi->Order->PaymentTypes->Payment($i)->setBrand($value);
		        } elseif($value == 'ATOS' || $value == 'HOBEX-AT' || $value == 'HOBEX-DE' || $value == 'HOBEX-NL'){
		            $mdxi->Order->PaymentTypes->Payment($i)->setType("ELV");
		            $mdxi->Order->PaymentTypes->Payment($i)->setBrand($value);
		        } elseif($value != ''){
		            $mdxi->Order->PaymentTypes->Payment($i)->setType($value);
		        }
		        $i++;
	        }
	        
	        if($i > 2)
	           $mdxi->Order->PaymentTypes->setEnable($this->shopSettings['MPAY24_PAYMENT_SYSTEMS_ENABLED']);
	           
	        $mdxi->Order->ShoppingCart->setStyle(utf8_encode($this->designSettings['MPAY24_SC_S']));
	        $mdxi->Order->ShoppingCart->setHeader(utf8_encode($this->designSettings['MPAY24_SC_H']));
	        $mdxi->Order->ShoppingCart->setHeaderStyle(utf8_encode($this->designSettings['MPAY24_SC_HS']));
	        $mdxi->Order->ShoppingCart->setCaptionStyle(utf8_encode($this->designSettings['MPAY24_SC_CS']));
	        $mdxi->Order->ShoppingCart->setNumberHeader(utf8_encode($this->designSettings['MPAY24_SC_NUMBER_H']));
	        $mdxi->Order->ShoppingCart->setNumberStyle(utf8_encode($this->designSettings['MPAY24_SC_NUMBER_S']));
	        $mdxi->Order->ShoppingCart->setProductNrHeader(utf8_encode($this->designSettings['MPAY24_SC_PRODUCT_NUMBER_H']));
	        $mdxi->Order->ShoppingCart->setProductNrStyle(utf8_encode($this->designSettings['MPAY24_SC_PRODUCT_NUMBER_S']));
	        $mdxi->Order->ShoppingCart->setDescriptionHeader(utf8_encode($this->designSettings['MPAY24_SC_DESCRIPTION_H']));
	        $mdxi->Order->ShoppingCart->setDescriptionStyle(utf8_encode($this->designSettings['MPAY24_SC_DESCRIPTION_S']));
	        $mdxi->Order->ShoppingCart->setPackageHeader(utf8_encode($this->designSettings['MPAY24_SC_PACKAGE_H']));
	        $mdxi->Order->ShoppingCart->setPackageStyle(utf8_encode($this->designSettings['MPAY24_SC_PACKAGE_S']));
	        $mdxi->Order->ShoppingCart->setQuantityHeader(utf8_encode($this->designSettings['MPAY24_SC_QUANTITY_H']));
	        $mdxi->Order->ShoppingCart->setQuantityStyle(utf8_encode($this->designSettings['MPAY24_SC_QUANTITY_S']));
	        $mdxi->Order->ShoppingCart->setItemPriceHeader(utf8_encode($this->designSettings['MPAY24_SC_ITEM_PRICE_H']));
	        $mdxi->Order->ShoppingCart->setItemPriceStyle(utf8_encode($this->designSettings['MPAY24_SC_ITEM_PRICE_S']));
	        $mdxi->Order->ShoppingCart->setPriceHeader(utf8_encode($this->designSettings['MPAY24_SC_PRICE_H']));
	        $mdxi->Order->ShoppingCart->setPriceStyle(utf8_encode($this->designSettings['MPAY24_SC_PRICE_S']));
	        
	        $mdxi->Order->ShoppingCart->Description = utf8_encode(($this->designSettings['MPAY24_ORDER_DESCR']));
	           
	        for ($i = 1; $i <= sizeof($this->products); $i++){
		        $mdxi->Order->ShoppingCart->Item(($i))->Number = $i;
		        $mdxi->Order->ShoppingCart->Item(($i))->ProductNr = utf8_encode($this->products[$i-1]['id_product']);
		        $mdxi->Order->ShoppingCart->Item(($i))->Description = $this->products[$i-1]["name"];
		        if(isset($this->products[$i-1]["attributes"]))
		          $mdxi->Order->ShoppingCart->Item(($i))->Package = ($this->products[$i-1]["attributes"]);
		        else
		          $mdxi->Order->ShoppingCart->Item(($i))->Package = ($this->products[$i-1]["name"]);
		        $mdxi->Order->ShoppingCart->Item(($i))->Quantity = $this->products[$i-1]["quantity"];
		        $mdxi->Order->ShoppingCart->Item(($i))->ItemPrice = number_format($this->products[$i-1]["price_wt"],2,'.','');
		        
		        $taxId = $this->products[$i-1]["id_tax"];
		        if($taxId > 0)
		            $mdxi->Order->ShoppingCart->Item(($i))->ItemPrice->setTax(number_format(number_format($this->products[$i-1]["price_wt"],2,'.','')-number_format($this->products[$i-1]["price"],2,'.',''), 2, '.', ''));
		        $mdxi->Order->ShoppingCart->Item(($i))->Price = number_format($this->products[$i-1]["total_wt"],2,'.','');
		
		        if((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])) !== '' || (($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])) !== ''){
		            if(($i % 2)) {
		                $mdxi->Order->ShoppingCart->Item(($i))->Number->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->ProductNr->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Description->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Package->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Quantity->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->ItemPrice->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Price->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_EVEN'])));
		            } elseif(!($i % 2)) {
		                $mdxi->Order->ShoppingCart->Item(($i))->Number->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->ProductNr->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Description->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Package->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Quantity->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->ItemPrice->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])));
		                $mdxi->Order->ShoppingCart->Item(($i))->Price->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S_ODD'])));
		            }  
		        } else {
		            $mdxi->Order->ShoppingCart->Item(($i))->Number->setStyle((($this->designSettings['MPAY24_ITEM_NUMBER_S'])));
		            $mdxi->Order->ShoppingCart->Item(($i))->ProductNr->setStyle((($this->designSettings['MPAY24_ITEM_PRODUCT_NUMBER_S'])));
		            $mdxi->Order->ShoppingCart->Item(($i))->Description->setStyle((($this->designSettings['MPAY24_ITEM_DESCRIPTION_S'])));
		            $mdxi->Order->ShoppingCart->Item(($i))->Package->setStyle((($this->designSettings['MPAY24_ITEM_PACKAGE_S'])));
		            $mdxi->Order->ShoppingCart->Item(($i))->Quantity->setStyle((($this->designSettings['MPAY24_ITEM_QUANTITY_S'])));
		            $mdxi->Order->ShoppingCart->Item(($i))->ItemPrice->setStyle((($this->designSettings['MPAY24_ITEM_ITEM_PRICE_S'])));
		            $mdxi->Order->ShoppingCart->Item(($i))->Price->setStyle((($this->designSettings['MPAY24_ITEM_PRICE_S'])));
		        }  
	        }
	        
	        $mdxi->Order->ShoppingCart->SubTotal(1, number_format($this->subTotal,2,'.',''));
	        $mdxi->Order->ShoppingCart->SubTotal(1)->setHeader(utf8_encode($this->designSettings['MPAY24_SUB_TOTAL_H']));
	        $mdxi->Order->ShoppingCart->SubTotal(1)->setHeaderStyle(($this->designSettings['MPAY24_SUB_TOTAL_HS']));
	        $mdxi->Order->ShoppingCart->SubTotal(1)->setStyle(($this->designSettings['MPAY24_SUB_TOTAL_S']));
	        
	        $mdxi->Order->ShoppingCart->ShippingCosts(1, number_format($this->shippingCosts,2,'.',''));
	        $mdxi->Order->ShoppingCart->ShippingCosts(1)->setHeader(utf8_encode($this->designSettings['MPAY24_SHIPPING_COSTS_H']));
	        $mdxi->Order->ShoppingCart->ShippingCosts(1)->setHeaderStyle(($this->designSettings['MPAY24_SHIPPING_COSTS_HS']));
	        $mdxi->Order->ShoppingCart->ShippingCosts(1)->setStyle(($this->designSettings['MPAY24_SHIPPING_COSTS_S']));
	        
            $mdxi->Order->ShoppingCart->Tax(1, number_format($this->tax,2,'.',''));
            $mdxi->Order->ShoppingCart->Tax(1)->setHeader(utf8_encode($this->designSettings['MPAY24_TAX_H']));
            $mdxi->Order->ShoppingCart->Tax(1)->setHeaderStyle(($this->designSettings['MPAY24_TAX_HS']));
            $mdxi->Order->ShoppingCart->Tax(1)->setStyle(($this->designSettings['MPAY24_TAX_S']));	
            
            for($i=1; $i<=count($this->discounts); $i++){
	            $mdxi->Order->ShoppingCart->Discount($i, '-'.number_format($this->discounts[$i-1]['value_real'],2,'.',''));
	            $mdxi->Order->ShoppingCart->Discount($i)->setHeader(utf8_encode($this->designSettings['MPAY24_DISCOUNT_H']));
	            $mdxi->Order->ShoppingCart->Discount($i)->setHeaderStyle(($this->designSettings['MPAY24_DISCOUNT_HS']));
	            $mdxi->Order->ShoppingCart->Discount($i)->setStyle(($this->designSettings['MPAY24_DISCOUNT_S'])); 
            }
            
            $mdxi->Order->Price = number_format($transaction->PRICE,2,'.','');
	        $mdxi->Order->Price->setHeader(utf8_encode($this->designSettings['MPAY24_PRICE_H']));
	        $mdxi->Order->Price->setHeaderStyle(($this->designSettings['MPAY24_PRICE_HS']));
	        $mdxi->Order->Price->setStyle(($this->designSettings['MPAY24_PRICE_S']));
	        
	        $mdxi->Order->Currency = $this->transaction->CURRENCY;
	        
	        $mdxi->Order->BillingAddr->setMode($this->shopSettings['MPAY24_BILLING_ADDRESS_MODE']);
	        $mdxi->Order->BillingAddr->Name = $this->billingAddress->firstname . " " . $this->billingAddress->lastname;
	        $mdxi->Order->BillingAddr->Street = (substr($this->billingAddress->address1, 0, 50));
	        $mdxi->Order->BillingAddr->Street2 = (substr($this->billingAddress->address2, 0, 50));
	        $mdxi->Order->BillingAddr->Zip = $this->billingAddress->postcode;
	        $mdxi->Order->BillingAddr->City = $this->billingAddress->city;
	        $billingCountry = new Country((int)$this->billingAddress->id_country);
	        $mdxi->Order->BillingAddr->Country->setCode($billingCountry->iso_code);
	        $mdxi->Order->BillingAddr->Email = $this->customer->email;
	        
	        $mdxi->Order->ShippingAddr->setMode("ReadOnly");
	        $mdxi->Order->ShippingAddr->Name = $this->shippingAddress->firstname . " " . $this->billingAddress->lastname;
	        $mdxi->Order->ShippingAddr->Street = substr($this->shippingAddress->address1, 0, 50);
	        $mdxi->Order->ShippingAddr->Street2 = substr($this->shippingAddress->address2, 0, 50);
	        $mdxi->Order->ShippingAddr->Zip = $this->shippingAddress->postcode;
	        $mdxi->Order->ShippingAddr->City = $this->shippingAddress->city;
	        $shippingCountry = new Country((int)$this->shippingAddress->id_country);
	        $mdxi->Order->ShippingAddr->Country->setCode($shippingCountry->iso_code);
	        $mdxi->Order->ShippingAddr->Email = $this->customer->email;
	           
	        if(Configuration::get('PS_SSL_ENABLED'))
	           $url = "https://";
	        else
	           $url = "http://";
            
            $mdxi->Order->URL->Success = $url . $_SERVER['SERVER_NAME']. __PS_BASE_URI__ . "history.php";
            $mdxi->Order->URL->Error = $url . $_SERVER['SERVER_NAME']. __PS_BASE_URI__ . "history.php";
            $mdxi->Order->URL->Confirmation = $url . $_SERVER['SERVER_NAME']. _MODULE_DIR_ . "mpay24/confirm.php?customerID=".$this->customer->id."&amp;cartID=".(int)$this->cart->id;
            $mdxi->Order->URL->Cancel = $url . $_SERVER['SERVER_NAME']. __PS_BASE_URI__ . "history.php";
            
            return $mdxi;
        }
        
        function setPaymentVariables($cart, $designSettings, $shopSettings, $orderID){
        	global $cookie;
        	
        	$this->cart = $cart;
        	$this->orderID = $orderID;
        	$this->endAmount = $cart->getOrderTotal();
        	$currency = new Currency((int)($cart->id_currency));
        	$this->currency = $currency->iso_code;
        	$this->lang = Language::getIsoById($cart->id_lang);
        	$this->designSettings = $designSettings;
        	$this->secureKey = $cart->secure_key;
        	$this->shopSettings = $shopSettings;
        	$this->products = $cart->getProducts();        	
        	$this->subTotal = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        	$this->shippingCosts = $cart->getOrderShippingCost();
        	$this->tax = $cart->getOrderTotal() - $cart->getOrderTotal(false);
        	$this->discounts = $cart->getDiscounts(); 
        	$this->customer = new Customer((int)$cart->id_customer);
        	$id_shippingAddress = $cart->id_address_delivery;
        	$id_billingAddress = $cart->id_address_invoice;
        	$this->billingAddress = new Address((int)$id_billingAddress);
        	$this->shippingAddress = new Address((int)$id_shippingAddress);
        }
        
        function setCustomer($customer, $cartID){
        	$this->customer = $customer;
        	$this->cart = new Cart($cartID);
        }
    } 

?>
