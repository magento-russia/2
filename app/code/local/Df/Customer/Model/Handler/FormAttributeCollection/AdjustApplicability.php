<?php
/** @method Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter getEvent() */
class Df_Customer_Model_Handler_FormAttributeCollection_AdjustApplicability extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if ($this->getAddress()) {
			foreach ($this->getAttributes() as $attribute) {
				/** @var Mage_Customer_Model_Attribute $attribute */
				$this->adjust($attribute);
			}
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter::_C;}

	/**
	 * @param Mage_Customer_Model_Attribute $attribute
	 * @return Df_Customer_Model_Handler_FormAttributeCollection_AdjustApplicability
	 */
	private function adjust(Mage_Customer_Model_Attribute $attribute) {
		Df_Customer_Model_Attribute_ApplicabilityAdjuster::i(array(
			Df_Customer_Model_Attribute_ApplicabilityAdjuster::P__ATTRIBUTE => $attribute
			,Df_Customer_Model_Attribute_ApplicabilityAdjuster::P__ADDRESS => $this->getAddress()
		))->adjust();
		return $this;
	}

	/** @return Mage_Customer_Model_Resource_Form_Attribute_Collection|Mage_Customer_Model_Entity_Form_Attribute_Collection */
	private function getAttributes() {return $this->getEvent()->getCollection();}

	/** @return Mage_Customer_Model_Address_Abstract|null */
	private function getAddress() {
		return $this->getAttributes()->getFlag(Df_Customer_Const_Form_Attribute_Collection::P__ADDRESS);
	}

	/** @used-by Df_Customer_Observer::form_attribute_collection__load_after() */
	const _C = __CLASS__;
}