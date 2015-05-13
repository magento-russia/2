<?php
abstract class Df_Core_Block_Admin_Entity_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getBuilderClass();

	/**
	 * @override
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm() {
		$this->getBuilder()->run();
		$this->setForm($this->getBuilder()->getForm());
		return parent::_prepareForm();
	}

	/** @return Df_Core_Model_Form_Builder */
	private function getBuilder() {
		if (!isset($this->{__METHOD__})) {
			/** @var Varien_Data_Form $form */
			$form =
				new Varien_Data_Form (
					array(
						'id' => 'edit_form'
						,'action' => $this->_getData('action')
						,'method' => 'post'
					)
				)
			;
			$form->setData('use_container', true);
			/** @var Varien_Data_Form_Element_Fieldset $fieldset */
			$fieldset = $form->addFieldset('base_fieldset', array('legend' => 'Основное'));
			df_assert($fieldset instanceof Varien_Data_Form_Element_Fieldset);
			/** @var Df_Core_Model_Form_Builder $result */
			$result =
				df_model(
					$this->getBuilderClass()
					,array(Df_Core_Model_Form_Builder::P__FIELDSET=> $fieldset)
				)
			;
			df_assert($result instanceof Df_Core_Model_Form_Builder);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}