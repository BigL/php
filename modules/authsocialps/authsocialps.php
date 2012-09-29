<?php
/**
* The Auth Social Module class provides social authentication and customer account
* creation using social networks. Currently works only for Facebook.
* Dev Api Key for FB
* 306979302708349
* 23dab46b16d2af8b8d8f9939f696576e
*
* @copyright  Personera
* @see        overide/classes/CustomerAuthentications.php, overide/classes/AuthenticationMethod.php,
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Authsocial PS
*/

if (!defined('_PS_VERSION_'))
  exit;

class AuthSocialPS extends Module
{

  public function __construct()
  {
    $this->name = 'authsocialps';
    $this->tab = 'front_office_features';
    $this->version = 1.0;
    $this->author = 'Shadley Wentzel';
    $this->need_instance = 0;

    parent::__construct();

    $this->displayName = $this->l('Social Authentication');
    $this->description = $this->l('This module provides Social Authentication.');
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
    if (parent::install() == false OR !$this->registerHook('header') OR !$this->registerHook('customizationBlock') OR !$this->createTbl() )
      return false;
    return true;
  }


  /**
   * Function to install default tables
   *
   * @param void
   * @return boolean If the function completed successfully
   *
   */
  public function createTbl()
  {
    Db::getInstance()->execute('
      CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'authentication_methods` (
        `id_authentication_method` int(11) NOT NULL AUTO_INCREMENT,
        `environment` varchar(255) DEFAULT NULL,
        `provider` varchar(255) DEFAULT NULL,
        `api_key` varchar(255) DEFAULT NULL,
        `api_secret` varchar(255) DEFAULT NULL,
        `is_active` tinyint(1) DEFAULT NULL,
        `date_add` datetime DEFAULT NULL,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id_authentication_method`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'authentication_methods` (`id_authentication_method`, `environment`, `provider`, `api_key`, `api_secret`, `is_active`, `date_add`, `date_upd`) VALUES (1, "development", "facebook", "306979302708349", "23dab46b16d2af8b8d8f9939f696576e", 1,'.date("Y-md-d").','.date("Y-m-d").');');

    Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customer_authentications` (
      `id_customer_authentication` int(11) NOT NULL AUTO_INCREMENT,
      `id_customer` int(32) unsigned NOT NULL,
      `id_authentication_method` int(10) unsigned NOT NULL,
      `id_shop` int(32) unsigned NOT NULL,
      `id_group_shop` int(32) unsigned NOT NULL,
      `uid` int(32) DEFAULT NULL,
      `date_add` datetime DEFAULT NULL,
      `date_upd` datetime DEFAULT NULL,
      `date_exp` datetime DEFAULT NULL,
      PRIMARY KEY (`id_customer_authentication`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`name`) VALUES ("FB_APPID");');
    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`name`) VALUES ("FB_SECRET");');

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
    if (!parent::uninstall() || 
        !$this->removeTbl() );
       return false;

    return true;
    // return (parent::uninstall() AND $this->unregisterHook(Hook::get('rightColumn')) 
    // AND $this->unregisterHook(Hook::get('customizationBlock'))  
    // AND $this->removeTbl() );
  }

 

  /**
   * Function to remove tables
   *
   * @param void
   * @return boolean If the function completed successfully
   *
   */
  public function removeTbl()
  {
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "FB_APPID";');
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "FB_SECRET";');
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'authentication_methods` WHERE `id_authentication_method` =1;');
    return true;
  }


  public function getContent()
  {
    $output = '<h2>'.$this->displayName.'</h2>';
    if (Tools::isSubmit('submitFBKey'))
    {
      $appid = (Tools::getValue('appid'));
      if (!$appid)
        $errors[] = $this->l('Invalid Facebook AppID');
      else
        Configuration::updateValue('FB_APPID', $appid);

      $fbsecret = (Tools::getValue('fbsecret'));
      if (!$fbsecret)
        $errors[] = $this->l('Invalid Facebook App Secret');
      else
        Configuration::updateValue('FB_SECRET', $fbsecret);

      if (isset($errors) AND sizeof($errors))
        $output .= $this->displayError(implode('<br />', $errors));
      else
        $output .= $this->displayConfirmation($this->l('Settings updated'));
    }
    return $output.$this->displayForm();
  }


  /**
   * Function to render the admin configuration form
   *
   * @param void
   * @return string The configuration form
   *
   */
  public function displayForm()
  {
    $output = '
    <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
      <fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
        <p>'.$this->l('Your Facebook AppID').'</p><br />
        <label>'.$this->l('Facebook AppID').'</label>
        <div class="margin-form">
          <input type="text" size="20" name="appid" value="'.Tools::getValue('appid', Configuration::get('FB_APPID')).'" />

        </div>

        <p>'.$this->l('Your Facebook App Secret').'</p><br />
        <label>'.$this->l('Facebook App Secret').'</label>
        <div class="margin-form">
          <input type="text" size="40" name="fbsecret" value="'.Tools::getValue('fbsecret', Configuration::get('FB_SECRET')).'" />

        </div>
        <center><input type="submit" name="submitFBKey" value="'.$this->l('Save').'" class="button" /></center>
      </fieldset>
    </form>';
    return $output;
  }


  /**
   * Function to hook into Header hook
   *
   * @param void
   * @return void
   *
   */
  public function hookHeader()
  {
      $this->context->controller->addCSS($this->_path.'authsocialps.css', 'all');
  }

  /**
   * Function to hook into customizationBlock hook
   * Notes: I changed this to use the cusomizationblock from hooktop so that access template vars of customization too
   * @param $params Array of parameters passed in this request
   * @return Template to show the social logins
   *super-18x14-toda-ocasión-p12-pasta-suave
   */
  public function hookCustomizationBlock($params)
  {
    global $smarty, $cookie;
    
    

    $this->context->controller->addCSS( $this->_path.'css/authsocialps_top.css');
    $this->context->controller->addJS( _PS_JS_DIR_ . 'jquery/jquery-ui.will.be.removed.in.1.6.js' );
    
    $this->context->smarty->assign(array(
      'appid' => (Configuration::get('FB_APPID', null, $this->context->shop->id_shop_group, $this->context->shop->id)),
      'fbsecret' => (Configuration::get('FB_SECRET', null, $this->context->shop->id_shop_group, $this->context->shop->id)),
      'fbButtonString' => $this->l('Authenticate Using Your Facebook'),
      
    ));
    
    return $this->display(__FILE__,'authsocialps_top.tpl');
  }

}
