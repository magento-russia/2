<?php
abstract class Df_C1_Cml2_Import_Data_Entity_AttributeValue_OfferPart
	extends Df_C1_Cml2_Import_Data_Entity_AttributeValue {
	/**
	 * @override
	 * @return Df_C1_Cml2_Import_Data_Entity_Product
	 */
	protected function getProduct() {return $this->getOffer()->getEntityProduct();}

	/** @return Df_C1_Cml2_Import_Data_Entity_Offer */
	protected function getOffer() {return $this->cfg(self::P__OFFER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__OFFER, Df_C1_Cml2_Import_Data_Entity_Offer::class);
	}
	const P__OFFER = 'offer';
}


 