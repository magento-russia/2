<?php
class Df_Customer_Block_Account_Dashboard_Info extends Mage_Customer_Block_Account_Dashboard_Info {
	/**
	 * Цель перекрытия —
	 * предоставить администратору возможность удалять вкладку
	 * «Уведомления» (подписка на рассылку) из личного кабиета покупателей.
	 * http://magento-forum.ru/topic/2320/
	 * @override
	 * @return boolean
	 */
	public function isNewsletterEnabled() {
		return
				parent::isNewsletterEnabled()
			&&
				!(
					df_module_enabled(Df_Core_Module::TWEAKS)
				  &&
					df_cfg()->tweaks()->account()->removeSectionNewsletterSubscriptions()
				)
		;
	}

	/**
	 * Это свойство используется родительским методом
	 * @used-by Mage_Customer_Block_Account_Dashboard_Info::getSubscriptionObject()
	 * В Magento Community Edition использование необъявленных свойств работает без сбоев
	 * по причине наличия метода @see Varien_Object::__get(),
	 * однако в Российской сборке Magento метод @see Varien_Object::__get() мешал и я его удалил
	 * путём перекрытия класса Varien_Object в области local.
	 * @var Mage_Newsletter_Model_Subscriber|null
	 */
	protected $_subscription = null;
}