<?php
class Df_Adminhtml_Block_Tax_Class_Edit_Form extends Mage_Adminhtml_Block_Tax_Class_Edit_Form {
	/**
	 * 2015-04-11
	 * Цель перекрытия —
	 * добавление возможности указывать для налоговой ставки страны,
	 * чтобы в дальнейшем, при использовании налоговых ставок в выпадающих списках,
	 * (например, при назначении налоговой ставки товару)
	 * не показывать администраторам интернет-магазинам одной страны налоговые ставки других стран).
	 * @override
	 * @see Mage_Adminhtml_Block_Tax_Class_Edit_Form::_prepareForm()
	 * @used-by Mage_Adminhtml_Block_Widget_Form::_beforeToHtml()
	 * @return Df_Adminhtml_Block_Tax_Class_Edit_Form
	 */
	protected function _prepareForm() {
		parent::_prepareForm();
		// Добавляем выпадающий список для указания страны
		/** @var Varien_Data_Form_Element_Fieldset $fieldset */
		$fieldset = $this->getForm()->getElement('base_fieldset');
		/** @var Mage_Tax_Model_Class $class */
		$class = Mage::registry('tax_class');
		/** @var string $value */
		$value = $class[Df_Tax_Model_Class::P__ISO2];
		if (!$value) {
			$value = df_store_iso2('general/store_information/merchant_country');
		}
		$fieldset->addField(Df_Tax_Model_Class::P__ISO2, 'select', array(
			'name'  => Df_Tax_Model_Class::P__ISO2
			,'label' => 'Страна'
			,'class' => 'required-entry'
			,'value' => $value
			,'values'   => df_countries_options($emptyLabel = false)
			,'required' => false
		));
		return $this;
	}
}