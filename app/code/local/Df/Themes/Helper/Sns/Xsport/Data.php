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
			$this->defaults =
				array_merge(
					array_combine($keys, array_fill(0, count($keys), null))
					, $this->defaults
				)
			;
		}
	}
}