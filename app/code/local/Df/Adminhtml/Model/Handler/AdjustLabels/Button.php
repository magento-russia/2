<?php
/**
 * @method Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before getEvent()
 */
class Df_Adminhtml_Model_Handler_AdjustLabels_Button extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (df_enabled(Df_Core_Feature::LOCALIZATION)) {
			$this->getBlockAsButton()
				->setData(
					Df_Adminhtml_Const::BUTTON_PROPERTY_LABEL
					,df_text()->formatCase(
						df_nts(
							$this->getBlockAsButton()->getData(
								Df_Adminhtml_Const::BUTTON_PROPERTY_LABEL
							)
						)
						,df_cfg()->admin()->_interface()->getButtonLabelFont()->getLetterCase()
					)
				)
			;
		}
	}

	/** @return Mage_Adminhtml_Block_Widget_Button */
	private function getBlockAsButton() {
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