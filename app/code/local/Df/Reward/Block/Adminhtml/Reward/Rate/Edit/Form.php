<?php
/**
 * Reward rate edit form
 */
class Df_Reward_Block_Adminhtml_Reward_Rate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * Getter
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public function getRate()
	{
		return Mage::registry('current_reward_rate');
	}

	/**
	 * Prepare form
	 * @return Df_Reward_Block_Adminhtml_Reward_Rate_Edit_Form
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form','action' => $this->getUrl('*/*/save', array('_current' => true)),'method' => 'post'
		));
		$form->setFieldNameSuffix('rate');
		$fieldset =
			$form->addFieldset(
				'base_fieldset'
				,array(
					'legend' => df_h()->reward()->__('Reward Exchange Rate Information')
				)
			)
		;
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
		$fieldset
			->addField(
				'website_id'
				,'select'
				,array(
					'name' => 'website_id'
					,'title'  => df_h()->reward()->__('Website')
					,'label'  => df_h()->reward()->__('Website')
					,'values' => Df_Reward_Model_Source_Website::s()->toOptionArray()
				)
			)
		;
		$fieldset
			->addField(
				'customer_group_id'
				,'select'
				,array(
					'name' => 'customer_group_id'
					,'title' => df_h()->reward()->__('Customer Group')
					,'label' => df_h()->reward()->__('Customer Group')
					,'values' => Df_Reward_Model_Source_Customer_Groups::I()->toOptionArray()
				)
			)
		;
		$fieldset
			->addField(
				'direction'
				,'select'
				,array(
					'name' => 'direction'
					,'title' => df_h()->reward()->__('Direction')
					,'label' => df_h()->reward()->__('Direction')
					,'values' => $this->getRate()->getDirectionsOptionArray()
				)
			)
		;
		$rateRenderer = new Df_Reward_Block_Adminhtml_Reward_Rate_Edit_Form_Renderer_Rate;
		$rateRenderer->setRate($this->getRate());
		$fromIndex = $this->getRate()->getDirection() == Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
				   ? 'points' : 'currency_amount';
		$toIndex = $this->getRate()->getDirection() == Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
				 ? 'currency_amount' : 'points';
		$fieldset
			->addField(
				'rate_to_currency'
				,'note'
				,array(
					'title' => df_h()->reward()->__('Rate')
					,'label' => df_h()->reward()->__('Rate')
					,'value_index' => $fromIndex,'equal_value_index' => $toIndex
				)
			)
			->setRenderer($rateRenderer)
		;
		$form->setUseContainer(true);
		$form->setValues($this->getRate()->getData());
		$this->setForm($form);
		return parent::_prepareForm();
	}
}