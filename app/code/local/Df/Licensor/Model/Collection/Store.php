<?php
/**
 * @method Df_Licensor_Model_Store|null getItemById(int $idValue)
 */
class Df_Licensor_Model_Collection_Store extends Df_Varien_Data_Collection {
	/**
	 * Возвращает ближайшую дату истечения лицензии на указанную функцию среди всех магазинов
	 * @param Df_Licensor_Model_Feature $feature
	 * @return Zend_Date
	 */
	public function getMinimalExpirationDate(Df_Licensor_Model_Feature $feature) {
		$result = df()->date()->getUnlimited();
		foreach ($this as $store) {
			/** @var Df_Licensor_Model_Store $store */
			$result = df()->date()->min($result, $store->getExpirationDateForFeature($feature));
		}
		return $result;
	}

	/**
	 * Возвращает самую дальнуюю дату истечения лицензии на указанную функцию среди всех магазинов
	 *
	 * @param Df_Licensor_Model_Feature $feature
	 * @return Zend_Date
	 */
	public function getMaximalExpirationDate(Df_Licensor_Model_Feature $feature) {
		$result = df()->date()->getUnlimited();
		foreach ($this as $store) {
			/** @var Df_Licensor_Model_Store $store */
			$result = df()->date()->max($result, $store->getExpirationDateForFeature($feature));
		}
		return $result;
	}

	/**
	 * Перечисляет магазины, для которых функция включена
	 *
	 * @param Df_Licensor_Model_Feature $feature
	 * @return Df_Licensor_Model_Collection_Store
	 */
	public function getStoresWithFeatureEnabled(Df_Licensor_Model_Feature $feature) {
		if (!isset($this->{__METHOD__}[$feature->getId()])) {
			/** @var Df_Licensor_Model_Collection_Store $result */
			$result = Df_Licensor_Model_Collection_Store::i();
			foreach ($this as $store) {
				/** @var Df_Licensor_Model_Store $store */
				// Раньше тут стояло выражение $store->isFeatureEnabled($feature),
				// однако оно не совсем верно, потому что не проверяет корректность времени на сервере.
				// Такая проверка осуществляется только в df_enabled.
				if (df_enabled($feature->getCode(), $store->getMagentoStore())) {
					$result->addItem($store);
				}
			}
			$this->{__METHOD__}[$feature->getId()] = $result;
		}
		return $this->{__METHOD__}[$feature->getId()];
	}

	/** @return Df_Licensor_Model_Collection_Store */
	public function log() {
		foreach ($this as $store) {
			/** @var Df_Licensor_Model_Store $store */
			$store->log();
		}
		return $this;
	}

	/** @return Df_Licensor_Model_Collection_Store */
	public function loadAll() {
		$this->clear();
		$this->_setIsLoaded(true);
		foreach (Df_Core_Model_Store::c($loadDefault = true) as $store) {
			/** @var Mage_Core_Model_Store $store */
			$this->addItem(Df_Licensor_Model_Store::i($store));
		}
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Licensor_Model_Store::_CLASS;}

	const _CLASS = __CLASS__;
	const FEATURE_ENABLED_ALL = 'all';
	const FEATURE_ENABLED_NONE = 'none';
	const FEATURE_ENABLED_PARTIALLY = 'partially';

	/** @return Df_Licensor_Model_Collection_Store */
	public static function i() {return new self;}
}