<?php
/**
 * @method Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before getEvent()
 */
class Df_Adminhtml_Model_Handler_AdjustLabels_Form extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (df_enabled(Df_Core_Feature::LOCALIZATION)) {
			/**
			 * Оказывается, на странице System - Permissions - Roles
			 * $this->getBlockAsForm()->getForm() возвращает null
			 */
			if ($this->getBlockAsForm()->getForm() instanceof Varien_Data_Form_Abstract) {
				$this->processElement($this->getBlockAsForm()->getForm());
			}
		}
	}

	/**
	 * @param Varien_Data_Form_Abstract $element
	 * @return Object
	 */
	private function processElement(Varien_Data_Form_Abstract $element) {
		$element
			->setData(
				Df_Varien_Const::DATA_FORM_ELEMENT__PARAM__LABEL
				,df_text()->formatCase(
					df_nts($element->getData(Df_Varien_Const::DATA_FORM_ELEMENT__PARAM__LABEL))
					,df_cfg()->admin()->_interface()->getFormLabelFont()->getLetterCase()
				)
			)
		;
		foreach ($element->getElements() as $child) {
			/** @var Varien_Data_Form_Abstract $child */
			$this->processElement($child);
		}
		return $this;
	}

	/** @return Mage_Adminhtml_Block_Widget_Form */
	private function getBlockAsForm() {
		return $this->getEvent()->getBlock();
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before::_CLASS;
	}

	const _CLASS = __CLASS__;
}