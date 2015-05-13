<?php
class Df_Customer_Model_Setup_2_23_7 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		$this->translateCustomerGroups();
		/**
		 * 2015-03-18
		 * Модуль «Multiple Vendor Marketplace»
		 * @link http://www.magentocommerce.com/magento-connect/multiple-vendor-marketplace.html
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
		if (!$this->isTestAccountExist() && !df_module_enabled('VES_AnonymousProduct')) {
			$this->createTestAccount();
			$this->createTestAddresses();
			df_mage()->index()->indexer()->getProcessByCode('catalog_product_price')->reindexAll();
		}
	}

	/** @return void */
	private function createTestAccount() {
		$this->getTestAccount()
			->setNameFirst('Дмитрий')
			->setNameLast('Федюк')
			->setNameMiddle('Сергеевич')
			->setEmail(self::$TEST_ACCOUNT__EMAIL)
			->setPassword('demo-shopper')
			->setGender(Df_Customer_Model_Customer::GENDER__MALE)
			->setDob(df()->date()->create(1982, 7, 8))
			->setGroupId(Df_Customer_Model_Group::ID__GENERAL)
			->setWebsiteId(Mage::app()->getWebsite()->getId())
			->save()
		;
	}

	/** @return void */
	private function createTestAddresses() {
		Df_Customer_Model_Address::i()
			->setNameFirst('Дмитрий')
			->setNameLast('Федюк')
			->setNameMiddle('Сергеевич')
			->setCountryId(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
			->setRegionId(df_h()->directory()->getRegions()->getIdByName('Бурятия'))
			->setRegion('Бурятия')
			->setCity('Улан-Удэ')
			->setPostcode('670000')
			->setTelephone('+79629197300')
			->setStreetFull('ул. Смолина, дом 81, кв 69')
			->setCustomerId($this->getTestAccount()->getId())
			->setIsDefaultBilling(true)
			->setIsDefaultShipping(true)
			->setSaveInAddressBook(true)
			->save()
		;
	}
	
	/** @return Df_Customer_Model_Customer */
	private function getTestAccount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Customer_Model_Customer::i();
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isTestAccountExist() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Customer_Model_Customer $testAccount */
			$testAccount =
				Df_Customer_Model_Customer::i()
					->setWebsiteId(Mage::app()->getWebsite()->getId())
					->loadByEmail(self::$TEST_ACCOUNT__EMAIL)
			;
			$this->{__METHOD__} = !!$testAccount->getId();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Customer_Model_Setup_2_23_7 */
	private function translateCustomerGroups() {
		foreach (Df_Customer_Model_Group::c() as $group) {
			/** @var Df_Customer_Model_Group $group */
			/**
			 * Magento Community Edition версий ниже 1.7.0.0
			 * содержит дефект, который приводит к сбою при сохранении объектов с нулевым идентификатором.
			 * Правильный код (смотреть в Magento CE не ниже 1.7.0.0):
			 * @see Mage_Core_Model_Resource_Db_Abstract::_checkUnique():
				if ($object->getId() || $object->getId() === '0') {
				  $select->where($this->getIdFieldName() . '!=?', $object->getId());
			 	}
			 * Дефектный код (смотреть в Magento CE ниже 1.7.0.0):
			 * @see Mage_Core_Model_Resource_Db_Abstract::_checkUnique():
			 * @see Mage_Core_Model_Mysql4_Abstract::_checkUnique():
				if ($object->getId()) {
				  $select->where($this->getIdFieldName().' != ?', $object->getId());
				}
			 */
			/** @var bool $hasCheckUniqueBug */
			static $hasCheckUniqueBug;
			if (!isset($hasCheckUniqueBug)) {
				$hasCheckUniqueBug = df_magento_version('1.7.0.0', '<');
			}
			if (!$hasCheckUniqueBug || $group->getId()) {
				$group
					->setCustomerGroupCode(
						$this->translateCustomerGroupCode($group->getCustomerGroupCode())
					)
					->save()
				;
			}

		}
		return $this;
	}

	/**
	 * @param string $name
	 * @param array(string => string) $dictionary
	 * @return string
	 */
	private function translate($name, array $dictionary) {return df_a($dictionary, $name, $name);}

	/**
	 * @param string $code
	 * @return string
	 */
	private function translateCustomerGroupCode($code) {
		return $this->translate($code, array(
			'General' => 'неупорядоченные'
			,'Wholesale' => 'оптовые'
			,'Retailer' => 'розничные'
			,'NOT LOGGED IN' => 'анонимные'
		));
	}

	/** @var string */
	private static $TEST_ACCOUNT__EMAIL = 'shopper@magento-demo.ru';

	/** @return Df_Customer_Model_Setup_2_23_7 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}