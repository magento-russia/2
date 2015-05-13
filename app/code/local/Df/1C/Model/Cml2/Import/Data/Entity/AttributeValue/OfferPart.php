<?php
abstract class Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue_OfferPart
	extends Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue {
	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Product
	 */
	protected function getProduct() {return $this->getOffer()->getEntityProduct();}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer */
	protected function getOffer() {return $this->cfg(self::P__OFFER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__OFFER, Df_1C_Model_Cml2_Import_Data_Entity_Offer::_CLASS);
	}
	const P__OFFER = 'offer';
}


 