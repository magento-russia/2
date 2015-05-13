<?php
/**
 * Reward update points form
 */
class Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update
	extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * Getter
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer()
	{
		return Mage::registry('current_customer');
	}

	/**
	 * Prepare form before rendering HTML
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('reward_');
		$form->setFieldNameSuffix('reward');
		$fieldset =
			$form
				->addFieldset(
					'update_fieldset'
					,array(
						'legend' => df_h()->reward()->__('Update Reward Points Balance')
					)
				)
		;
		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset
				->addField(
					'store'
					,'select'
					,array(
						'name' => 'store_id'
						,'title' => df_h()->reward()->__('Store')
						,'label' => df_h()->reward()->__('Store')
						,'values' => $this->_getStoreValues()
					)
				)
			;
		}
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что addField() возвращает не $fieldset, а созданное поле.
		 */
		$fieldset
			->addField(
				'points_delta'
				,'text'
				,array(
					'name' => 'points_delta'
					,'title' => df_h()->reward()->__('Update Points')
					,'label' => df_h()->reward()->__('Update Points')
					,'note'  => df_h()->reward()->__('Enter Negative Number to Subtract Balance')
				)
			)
		;
		$fieldset
			->addField(
				'comment'
				,'text'
				,array(
					'name' => 'comment'
					,'title' => df_h()->reward()->__('Comment')
					,'label' => df_h()->reward()->__('Comment')
				)
			)
		;
		$fieldset =
			$form->addFieldset(
				'notification_fieldset'
				,array(
					'legend' => df_h()->reward()->__('Reward Points Notifications')
				)
			)
		;
		$fieldset
			->addField(
				'update_notification'
				,'checkbox'
				,array(
					'name' => 'reward_update_notification'
					,'label' => df_h()->reward()->__('Subscribe for balance updates')
					,'checked' => !!$this->getCustomer()->getRewardUpdateNotification()
					,'value' => 1
				)
			)
		;
		/*
		$fieldset->addField('warning_notification', 'checkbox', array(
			'name' => 'reward_warning_notification'
			,'label' => df_h()->reward()->__('Subscribe for points expiration notifications')
			,'checked' => !!$this->getCustomer()->getRewardWarningNotification()
			,'value' => 1
		)); */
		$this->setForm($form);
		return parent::_prepareForm();
	}

	/**
	 * Retrieve source values for store drop-dawn
	 * @return array
	 */
	protected function _getStoreValues()
	{
		$customer = $this->getCustomer();
		if (!$customer->getWebsiteId()
			|| Mage::app()->isSingleStoreMode()
			|| $customer->getSharingConfig()->isGlobalScope())
		{
			return df_mage()->adminhtml()->system()->storeSingleton()->getStoreValuesForForm();
		}

		$stores = df_mage()->adminhtml()->system()->storeSingleton()
			->getStoresStructure(false, array(), array(), array($customer->getWebsiteId()));
		$values = array();
		$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
		foreach ($stores as $websiteId => $website) {
			$values[]= array(
				'label' => $website['label'],'value' => array()
			);
			if (isset($website['children']) && is_array($website['children'])) {
				foreach ($website['children'] as $groupId => $group) {
					if (isset($group['children']) && is_array($group['children'])) {
						$options = array();
						foreach ($group['children'] as $storeId => $store) {
							$options[]= array(
								'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],'value' => $store['value']
							);
						}
						$values[]= array(
							'label' => str_repeat($nonEscapableNbspChar, 4) . $group['label'],'value' => $options
						);
					}
				}
			}
		}
		return $values;
	}

	/** @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update */
	public static function i() {return df_block(__CLASS__);}
}