<?php
/**
 * Cообщение:		«adminhtml_block_salesrule_actions_prepareform»
 * Источник:		Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions::_prepareForm()
 * [code]
		Mage::dispatchEvent('adminhtml_block_salesrule_actions_prepareform', array('form' => $form));
 * [/code]
 *
 * Назначение:		Обработчик может изменить вкладку «Actions»
 * 					формы редактирования ценовых правил
 */
class Df_Adminhtml_Model_Event_Block_SalesRule_Actions_PrepareForm extends Df_Core_Model_Event {
	/**
	 * Возвращает элемент формы: выпадающий список типов действий (типов ценовых правил)
	 * @return Varien_Data_Form_Element_Select
	 */
	public function getActionElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getActionsFieldset()->getElements()->searchById('simple_action');
			df_assert($this->{__METHOD__} instanceof Varien_Data_Form_Element_Select);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает элемент формы: набор полей вкладки «Actions»
	 * @return Varien_Data_Form_Element_Fieldset
	 */
	public function getActionsFieldset() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getForm()->getElements()->searchById('action_fieldset');
			df_assert($this->{__METHOD__} instanceof Varien_Data_Form_Element_Fieldset);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает форму «Actions»
	 * @return Varien_Data_Form
	 */
	public function getForm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getObserver()->getData('form');
			df_assert($this->{__METHOD__} instanceof Varien_Data_Form);
		}
		return $this->{__METHOD__};
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'adminhtml_block_salesrule_actions_prepareform';}

	/**
	 * @used-by Df_PromoGift_Observer::adminhtml_block_salesrule_actions_prepareform()
	 * @used-by Df_PromoGift_Model_Handler_Adminhtml_Block_Actions_PrepareForm_AddInputMaxUsagesPerQuote::getEventClass()
	 */

}