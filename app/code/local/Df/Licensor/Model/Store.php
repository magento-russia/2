<?php
class Df_Licensor_Model_Store extends Df_Core_Model_Abstract {
	/**
	 * @param Df_Licensor_Model_Feature $feature
	 * @return Zend_Date
	 */
	public function getExpirationDateForFeature(Df_Licensor_Model_Feature $feature) {
		// Вычисляем даты истечения для всех функций
		$this->getFeatures();
		/** @var Zend_Date $result */
		$result = df_a($this->_expirationDateForFeature, $feature->getId(), df()->date()->getLeast());
		if (!$feature->isSuper()) {
			if ($this->isSuperFeatureEnabled()) {
				$result = df()->date()->max($result, $this->getExpirationDateForSuperFeature());
			}
		}
		return $result;
	}
	/** @var array */
	private $_expirationDateForFeature = array();

	/**
	 * Возвращает перечень доступных магазину функций
	 * @return Df_Licensor_Model_Collection_Feature
	 */
	public function getFeatures() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Licensor_Model_Collection_Feature::i();
			foreach (df_h()->licensor()->getLicenses() as $license) {
				/** @var Df_Licensor_Model_License $license */
				if ($license->validateStore($this->getMagentoStore())) {
					foreach ($license->getFeatures() as $feature) {
						/** @var Df_Licensor_Model_Feature $feature */
						if (!$this->{__METHOD__}->getItemById($feature->getId())) {
							$this->{__METHOD__}->addItem($feature);
						}
						// Ведём учёт дат истечения для всех функций
						$this->_expirationDateForFeature[$feature->getId()] =
							df()->date()->max(
								$license->getExpirationDate()
								,$this->getExpirationDateForFeature($feature)
							)
						;
					}
				}
			}
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getId() {return $this->getMagentoStore()->getId();}

	/** @return Mage_Core_Model_Store */
	public function getMagentoStore() {return $this->cfg(self::P__MAGENTO_STORE);}

	/**
	 * @param Df_Licensor_Model_Feature $feature
	 * @return bool
	 */
	public function isFeatureEnabled(Df_Licensor_Model_Feature $feature) {
		if (!isset($this->{__METHOD__}[$feature->getId()])) {
			/** @var bool $result */
			$result = false;
			if (!$feature->isSuper()) {
				$result = $this->isSuperFeatureEnabled();;
			}
			if (!$result) {
				$result = df()->date()->isInFuture($this->getExpirationDateForFeature($feature));
			}
			$this->{__METHOD__}[$feature->getId()] = $result;
		}
		return $this->{__METHOD__}[$feature->getId()];
	}

	/** @return Zend_Date */
	private function getExpirationDateForSuperFeature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getExpirationDateForFeature(df_h()->licensor()->getSuperFeature())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isSuperFeatureEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->isFeatureEnabled(df_h()->licensor()->getSuperFeature());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Licensor_Model_Collection_Store */
	public function log() {
		Mage::log(
			rm_sprintf(
				self::LOG_TEMPLATE
				,$this->getId()
				,$this->getMagentoStore()->getName()
				,$this->getMagentoStore()->getCode()
			)
		);
		return $this;
	}
	const LOG_TEMPLATE = 'Id: «%s», name: «%s», code: «%s»';
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MAGENTO_STORE, 'Mage_Core_Model_Store');
	}
	const _CLASS = __CLASS__;
	const P__MAGENTO_STORE = 'magentoStore';
	/**
	 * @static
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Licensor_Model_Store
	 */
	public static function i(Mage_Core_Model_Store $store) {
		return new self(array(self::P__MAGENTO_STORE => $store));
	}
}