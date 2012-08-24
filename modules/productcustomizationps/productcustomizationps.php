<?php
/**
* The Product Customization module adds a customization SWF to the product details page
* Addes a new hook to the product detail page for the swf
*
* @copyright  Personera
* @see        /overide/classes/CartController, /override/classes/Printspec.php, /override/classes/QueuePrintspec.php
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Product Customization PS
*/

if (!defined('_PS_VERSION_'))
  exit;

class ProductCustomizationPS extends Module
{

  public function __construct()
  {
    $this->name = 'productcustomizationps';
    $this->tab = 'front_office_features';
    $this->version = 1.0;
    $this->author = 'Shadley Wentzel';
    $this->need_instance = 0;

    parent::__construct();

    $this->displayName = $this->l('Product Customization');
    $this->description = $this->l('This module provides Product Customization.');
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    
    // if (!Configuration::get('PRODUCT_CUSTOMIZATION_PS_NAME'))
    // {
    //     $this->warning = $this->l('No name provided');
    // }
  }

  public function install()
  {
    if (parent::install() == false OR !$this->createTbl() OR !$this->registerHook('customizationBlock') OR !$this->registerHook('cart'))
      return false;
    return true;
  }

  /**
   * Function to install default tables
   *
   * @param void
   * @return void
   *
   */
  public function createTbl()
  {
    Db::getInstance()->execute('
      CREATE TABLE `'._DB_PREFIX_.'printspecs` (
        `id_printspec` int(11) NOT NULL AUTO_INCREMENT,
        `id_order` int(10) unsigned DEFAULT NULL,
        `id_cart` int(10) unsigned NOT NULL,
        `id_product` int(10) unsigned NOT NULL,
        `id_customer` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `quantity` int(10) DEFAULT 1,
        `print_json` mediumtext,
        `state` varchar(255) DEFAULT NULL,
        `clazz` varchar(255) DEFAULT NULL,
        `date_add` datetime DEFAULT NULL,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id_printspec`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    // Db::getInstance()->execute('
    //   CREATE TABLE `'._DB_PREFIX_.'queue_printspecs` (
    //     `id_queue_printspec` int(11) NOT NULL AUTO_INCREMENT,
    //     `id_order` int(10) unsigned NOT NULL,
    //     `id_customer` int(10) unsigned NOT NULL,
    //     `id_shop` int(10) unsigned NOT NULL,
    //     `id_printspec` int(10) unsigned NOT NULL,
    //     `state` varchar(255) DEFAULT NULL,
    //     `email` varchar(255) DEFAULT NULL,
    //     `date_add` datetime DEFAULT NULL,
    //     `date_upd` datetime DEFAULT NULL,
    //     PRIMARY KEY (`id_queue_printspec`)
    //   ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    Db::getInstance()->execute('
      CREATE TABLE `'._DB_PREFIX_.'state_events` (
        `id_state_event` int(11) NOT NULL AUTO_INCREMENT,
        `id_stateful` int(10) unsigned NOT NULL,
        `id_customer` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `stateful_type` varchar(255) DEFAULT NULL,
        `current_state` varchar(255) DEFAULT NULL,
        `date_add` datetime DEFAULT NULL,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id_state_event`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'hook` (`name`, `title`, `description`) VALUES ("customizationBlock", "Customization Block", "Customization for product in SWF file");');

    return true;
  }

  public function uninstall()
  {
    # Delete configuration      
    return (parent::uninstall() AND $this->removeTbl());
  }

  /**
   * Function to uninstall default tables
   *
   * @param void
   * @return void
   *
   */
  public function removeTbl()
  {
    Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'printspecs`;');
    Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'queue_printspecs`;');
    Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'state_events`;');
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'hook` WHERE `name` = "customizationBlock";');
  }

  /**
   * Function to add hook for Customization Block
   *
   * @param $params Array of parameters passed in this request
   * @return Template to show the social logins
   *
   */
  public function hookCustomizationBlock($params)
  {
    global $smarty, $protocol_content;
    $product = new Product((int)Tools::getValue('id_product'), false, Context::getContext()->language->id);

    # Get Product features
    $product_features = $product->getFrontFeaturesArrangeByFeature(Context::getContext()->language->id);    
   
    $smarty->assign(array(
      'fb_app_id' => (Configuration::get('FB_APPID', null, $this->context->shop->id_group_shop, $this->context->shop->id)),
      'product_price' => $product->price,
      'design_id' => $product_features[7]['value'],
      'product_type' => $product_features[9]['value']
    )); 
    
    return $this->display(__FILE__,'productcustomizationps.tpl');
  }


  /**
   * Function to add hook when a new order is created that updates
   * the related prinspecs
   *
   * @param $params Array of parameters passed in this request
   * @return boolean when printspec has been updated
   *
   */
  public function hookActionPaymentConfirmation($params)
  { 
    # Get Order Object
    $order = new Order((int)$params['id_order']);

    # Add new record into print spec queue for each order item
    foreach ($order->getPrintspecsByCart() as $printspec){
      $printspec = new Printspec($printspec['id_printspec']);
      $printspec->id_order = $order->id;
      $printspec->state = 'unstarted';
      $printspec->update();
    }

    return true;
  }

}