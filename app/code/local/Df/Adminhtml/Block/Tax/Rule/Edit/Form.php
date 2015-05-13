<?php
class Df_Adminhtml_Block_Tax_Rule_Edit_Form extends Mage_Adminhtml_Block_Tax_Rule_Edit_Form {
	/**
	 * Цель перекрытия —
	 * добавить комментарий к полю «Налоговая ставка» на экране
	 * «Продажи» → «Налоги и наценки» → «Налоговые правила»  → <Налоговое правило>.
	 * @override
	 * @return Df_Adminhtml_Block_Tax_Rule_Edit_Form
	 */
	protected function _prepareForm() {
		parent::_prepareForm();
		/** @var Varien_Data_Form_Element_Fieldset $fieldset */
		$fieldset = $this->getForm()->getElement('base_fieldset');
		df_assert($fieldset);
		/** @var Varien_Data_Form_Element_Abstract $taxRateField */
		$taxRateField = $fieldset->getElements()->searchById('tax_rate');
		df_assert($taxRateField);
		$taxRateField->setData(
			'note'
			,'Если Вы выберите несколько ставок — система применит только ставку с наибольшим процентом.'
		);
		return $this;
	}
}