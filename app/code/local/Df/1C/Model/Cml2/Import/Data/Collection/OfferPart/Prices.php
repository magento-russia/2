<?php
class Df_1C_Model_Cml2_Import_Data_Collection_OfferPart_Prices
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_Price|null */
	public function getMain() {
		return $this->findByExternalId($this->getState()->getPriceTypes()->getMain()->getExternalId());
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_Price::_CLASS;}
	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('Цены', 'Цена');}
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer */
	private function getOffer() {return $this->cfg(self::P__OFFER);}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__OFFER, Df_1C_Model_Cml2_Import_Data_Entity_Offer::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__OFFER = 'offer';
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_OfferPart_Prices
	 */
	public static function i(
		Df_Varien_Simplexml_Element $element, Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	) {
		return new self(array(self::P__SIMPLE_XML => $element, self::P__OFFER => $offer));
	}
}