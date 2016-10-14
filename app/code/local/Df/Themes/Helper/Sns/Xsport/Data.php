<?php
class Df_Themes_Helper_Sns_Xsport_Data extends Sns_Xsport_Helper_Data {
	/**
	 * @override
	 * @return Df_Themes_Helper_Sns_Xsport_Data
	 */
	public function __construct() {
		parent::__construct();
		if (isset($this->defaults)) {
			/** @var string[] $keys */
			$keys = array(
				'displayAddtocart'
				,'displayCompare'
				,'displayWishlist'
				,'general_useTagNew'
				,'general_useTagSale'
				,'listingpage_displayAddtocart'
				,'listingpage_displayCompare'
				,'listingpage_displayWishlist'
				,'listingpage_useTagNew'
				,'listingpage_useTagSale'
				,'useTagNew'
				,'useTagSale'
			);
			/**
			 * 2015-02-08
			 * Для ассоциативных массивов $b + $a по сути эквивалентно array_merge($a, $b)
			 */
			$this->defaults += array_fill_keys($keys, null);
		}
	}
}