<?php
class Df_1C_Cml2_Import_Data_Collection_OfferPart_Prices extends Df_1C_Cml2_Import_Data_Collection {
	/** @return Df_1C_Cml2_Import_Data_Entity_OfferPart_Price|null */
	public function getMain() {
		return $this->findByExternalId($this->getState()->getPriceTypes()->getMain()->getExternalId());
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_OfferPart_Price::_C;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'Цены/Цена';}

	/** @return Df_1C_Cml2_Import_Data_Entity_Offer */
	private function getOffer() {return $this->cfg(self::$P__OFFER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__OFFER, Df_1C_Cml2_Import_Data_Entity_Offer::_C);
	}
	/** @var string */
	private static $P__OFFER = 'offer';
	/**
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Offer::getPrices()
	 * @static
	 * @param \Df\Xml\X $e
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Cml2_Import_Data_Collection_OfferPart_Prices
	 */
	public static function i(\Df\Xml\X $e, Df_1C_Cml2_Import_Data_Entity_Offer $offer) {
		return new self(array(self::$P__E => $e, self::$P__OFFER => $offer));
	}
}