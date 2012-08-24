<?php
/**
* The Authentication Method Class to hanlde customer authentication
*
* @copyright  Personera
* @see        authsocialps.php
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Authsocial PS
*/

class AuthenticationMethod extends ObjectModel
{
	public $id;

	public $environment;

	public $provider;

	public $api_key;

	public $api_secret;

	public $is_active;

	public $date_add;

	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'authentication_method',
		'primary' => 'id_authentication_method',
		'fields' => array(
			'environment' 	=>  array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true),
			'provider' 		=> 	array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true),
			'api_key' 		=> 	array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 254),
			'api_secret' 	=>  array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 254),
			'is_active' 	=> 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'size' => 1),
			'date_add' 		=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 		=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	/**
	 * @see ObjectModel::$getFields
	 */
	public function getFields() 
	{
		parent::validateFields();
	    $fields['environment'] = pSQL($this->environment);
	    $fields['provider'] = pSQL($this->environment);
	    $fields['api_key'] = pSQL($this->environment);
	    $fields['api_secret'] = pSQL($this->environment);
	    return $fields;
	}

	 /**
	 * Function to delete a customer authentication record from the DB
	 *
	 * @param void
	 * @return integer ID of deleted record
	 *
	 */
	public function delete()
	{
		if (!Validate::isUnsignedId($this->id))
			return false;
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'customer_authentication`
			WHERE `id` = '.(int)$this->id
		);
		return (parent::delete());
	}

	/**
	 * Return all active authentication method by provider
	 *
	 * @param string $provider Name of provider
	 * @param string $passwd Password is also checked if specified
	 * @return array of AuthenticationMethods
	 */
	public function getByProvider($provider)
	{
		if (!Validate::isEmail($email) || ($passwd && !Validate::isPasswd($passwd)))
			die (Tools::displayError());

		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'authentication_method`
				WHERE `is_active` = 1
					AND `provider` = \''.pSQL($provider).'\'';
		$result = Db::getInstance()->getRow($sql);

		if (!$result)
			return false;
		$this->id = $result['id_authentication_method'];
		foreach ($result as $key => $value)
			if (key_exists($key, $this))
				$this->{$key} = $value;

		return $this;
	}

}