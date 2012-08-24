<?php
/**
 * @author              support@mpay24.com
 * @filesource          confirm.php
 * @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/mpay24.php');
include_once("api/prestaShop.php");

    Db::getInstance()->Execute("
            UPDATE `"._DB_PREFIX_."mpay24_order` SET 
                `MPAYTID` = ".$_REQUEST['MPAYTID'].",
                `STATUS` = '".$_REQUEST['STATUS']."' 
                WHERE `TID` = '".$_REQUEST['TID']."'
        ");
    
        $args['MPAYTID'] = $_REQUEST['MPAYTID'];
        $args['STATUS'] = $_REQUEST['STATUS'];    
        $args['token'] = '';
    
        $A=substr($_SERVER['DOCUMENT_ROOT'], strrpos($_SERVER['DOCUMENT_ROOT'], $_SERVER['PHP_SELF']));
        $basepath = substr(__FILE__, strlen($A)+1, strlen(__FILE__) - strlen("/modules/mpay24/confirm.php") - (strlen($A)+1));
            
				if(Configuration::get('MPAY24_TEST_MODE') == "true")
            $mode = true;
        else
            $mode = false;
           
        $prestaShop = new prestaShop(Configuration::get('MPAY24_MERCHANT_ID'), Configuration::get('MPAY24_SOAP_PASS'), $mode, Configuration::get('MPAY24_PROXY_PORT'));
        $prestaShop->setCustomer(new Customer((int)$_REQUEST['customerID']), $_REQUEST['cartID']);
        $result = $prestaShop->confirm($_REQUEST['TID'], $args);
?>