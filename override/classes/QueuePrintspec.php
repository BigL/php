<?php
/**
* The Quete Print Specs Class to handle the print spec queue manangement
*
* @copyright  Personera
* @see        Printspec.php, QueuePrintspec.php
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Printspec
*/

class QueuePrintspecCore extends ObjectModel
{
	public $id;

	public $id_order;

	public $id_customer;

	public $id_printspec;

	public $id_shop;

	public $state;

	public $email;

	public $date_add;

	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'queue_printspecs',
		'primary' => 'id_queue_printspec',
		'fields' => array(
			'id_order' 	=>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_customer' 		=> 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop' 			=> 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_printspec' 		=> 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'state' 			=>  array('type' => self::TYPE_STRING, 'size' => 100),
			'email' 			=> 	array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 254),
			'date_add' 			=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 			=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	 /**
	 * Function to delete a queue print spec object
	 *
	 * @param void
	 * @return id
	 *
	 */
	public function delete()
	{
		if (!Validate::isUnsignedId($this->id))
			return false;
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'queue_printspecs`
			WHERE `id` = '.(int)$this->id
		);
		return (parent::delete());
	}

}