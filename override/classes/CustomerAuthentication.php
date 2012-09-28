<?php
/**
* The Customer Authentication Class to handles all authentication methods from customer
*
* @copyright  Personera
* @see        Customer.php
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Authsocial PS
*/

class CustomerAuthentication extends ObjectModel
{
	public $id;

	public $id_customer;

	public $id_authentication_method;

	public $id_shop;

	public $uid;

	public $access_token;

	public $date_add;

	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'customer_authentications',
		'primary' => 'id_customer_authentication',
		'fields' => array(
			'id_customer' 				=> 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true, 'size' => 20),
			'id_authentication_method'  => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true, 'size' => 20),
			'id_shop' 					=>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false, 'size' => 20),
			'id_group_shop' 			=>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false, 'size' => 20),
			'uid' 						=>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true, 'size' => 20),
			'access_token' 				=>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 200),
			'date_add' 					=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 					=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_exp' 					=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	/**
	 * Add current object to database, adding some extra variables for shop and shop group
	 *
	 * @param bool $null_values
	 * @param bool $autodate
	 * @return boolean Insertion result
	 */
	public function add($autodate = true, $null_values = true)
	{
		$this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;
		$this->id_group_shop = ($this->id_group_shop) ? $this->id_group_shop : Context::getContext()->shop->id_group_shop;
		
	 	$success = parent::add($autodate, $null_values);
		return $success;
	}

	 /**
	 * Function to delete a customer authentication
	 *
	 * @param void
	 * @return id
	 */
	public function delete()
	{
		if (!Validate::isUnsignedId($this->id))
			return false;
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'customer_authentications`
			WHERE `id` = '.(int)$this->id
		);
		return (parent::delete());
	}

	/**
	 * Return all customer authentication method by customer
	 *
	 * @param integer $id_customer Id of customer
	 * @param integer $id_shop Id of shop
	 * @param string $passwd Password is also checked if specified
	 * @return CustomerAuthentication representaion
	 */
	public static function getByCustomerId($id_customer, $id_shop)
	{
		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'customer_authentications`
				WHERE `id_customer` = \''.pSQL($id_customer).'\' AND `id_shop` = \''.pSQL($id_shop).'\'';
		$result = Db::getInstance()->getRow($sql);

		if (!$result)
			return false;
		
		$retrieved_customer_authentication = new CustomerAuthentication();
		$retrieved_customer_authentication->id = $result['id_customer_authentication'];
		foreach ($result as $key => $value)
			if (key_exists($key, $retrieved_customer_authentication))
				$retrieved_customer_authentication->{$key} = $value;
		
		return $retrieved_customer_authentication;
	}

}