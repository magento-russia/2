<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field {
	/**
	 * @override
	 * @see Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getCssClasses()
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getCssClassesAsText()
	 * @return string[]
	 */
	protected function getCssClasses() {return array_merge(array('rm-select'), parent::getCssClasses());}

	/**
	 * @override
	 * @return string
	 */
	public function getType() {return parent::getType() . '_id';}

	/**
	 * @override
	 * @see Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getConfigShortKey()
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getConfigValue()
	 * @return string
	 */
	protected function getConfigShortKey() {return str_replace('_id', '', $this->getType());}

	/**
	 * Обратите внимание, что если поле типа «выпадащий список» необязательно для заполнения,
	 * то добавление класса «validate-select» будет ошибкой,
	 * потому что «validate-select» требует обязательной заполненности поля.
	 * @override
	 * @return string
	 */
	protected function getValidatorCssClass() {return 'validate-select';}

	/**
	 * 2015-02-15
	 * Вместо класса «»
	 * @override
	 * @return bool
	 */
	protected function needAddRequiredEntryCssClass() {return false;}
}