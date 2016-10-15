<?php
class Df_Newsletter_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function newsletter_subscriber_save_before(Varien_Event_Observer $o) {
		try {
			/** @var Mage_Newsletter_Model_Subscriber $subscriber */
			$subscriber = $o['subscriber'];
			/**
			 * Заметил, что в магазине mamymall.ru
			 * сюда иногда передаётся не объект класса Mage_Newsletter_Model_Subscriber,
			 * а нечто другое.
			 * Чтобы понять тип неправильно передаваемого сюда параметра,
			 * используем @see df_assert_class() вместо @see instanceof().
			 */
			df_assert_class($subscriber, 'Mage_Newsletter_Model_Subscriber');
			if (
				0 === df_nat0($subscriber->getStoreId())
				&& df_cfg()->newsletter()->subscription()->fixSubscriberStore()
			) {
				/** @var Df_Customer_Model_Customer $customer */
				$customer = Df_Customer_Model_Customer::i();
				$customer->setData('website_id', df_website_id());
				$customer->loadByEmail($subscriber->getSubscriberEmail());
				/** @var bool $isSubscribeOwnEmail */
				$isSubscribeOwnEmail =
						df_customer_logged_in()
					&&
						$customer->getId() === df_session_customer()->getId()
				;
				if ($isSubscribeOwnEmail) {
					$subscriber->setStoreId(df_store_id());
				}
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}