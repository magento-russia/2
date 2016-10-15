<?php
// 2015-02-04
// Раньше класс содержал дефект: тестовая учётная запись покупателя
// была привязана к административному сайту, что смысла не имеет:
// такой покупатель не мог делать заказы ни в одном магазине.
// Сегодня обратил внимание, наконец, на лежащую на поверхности причину этого дефекта:
// тестовая учётная запись так создавалась с привязкой к сайту по умолчанию,
// (может быть, так происходит только при запуске установщика из административного интерфейса?)
// а таким магазином при установке являлся административный.
// Теперь тестовая учётная запись добавляется для каждого сайта.
class Df_Customer_Setup_2_23_7 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$this->translateCustomerGroups();
		/**
		 * 2015-03-18
		 * Модуль «Multiple Vendor Marketplace»
		 * http://www.magentocommerce.com/magento-connect/multiple-vendor-marketplace.html
		 * содержит некорректный код в методе
		 * @see VES_AnonymousProduct_Model_Observer_Customer::customer_save_after():
			$customer = $ob->getCustomer();
			if($customer and $customer->getId()) {
				if(!$customer->getDefaultShipping()) {
					$default_billing_id = $customer->getDefaultBilling();
					$billing = Mage::getModel('customer/address')->load($default_billing_id);
					$address = Mage::getModel('customer/address')->setData($billing->getData());
					$address->setEntityId(null)
						->setIsDefaultBilling('0')
						->setIsDefaultShipping('1')->save();
				}
		 		else {
					return;
				}
			}
		 * Некорректны строки:
			$default_billing_id = $customer->getDefaultBilling();
			$billing = Mage::getModel('customer/address')->load($default_billing_id);
		 * Код считает, что $customer->getDefaultBilling() обязательно вернёт непустое значение,
		 * однако при первичном сохранении покупателя адреса у него может не быть,
		 * поэтому такое предположение кода ошибочно.
		 * Более того, невозможно (проверял) программно создать сначала адрес, а потом покупателя:
		 * при создании адреса покупатель должен уже присутствовать!
		 */
		if (!df_module_enabled('VES_AnonymousProduct')) {
			$this->createTestAccounts();
			df_mage()->index()->indexer()->getProcessByCode('catalog_product_price')->reindexAll();
		}
	}

	/**
	 * @param Mage_Core_Model_Website $website
	 * @return void
	 */
	private function createTestAccount(Mage_Core_Model_Website $website) {
		/** @var Df_Customer_Model_Customer $account */
		$account = Df_Customer_Model_Customer::i()
			->setWebsiteId($website->getId())
			->loadByEmail(self::$TEST_ACCOUNT__EMAIL)
		;
		if (!$account->getId()) {
			$account
				->setFirstname('Дмитрий')
				->setLastname('Федюк')
				->setMiddlename('Сергеевич')
				->setEmail(self::$TEST_ACCOUNT__EMAIL)
				->setPassword('demo-shopper')
				->setGender(Df_Customer_Model_Customer::GENDER__MALE)
				->setDob(df_date_create(1982, 7, 8))
				->setGroupId(Df_Customer_Model_Group::ID__GENERAL)
				->setWebsiteId($website->getId())
				->save()
			;
			$this->createTestAddress($account);
		}
	}

	/** @return void */
	private function createTestAccounts() {
		/** @uses createTestAccount() */
		array_map(array($this, 'createTestAccount'), Mage::app()->getWebsites($withDefault = false));
	}

	/**
	 * @param Df_Customer_Model_Customer $account
	 * @return void
	 */
	private function createTestAddress(Df_Customer_Model_Customer $account) {
		Df_Customer_Model_Address::i()
			->setFirstname('Дмитрий')
			->setLastname('Федюк')
			->setMiddlename('Сергеевич')
			->setCountryId(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
			->setRegionId(df_h()->directory()->getRegions()->getIdByName('Бурятия'))
			->setRegion('Бурятия')
			->setCity('Улан-Удэ')
			->setPostcode('670000')
			->setTelephone('+79629197300')
			->setStreetFull('ул. Смолина, дом 81, кв 69')
			->setCustomerId($account->getId())
			->setIsDefaultBilling(true)
			->setIsDefaultShipping(true)
			->setSaveInAddressBook(true)
			->save()
		;
	}

	/** @return void */
	private function translateCustomerGroups() {
		foreach (Df_Customer_Model_Group::c() as $group) {
			/** @var Df_Customer_Model_Group $group */
			$group->setCustomerGroupCode(
				$this->translateCustomerGroupCode($group->getCustomerGroupCode())
			);
			$group->save();
		}
	}

	/**
	 * @param string $name
	 * @param array(string => string) $dictionary
	 * @return string
	 */
	private function translate($name, array $dictionary) {return dfa($dictionary, $name, $name);}

	/**
	 * @param string $code
	 * @return string
	 */
	private function translateCustomerGroupCode($code) {
		return $this->translate($code, array(
			'General' => 'обычный'
			,'NOT LOGGED IN' => 'анонимный'
			,'Private Sales Member' => 'член клуба'
			,'Retailer' => 'розничный'
			,'VIP Member' => 'VIP'
			,'Wholesale' => 'оптовый'
		));
	}

	/** @var string */
	private static $TEST_ACCOUNT__EMAIL = 'shopper@magento-demo.ru';
}