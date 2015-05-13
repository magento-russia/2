<?php
class Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$prefix = '_customerbalance';
		$form->setHtmlIdPrefix($prefix);
		$form->setFieldNameSuffix('customerbalance');
		/** @var Df_Customer_Model_Customer $customer */
		$customer = Df_Customer_Model_Customer::ld($this->getRequest()->getParam('id'));
		/** @var $fieldset Varien_Data_Form_Element_Fieldset */
		$fieldset =
			$form->addFieldset(
				'storecreidt_fieldset'
				,array(
					'legend' => df_h()->customer()->balance()->__('Update Balance')
				)
			)
		;
		if (!Df_CustomerBalance_Model_Balance::s()->shouldCustomerHaveOneBalance($customer)) {
			$fieldset
				->addField(
					'website_id'
					,'select'
					,array(
						'name' => 'website_id'
						,'label' => df_h()->customer()->balance()->__('Website')
						,'title' => df_h()->customer()->balance()->__('Website')
						,'values' => df_mage()->adminhtml()->system()->storeSingleton()
							->getWebsiteValuesForForm()
						,'onchange' => 'updateEmailWebsites()'
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
				'amount_delta'
				,'text'
				,array(
					'name' => 'amount_delta'
					,'label' => df_h()->customer()->balance()->__('Balance Change')
					,'title' => df_h()->customer()->balance()->__('Balance Change')
					,'comment' =>
						df_h()->customer()->balance()->__('An amount on which to change the balance')
				)
			)
		;
		$fieldset
			->addField(
				'notify_by_email'
				,'checkbox'
				,array(
					'name' => 'notify_by_email'
					,'label' => df_h()->customer()->balance()->__('Notify customer by email')
					,'title' => df_h()->customer()->balance()->__('Notify customer by email')
					,'after_element_html' => '<script type="text/javascript">'
						. "
						updateEmailWebsites();
						$('{$prefix}notify_by_email').disableSendemail = function() {
							$('{$prefix}store_id').disabled = (this.checked) ? false : true;
						}.bind($('{$prefix}notify_by_email'));
						Event.observe('{$prefix}notify_by_email', 'click', $('{$prefix}notify_by_email').disableSendemail);
						$('{$prefix}notify_by_email').disableSendemail();
						"
						. '</script>'
				)
			)
		;
		$fieldset
			->addField(
				'store_id'
				,'select'
				,array(
					'name' => 'store_id'
					,'label' =>
						df_h()->customer()->balance()->__(
							'Send email notification from the following Store View'
						)
					,'title' =>
						df_h()->customer()->balance()->__(
							'Send email notification from the following Store View'
						)
				)
			)
		;
		$fieldset
			->addField(
				'comment'
				,'text'
				,array(
					'name' => 'comment'
					,'label' => df_h()->customer()->balance()->__('Comment')
					,'title' => df_h()->customer()->balance()->__('Comment')
					,'comment' => df_h()->customer()->balance()->__('Comment')
				)
			)
		;
		if ($customer->isReadonly()) {
			if ($form->getElement('website_id')) {
				$form->getElement('website_id')->setReadonly(true, true);
			}
			$form->getElement('store_id')->setReadonly(true, true);
			$form->getElement('amount_delta')->setReadonly(true, true);
			$form->getElement('notify_by_email')->setReadonly(true, true);
		}
		$form->setValues($customer->getData());
		$this->setForm($form);
		return $this;
	}
}