<?php
class Df_1C_Block_System_Config_Form_Field_NonStandardCurrencyCodes
	extends Df_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addColumn(
			self::COLUMN__NON_STANDARD
			,array(
				'label' => 'код'
				,'style' => 'width:3em'
			)
		);
		/**
		 * @var array(array(string => string)) $currencies
			Элемент массива: array('label' => $name,'value' => $code)
		 */
		$currencies = Mage::app()->getLocale()->getOptionCurrencies();
		$this->addColumn(
			self::COLUMN__STANDARD
			,new Varien_Object(
				array(
					'label' => 'валюта'
					,'renderer' => Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Select::i()
					,'options' =>
						array_combine(
							df_column($currencies, 'value')
							,df_column($currencies, 'label')
						)
					,'style' => 'width:120px'
					/**
					 * Обратите внимание, что ширина этой колонки указана в файле CSS.
					 * Атрибут style здесь не работает.
					 */
				)
			)
		);
		$this->_addAfter = false;
		$this->_addButtonLabel = 'добавить...';
	}
	const _CLASS = __CLASS__;
	const COLUMN__NON_STANDARD = 'non_standard_code';
	const COLUMN__STANDARD = 'standard_code';
}