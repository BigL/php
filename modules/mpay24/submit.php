<?php
/**
 * @author              support@mpay24.com
 * @filesource          submit.php
 * @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/mpay24.php');

global $cookie;

if (!$cookie->isLogged(true))
    Tools::redirect('authentication.php?back=order.php');
elseif (!$cart->getOrderTotal(true, Cart::BOTH))
    Tools::displayError('Error: Empty cart');

$mpay24 = new mpay24();
// Prepare payment
$mpay24->executePayment($cookie);