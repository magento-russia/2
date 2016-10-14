<?php
class Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue_Empty
	extends Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	public function getAttributeMagento() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->_getAttributeMagento();
			/**
			 * Нельзя объединять это выражение с предыдущим,
			 * чтобы не попасть в рекурсию.
			 */
			$this->setupAttribute($this->{__METHOD__});
			$this->setupOption($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return $this->_getAttributeMagento()->getFrontendLabel();}

	/**
	 * @override
	 * @return string
	 */
	public function getValue() {return self::$VALUE__UNKNOWN;}

	/**
	 * Этот метод необходим, иначе @used-by getName() приведёт к сбою.
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	private function _getAttributeMagento() {return $this->cfg(self::$P__ATTRIBUTE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ATTRIBUTE, Df_Catalog_Model_Resource_Eav_Attribute::_C);
	}
	/** @var string */
	private static $P__ATTRIBUTE = 'attribute';
	/**
	 * @static
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer $offer
	 * @param Df_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue_Empty
	 */
	public static function i2(
		Df_1C_Cml2_Import_Data_Entity_Offer $offer
		,Df_Catalog_Model_Resource_Eav_Attribute $attribute
	) {
		return new self(array(self::P__OFFER => $offer, self::$P__ATTRIBUTE => $attribute));
	}
}