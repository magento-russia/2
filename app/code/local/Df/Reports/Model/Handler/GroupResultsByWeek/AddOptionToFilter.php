<?php
/**
 * @method Df_Core_Model_Event_Adminhtml_Block_HtmlBefore getEvent()
 */
class Df_Reports_Model_Handler_GroupResultsByWeek_AddOptionToFilter extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var Varien_Data_Form $form */
		$form = $this->getBlockAsReportFilterForm()->getForm();
		/** @var Varien_Data_Form_Element_Fieldset $fieldset */
		$fieldset = $form->getElement('base_fieldset');
		df_assert($fieldset instanceof Varien_Data_Form_Element_Fieldset);
		/** @var Varien_Data_Form_Element_Select $fieldPeriodType */
		$fieldPeriodType = $fieldset->getElements()->searchById('period_type');
		df_assert($fieldPeriodType instanceof Varien_Data_Form_Element_Select);
		/** @var array $options */
		$options = $fieldPeriodType->getData('options');
		df_assert_array($options);
		$options['week'] = df_mage()->reportsHelper()->__('Week');
		$fieldPeriodType->setData('options', $options);
		/** @var array $values */
		$values = $fieldPeriodType->getData('values');
		df_assert_array($values);
			array_splice(
				$values
				,1
				,0
				,array(
					array(
						'value' => 'week'
						,'label' => df_mage()->reportsHelper()->__('Week')
					)
				)
			)
		;
		$fieldPeriodType->setData('values', $values);
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::_CLASS;
	}

	/** @return Mage_Adminhtml_Block_Report_Filter_Form */
	private function getBlockAsReportFilterForm() {
		return $this->getEvent()->getBlock();
	}

	const _CLASS = __CLASS__;
}