<?php
/**
* The Product Customization module adds a customization SWF to the product details page
* Addes a new hook to the product detail page for the swf. This also uses the Calender API
* from Personera V1 to render a preview of a calender project
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

  }


  /**
   * Function to install module
   *
   * @param void
   * @return boolean If the function completed successfully
   *
   */
  public function install()
  {
    if (parent::install() == false OR !$this->createTbl() OR !$this->registerHook('customizationBlock') OR !$this->registerHook('cart'))
      return false;
    return true;
  }


  /**
   * Function to install default tables printspecs used to hold
   * print information that will be used by the stitching server and
   * state_events which control the states of objects also needed for stitching
   *
   * @param void
   * @return boolean If the function completed successfully
   *
   */
  public function createTbl()
  {
    Db::getInstance()->execute('
      CREATE TABLE IF NOT EXISTS  `'._DB_PREFIX_.'printspecs` (
        `id_printspec` int(11) NOT NULL AUTO_INCREMENT,
        `id_order` int(10) unsigned DEFAULT NULL,
        `id_cart` int(10) unsigned NOT NULL,
        `id_product` int(10) unsigned NOT NULL,
        `id_customer` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `quantity` int(10) DEFAULT 1,
        `guid` char(36) DEFAULT NULL,
        `print_json` mediumtext,
        `state` varchar(255) DEFAULT NULL,
        `clazz` varchar(255) DEFAULT NULL,
        `date_add` datetime DEFAULT NULL,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id_printspec`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    Db::getInstance()->execute('
      CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'state_events` (
        `id_state_event` int(11) NOT NULL AUTO_INCREMENT,
        `id_stateful` int(10) unsigned NOT NULL,
        `id_customer` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `stateful_type` varchar(255) DEFAULT NULL,
        `previous_state` varchar(255) DEFAULT NULL,
        `next_state` varchar(255) DEFAULT NULL,
        `date_add` datetime DEFAULT NULL,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id_state_event`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'hook` (`name`, `title`, `description`) VALUES ("customizationBlock", "Customization Block", "Customization for product in SWF file");');

    return true;
  }


  /**
   * Function to un-install module
   *
   * @param void
   * @return boolean If the function completed successfully
   *
   */
  public function uninstall()
  {
    # Delete configuration
    return (parent::uninstall() AND $this->removeTbl());
  }


  /**
   * Function to uninstall default tables
   *
   * @param void
   * @return boolean If the function completed successfully
   *
   */
  public function removeTbl()
  {
    Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'printspecs`;');
    Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'queue_printspecs`;');
    Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'state_events`;');
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'hook` WHERE `name` = "customizationBlock";');

    return true;
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
    global $smarty, $protocol_content,$fbClient;

    Tools::addCSS($this->_path.'css/productcustomizationps.css', 'all');

    # retrieve the correct product
    $product = new Product((int)Tools::getValue('id_product'), false, Context::getContext()->language->id);

    # Get Product features
    $product_features = $product->getFrontFeaturesArrangeByFeature(Context::getContext()->language->id);
    // Tools::dieObject( $product_features ); 
    # handle case for calender product template

    if(count($product_features) > 0 && $product_features[8]['value'] == 'Calender'){
      # this is the case of a calender product which
      # needs to use the external Calender API

      # get user data
      $customer = new Customer();
      if($this->context->customer->email)
        $retrieved_customer = $customer->getByEmail($this->context->customer->email);

      # get customer authentiction which holds the FB authentication data
      if($retrieved_customer->id)
        $customer_authentication = CustomerAuthentication::getByCustomerId($retrieved_customer->id, $retrieved_customer->id_shop);
        // Tools::dieObject(Configuration::get('PS_SHOP_DOMAIN', null, $this->context->shop->id_group_shop, $this->context->shop->id));
      # retrieve a new project guid using the api
      $project_data = self::getProject(
              Configuration::get('PS_STORE_DOMAIN', null, $this->context->shop->id_group_shop, $this->context->shop->id).'/api/calender',
              $product_features[14]['value'],
              $product_features[11]['value'],
              $product_features[12]['value'],
              $product_features[13]['value'],
              date('Y'),
              date('m'),
              1,
              Configuration::get('FB_APPID', null, $this->context->shop->id_group_shop, $this->context->shop->id),
              Configuration::get('FB_SECRET', null, $this->context->shop->id_group_shop, $this->context->shop->id),
              Configuration::get('PS_SHOP_DOMAIN', null, $this->context->shop->id_group_shop, $this->context->shop->id),
              $customer_authentication->uid,
              $customer_authentication->access_token
           );

      $calender_array = array(
          "iframe_guid" => $product_features[14]['value'],
          "product_guid" => $product_features[11]['value'],
          "theme_guid" => $product_features[12]['value'],
          "product_layout_guid" => $product_features[13]['value'],
          "project_guid" => $project_data->project_guid,
          "order_guid" => $project_data->order_guid,
          "month" => (int)date('n'),
          "year" => (int)date('Y'),
          "day" => 1
        );

      # add variables for calender product to smarty
      $this->context->smarty->assign(array(
        'fb_app_id' => (Configuration::get('FB_APPID', null, $this->context->shop->id_group_shop, $this->context->shop->id)),
        'product_price' => $product->price,
        'product_type' => $product_features[8]['value'],
        'project_guid' => $project_data->project_guid,
        'preview_url' => Configuration::get('PS_STORE_DOMAIN', null, $this->context->shop->id_group_shop, $this->context->shop->id).'/project/calendarpreview',
        'month' => (int)date('n'),
        'year' => (int)date('Y'),
        'guid' => $project_data->project_guid,
        'print_json' => json_encode($calender_array)
      ));

      # use calender template for calender product
      $template_name = 'product_calender.tpl';
    }else{
      /**
       * this is the case of photobook, poster or postcard any product
       * that use the swf to do customization and previewing
       */

      $this->context->smarty->assign(array(
        'fb_app_id' => (Configuration::get('FB_APPID', null, $this->context->shop->id_shop_group, $this->context->shop->id)),
        'product_price' => $product->price,
        'design_id' => (count($product_features) > 0)? $product_features[7]['value']: "",
        'guid' => Printspec::generate_uuid(),
        'product_type' => (count($product_features) > 0)?$product_features[8]['value']:""
      ));

      # use default template for product
      $template_name = 'productcustomizationps.tpl';
    }

    if($product_features)
    {
      # we are inside product customization so popup a facebook connect prompt
      $this->context->smarty->assign(array("show_fb_connect" => 1));
    }else{
      # we are not inside product customization so don't popup a facebook connect prompt
      $this->context->smarty->assign(array("show_fb_connect" => 0));
    }

    // Tools::dieObject($this->context->customer);
    return $this->display(__FILE__, $template_name);
  }


  /**
   * Function to add hook when a new order is created that updates
   * the related prinspecs. This sets the print spec state to unstarted
   * this will cause the stitching daemon to start the new print specj job
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

      # if this is a calender print spec do the PUT call to the Calender API
      if($printspec->clazz == 'Calender'){

        # decode printspec json to find the project guid for this project
        $parsed_json = json_decode($printspec->print_json);

        # do put call to calender API to update Calender Order
        $update_calender = self::updateProject(
              Configuration::get('PS_STORE_DOMAIN', null, $this->context->shop->id_group_shop, $this->context->shop->id).'/api/calender',
              $parsed_json->project_guid);
      }
    }

    return true;
  }


  /**
   * Function to send a curl request to the calender api and retrive
   * a new project currently will receive the project and order guid
   *
   * @param string $url The API url to retrieve a new calender project
   * @param string $iframe_name The name of the iframe for the calender project
   * @param string $product_guid The associated product guid for the calender
   * @param string $theme_guid The associated theme for the calender
   * @param string $product_layout_guid The associated product layout for the calender
   * @param integer $start_year The starting year of the calender
   * @param intger $start_month The starting month of the calender
   * @param integer $start_day The starting day of the calender
   * @param string $fb_app_id The correct Facebook App ID
   * @param string $fb_uid The correct Facebook User Id
   * @param string fb_access_token The Facebook access token generated
   * @return string Project guid of newly created calender project
   *
   */
  private function getProject($url,$iframe_name,$product_guid,$theme_guid,$product_layout_guid,$start_year,$start_month,$start_day,$fb_app_id,$fb_app_secret,$fb_app_url,$fb_uid,$fb_access_token)
  {
    // Tools::dieObject(func_get_args());
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);

    # build data aray that will be sent to api
    $data = array(
        'iframe_name' => urlencode($iframe_name),
        'product_guid' => urlencode($product_guid),
        'theme_guid' => urlencode($theme_guid),
        'product_layout_guid' => urlencode($product_layout_guid),
        'start_year' => urlencode($start_year),
        'start_month' => urlencode($start_month),
        'start_day' => urlencode($start_day),
        'fb_app_id' => urlencode($fb_app_id),
        'fb_app_secret' => urlencode($fb_app_secret),
        'fb_app_url' => urlencode($fb_app_url),
        'fb_uid' => urlencode($fb_uid),
        'fb_access_token' => urlencode($fb_access_token),
        'format' => json
    );

    # execute curl request
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $decoded_response = json_decode($response);
    $info = curl_getinfo($ch);

    if (curl_errno($ch)) {
      # moving to display page to display curl errors
        $this->curlErrorNum = curl_errno($ch) ;
        $this->curlErrorMsg = curl_error($ch);
    } else {
      # closing
      curl_close($ch);
    }

    return $decoded_response->message;
  }


  /**
   * Function to send a curl request to the calender api and update
   * any calenders associated to this order via the PrintSpec table
   *
   * @param string $url The API url to retrieve a new calender project
   * @param string $project_data The project data array that holds the update information for the calender project
   * @return string Project guid of newly created calender project
   *
   */
  private function updateProject($url,$project_data)
  {

    $ch = curl_init($url . $project_data['project_guid']);

    # build data aray that will be sent to api
    $data = array(
        'id' => urlencode($project_data['project_guid']),
        'product_guid' => urlencode($project_data['payment_status']),
        'format' => json
    );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));

    # execute curl request
    $response = curl_exec($ch);
    $decoded_response = json_decode($response);
    $info = curl_getinfo($ch);

    if (curl_errno($ch)) {
      # moving to display page to display curl errors
        $this->curlErrorNum = curl_errno($ch) ;
        $this->curlErrorMsg = curl_error($ch);
    } else {
      # closing
      curl_close($ch);
    }

    return $decoded_response->message;
  }


}
