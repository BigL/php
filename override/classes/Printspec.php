<?php
/**
* The Print Specification Class to handle all the collection of print specs for products
*
* @copyright  Personera
* @see        
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Printspec
*/

class PrintspecCore extends ObjectModel
{
	public $id;

	public $id_order;

	public $id_cart;

	public $id_product;

	public $id_customer;

	public $id_shop;

	public $quantity;

	public $print_json;

	public $state;

	public $clazz;

	public $date_add;

	public $date_upd;
	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'printspecs',
		'primary' => 'id_printspec',
		'fields' => array(
			'id_order'      	=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_cart' 			=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_product' 		=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_customer' 		=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop' 		  	=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'guid' 				=> array('type' => self::TYPE_STRING, 'size' => 36),
			'quantity' 		  	=> array('type' => self::TYPE_INT),
			'print_json' 		=> array('type' => self::TYPE_STRING, 'size' => 254),
			'state' 			=> array('type' => self::TYPE_STRING, 'size' => 100),
			'clazz' 			=> array('type' => self::TYPE_STRING, 'size' => 100),
			'date_add' 			=> array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 			=> array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);


	/**
	 * Function to delete a customer authentication
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
			DELETE FROM `'._DB_PREFIX_.'printspec`
			WHERE `id` = '.(int)$this->id
		);
		return (parent::delete());
	}


	/**
	 * Function to retrieve a printspec using its unique guid
	 *
	 * @param string $guid The guid to serach for
	 * @return Printspec|boolean the retrieved printspec, if one is not found false is returned
	 *
	 */
	public static function getByGuid($guid)
	{
		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'printspecs`
				WHERE `guid` = \''.pSQL($guid).'\'';
		$result = Db::getInstance()->getRow($sql);

		if (!$result)
			return false;
		
		$retrieved_printspec = new Printspec();
		$retrieved_printspec->id = $result['id_printspec'];

		foreach ($result as $key => $value)
			if (key_exists($key, $retrieved_printspec))
				$retrieved_printspec->{$key} = $value;
		
		return $retrieved_printspec;
	}
	

	/**
	 * Function to retrieve a printspec using its cart, product and shop id
	 *
	 * @param integer $id_cart The guid to serach for
	 * @param integer $id_product The guid to serach for
	 * @param integer $id_shop The guid to serach for
	 * @return Printspec|boolean the retrieved printspec, if one is not found false is returned
	 *
	 */
	public static function getByCart($id_cart,$id_product,$id_shop)
	{	 
		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'printspecs`
				WHERE `id_cart` = '.(int)$id_cart
				.' AND id_product = '.(int)$id_product
				.' AND id_shop = '.(int)$id_shop;
		$result = Db::getInstance()->getRow($sql);

		if (!$result)
			return false;
		
		$retrieved_printspec = new Printspec();
		$retrieved_printspec->id = $result['id_printspec'];
		
		foreach ($result as $key => $value)
			if (key_exists($key, $retrieved_printspec))
				$retrieved_printspec->{$key} = $value;
		
		return $retrieved_printspec;
	}


    /**
     * Function that Generates a Universally Unique IDentifier, version 4.
     *
     * This function generates a truly random GUID.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.4
     * @see http://en.wikipedia.org/wiki/UUID
     * @see http://en.wikipedia.org/wiki/GUID
     * @return string A GUID, made up of 32 hex digits and 4 hyphens.
     */
    public static function generate_uuid()
    {
        $pr_bits = false;

        $fp = @fopen('/dev/urandom', 'rb');

        if ($fp !== false) {
            $pr_bits .= @fread($fp, 16);
            @fclose($fp);
        } else {
            // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
            $pr_bits = "";

            for($cnt = 0; $cnt < 16; $cnt ++) {
                $pr_bits .= chr(mt_rand(0, 255));
            }
        }

        $time_low = bin2hex(substr($pr_bits, 0, 4));
        $time_mid = bin2hex(substr($pr_bits, 4, 2));
        $time_hi_and_version = bin2hex(substr($pr_bits, 6, 2));
        $clock_seq_hi_and_reserved = bin2hex(substr($pr_bits, 8, 2));
        $node = bin2hex(substr($pr_bits, 10, 6));

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec($time_hi_and_version);
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        $guid = sprintf('%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);

        return strtoupper($guid);
    }

}