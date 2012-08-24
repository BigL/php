<?php
/**
* The Paypal Direct Payments module
*
* @copyright  Personera
* @see        
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Paypal Direct PS
*/

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/paypaldirectps.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');
	
$paypaldirect = new PaypalDirectPS();
echo $paypaldirect->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>
