<?php
/**
* The Override calss for the product
*
* @copyright  Personera
* @see        ProductCore.php
* @author     Shadley Wentzel <shadley@personera.com>
* @package    Printspec
*/

class Product extends ProductCore
{

  /**
   * Function to find all features of a product and arrange them with 
   * the feature id as the index
   *
   * @param $id_lang integer The language id
   * @return All features found
   *
   */
	public function getFrontFeaturesArrangeByFeature($id_lang)
	{
		
		$results = array();
		foreach($this->getFrontFeatures($id_lang) as $feature ){
		// Tools::dieObject($feature);
			// kkk
			$results[$feature['id_feature']] = $feature;
		}

		return $results;
	}
}

