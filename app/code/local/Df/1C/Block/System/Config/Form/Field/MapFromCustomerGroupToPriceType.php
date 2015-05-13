<?php
class Df_1C_Block_System_Config_Form_Field_MapFromCustomerGroupToPriceType
	extends Df_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/** @var Mage_Customer_Model_Resource_Group_Collection $customerGroups */
		$customerGroups = Mage::getResourceModel('customer/group_collection');
		$customerGroups->setRealGroupsFilter();
		$this
			->addColumn(
				self::COLUMN__CUSTOMER_GROUP
				,new Varien_Object(
					array(
						'label' => 'категория покупателей'
						,'renderer' => Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Select::i()
						,'options' => $customerGroups->toOptionHash()
						/**
						 * Обратите внимание, что ширина этой колонки указана в файле CSS.
						 * Атрибут style здесь не работает.
						 */
					)
				)
			)
		;
		$this
			->addColumn(
				self::COLUMN__PRICE_TYPE
				,array(
					'label' => 'типовое соглашение / вид цен'
					,'style' => 'width:15em'
				)
			)
		;
		$this->_addAfter = false;
		$this->_addButtonLabel = 'добавить...';
	}
	const _CLASS = __CLASS__;
	const COLUMN__PRICE_TYPE = 'price_type';
	const COLUMN__CUSTOMER_GROUP = 'customer_group';
}