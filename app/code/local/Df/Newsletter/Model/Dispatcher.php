<?php
class Df_Newsletter_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function newsletter_subscriber_save_before(
		Varien_Event_Observer $observer
	) {
		try {
			/** @var Mage_Newsletter_Model_Subscriber $subscriber */
			$subscriber = $observer->getEvent()->getData('subscriber');
			/**
			 * Заметил, что в магазине mamymall.ru
			 * сюда иногда передаётся не объект класса Mage_Newsletter_Model_Subscriber,
			 * а нечто другое.
			 * Чтобы понять тип неправильно передаваемого сюда параметра,
			 * используем @see df_assert_class() вместо @see instanceof().
			 */
			df_assert_class($subscriber, 'Mage_Newsletter_Model_Subscriber');
			if (
					(0 === rm_nat0($subscriber->getStoreId()))
				&&
					df_cfg()->newsletter()->subscription()->fixSubscriberStore()
				&&
					df_enabled(Df_Core_Feature::NEWSLETTER)
			) {
				/** @var Df_Customer_Model_Customer $customer */
				$customer = Df_Customer_Model_Customer::i();
				$customer->setData('website_id', Mage::app()->getStore()->getWebsiteId());
				$customer->loadByEmail($subscriber->getSubscriberEmail());
				/** @var bool $isSubscribeOwnEmail */
				$isSubscribeOwnEmail =
						rm_session_customer()->isLoggedIn()
					&&
						$customer->getId() === rm_session_customer()->getId()
				;
				if ($isSubscribeOwnEmail) {
					$subscriber->setStoreId(Mage::app()->getStore()->getId());
				}
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}