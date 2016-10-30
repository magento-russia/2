<?php
class Df_Adminhtml_Block_Customer_Edit_Tab_Account extends Mage_Adminhtml_Block_Customer_Edit_Tab_Account {
	/**
	 * Цель перекрытия —
	 * позволить администратору менять привязку пользователя к сайту.
	 * @override
	 * @return Df_Adminhtml_Block_Customer_Edit_Tab_Account
	 */
	public function initForm() {
		parent::initForm();
		if (df_cfgr()->admin()->sales()->customers()->getEnableWebsiteChanging()) {
			// Позволяем администратору редактировать поле website_id
			/** @var Varien_Object $websiteIdElement */
			$websiteIdElement = $this->getForm()->getElement('website_id');
			df_assert($websiteIdElement);
			$websiteIdElement->unsetData('disabled');
		}
		return $this;
	}
}