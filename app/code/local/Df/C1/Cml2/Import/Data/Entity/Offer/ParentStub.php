<?php
class Df_C1_Cml2_Import_Data_Entity_Offer_ParentStub extends Df_C1_Cml2_Import_Data_Entity_Offer {
	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {return $this->getPrototype()->getExternalIdForConfigurableParent();}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return $this->getEntityProduct()->getName();}

	/** @return Df_C1_Cml2_Import_Data_Entity_Offer */
	private function getPrototype() {return $this->cfg(self::$P__PROTOTYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PROTOTYPE, Df_C1_Cml2_Import_Data_Entity_Offer::class);
	}
	/** @var string */
	private static $P__PROTOTYPE = 'prototype';
	/**
	 * @static
	 * @param Df_C1_Cml2_Import_Data_Entity_Offer $prototype
	 * @return Df_C1_Cml2_Import_Data_Entity_Offer_ParentStub
	 */
	public static function i(Df_C1_Cml2_Import_Data_Entity_Offer $prototype) {
		return new self(array(self::$P__PROTOTYPE => $prototype, self::$P__E => $prototype->e()));
	}
}