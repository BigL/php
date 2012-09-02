<?php
/**
* The Auth Social Module class provides social authentication and customer account
* creation using social networks. Currently works only for Facebook.
* Dev Api Key for FB
* 306979302708349
* 23dab46b16d2af8b8d8f9939f696576e
*
* @copyright  Personera
* @see
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

  public function install()
  {
    if (parent::install() == false OR !$this->registerHook('header') OR !$this->registerHook('top') OR !$this->createTbl())
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
      CREATE TABLE `'._DB_PREFIX_.'authentication_methods` (
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
    CREATE TABLE `'._DB_PREFIX_.'customer_authentications` (
      `id_customer_authentication` int(11) NOT NULL AUTO_INCREMENT,
      `id_customer` int(32) unsigned NOT NULL,
      `id_authentication_method` int(10) unsigned NOT NULL,
      `id_shop` int(32) unsigned NOT NULL,
      `id_group_shop` int(32) unsigned NOT NULL,
      `uid` int(32) DEFAULT NULL,
      `date_add` datetime DEFAULT NULL,
      `date_upd` datetime DEFAULT NULL,
      PRIMARY KEY (`id_customer_authentication`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`name`) VALUES ("FB_APPID");');
    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`name`) VALUES ("FB_SECRET");');
    return true;
  }


  public function uninstall()
  {
    # Delete configuration
    return (parent::uninstall() AND $this->unregisterHook(Hook::get('rightColumn')) AND $this->unregisterHook(Hook::get('top'))  AND $this->removeTbl() );
  }

  /**
   * Function to remove tables
   *
   * @param void
   * @return void
   *
   */
  public function removeTbl()
  {
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "FB_APPID";');
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "FB_SECRET";');
    Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'authentication_methods`;');
    Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'customer_authentications`;');
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

  public function hookHeader()
  {
      $this->context->controller->addCSS($this->_path.'authsocialps.css', 'all');
  }


  /**
   * Function to hook into top of page hook
   *
   * @param $params Array of parameters passed in this request
   * @return Template to show the social logins
   *
   */
    public function hookTop($params)
    {
        global $smarty, $cookie;

        $this->context->controller->addCSS($this->_path.'css/authsocialps_top.css');


        require_once (_PS_MODULE_DIR_.'authsocialps/classes/CustomerAuthentication.php');
        $db_Customerauth = new CustomerAuthentication();

        $fb_user_data = $db_Customerauth->getByCustomerId($this->context->customer->id, $this->context->customer->id_shop);
        //echo "<pre>";print_r($fb_user_data);print_r($this->context->customer);
        //die();

        $smarty->assign(array(
        'appid' => (Configuration::get('FB_APPID', null, $this->context->shop->id_shop_group, $this->context->shop->id)),
        'fbsecret' => (Configuration::get('FB_SECRET', null, $this->context->shop->id_shop_group, $this->context->shop->id)),
        'fb_uid' => $fb_user_data->uid,
        'logged' => $this->context->customer->isLogged(),
        ));

        return $this->display(__FILE__,'authsocialps_top.tpl');
    }

}
