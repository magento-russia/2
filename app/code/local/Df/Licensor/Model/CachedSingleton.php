<?php
class Df_Licensor_Model_CachedSingleton extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $feature
	 * @param int|Mage_Core_Model_Store $store [optional]
	 * @return bool
	 */
	public function isEnabled($feature, $store = null) {
		/** @var bool $result */
		$result = false;
		// Проверка на умников, которые могут попытаться переопределить df_enabled таким образом,
		// чтобы эта функция всегда возвращала true
		if (Df_Core_Feature::SUPER !== $feature) {
			if (!df_installed()) {
				// Заплатка для Magento CE 1.7.0.1:
				// избегаем обращений к ещё не подключенной базе данных.
				$result = true;
			}
			else {
				/** @var Mage_Core_Model_Store $store */
				$store = Mage::app()->getStore($store);
				/** @var int $storeId */
				$storeId = $store->getId();
				if (isset($this->_enabled[$feature][$storeId])) {
					$result = $this->_enabled[$feature][$storeId];
				}
				else {
					/** @var string[] $validDomains */
					$validDomains = array('www.klavishi.kz', 'www.trololo.kz');
					if (
							in_array(df_a($_SERVER, 'HTTP_HOST'), $validDomains)
						||
							Df_Licensor_Model_Validator_ServerTime::s()->isValid()
					) {
						$result = df_feature($feature)->isEnabled($store);
						$this->_enabled[$feature][$storeId] = $result;
						$this->markCachedPropertyAsModified('_enabled');
					}
				}
			}
		}
		return $result;
	}
	/** @var array(string => array(string => bool)) */
	protected $_enabled;

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return array('_enabled');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return $this->getPropertiesToCache();}

	/** @return Df_Licensor_Model_CachedSingleton */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}