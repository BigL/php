<?php
if (!defined('_PS_VERSION_'))
      exit;

class MyModule extends Module
{
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'Test';
        $this->version = 1.0;
        $this->author = 'Firstname Lastname';
        $this->need_instance = 0;

         parent::__construct();

        $this->displayName = $this->l('My module');
        $this->description = $this->l('Description of my module.');
    }

    public function install()
    {
          if (parent::install() == false OR !$this->registerHook('left'))
            return false;
          return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'mymodule`');

        parent::uninstall();
    }
    public function hookLeftColumn( $params )
    {
        global $smarty;

        $customer = new Customer();
        /*$retrieved_customer = $customer->getByEmail($this->context->customer->email);
        
        if($retrieved_customer)
        {
            $marty->assign(array(
            "user_name"=>$retrieved_customer->firstname
            ));    
        }else{
            $marty->assign(array(
            "user_name"=>"anonymous"
            ));    
        }
        echo "<pre>";print_r($this->context->customer);die();
        */
        return $this->display(__FILE__,'mymodule.tpl');
    }

    public function hookRightColumn( $params )
    {
        return $this->hookLeftColumn($params);
    }
}


?>
