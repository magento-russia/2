<?php
class Df_Licensor_Model_Feature_Info extends Df_Core_Model_Abstract {
	/**
	 * @override
	 * @return Df_Licensor_Model_Feature
	 */
	public function getFeature() {return $this->cfg(self::P__FEATURE);}

	/**
	 * Для коллекции
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getFeature()->getId();}

	/** @return string */
	public function getState() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				(0 >= $this->getStoresWithFeatureEnabled()->count())
				? Df_Licensor_Model_Collection_Store::FEATURE_ENABLED_NONE
				: (
					$this->isEnabledForAllStoresInCurrentScope()
					? Df_Licensor_Model_Collection_Store::FEATURE_ENABLED_ALL
					: Df_Licensor_Model_Collection_Store::FEATURE_ENABLED_PARTIALLY
				)
			;
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getStateText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->isDisabledForAllStoresInCurrentScope()
				? df_h()->admin()->__(Df_Admin_Const::T_DISABLED)
				: (
					df()->date()->isUnlimited($this->getExpirationDate())
					? $this->addMultistoreCondition(
						df_h()->admin()->__(Df_Admin_Const::T_ENABLED_FOREVER)
					)
					: $this->addMultistoreCondition(
						rm_sprintf(
							df_h()->admin()->__(Df_Admin_Const::T_TILL_PATTERN)
							,df_dts(
								$this->getExpirationDate(), df_h()->licensor()->getDateFormat()
							)
						)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод должен быть публичным,
	 * потому что используется как callable
	 * за пределами своего класса:
	 * @see Df_Licensor_Model_Feature_Info::getStoreNames()
	 *
	 * @param Df_Licensor_Model_Store $store
	 * @return string
	 */
	public function getStoreName(Df_Licensor_Model_Store $store) {
		return $store->getMagentoStore()->getName();
	}

	/** @return string */
	public function getTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_sprintf(
					df_h()->admin()->__(Df_Admin_Const::T_FEATURE_NAME_PATTERN)
					,rm_tag_a(array(
						Df_Core_Model_Output_Html_A::P__HREF => $this->getFeature()->getUrl()
						,Df_Core_Model_Output_Html_A::P__ANCHOR =>
							$this->getFeature()->__($this->getFeature()->getTitle())
					))
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isDisabledForAllStoresInCurrentScope() {
		return Df_Licensor_Model_Collection_Store::FEATURE_ENABLED_NONE === $this->getState();
	}

	/**
	 * @param string $label
	 * @return string
	 */
	private function addMultistoreCondition($label) {
		return rm_concat_clean(' ', $label, $this->getStoresWithFeatureEnabledAsText());
	}

	/** @return Zend_Date */
	private function getExpirationDate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				call_user_func(
					array(
						$this->getStoresWithFeatureEnabled()
						,$this->isEnabledForAllStoresInCurrentScope()
						? 'getMinimalExpirationDate' //"getMaximalExpirationDate"
						: 'getMinimalExpirationDate'
					)
					,$this->getFeature()
				)
			;
			df_assert($this->{__METHOD__} instanceof Zend_Date);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Licensor_Model_Collection_Store $stores
	 * @return array
	 */
	private function getStoreNames(Df_Licensor_Model_Collection_Store $stores) {
		return $stores->walk(array($this, 'getStoreName'));
	}

	/** @return Df_Licensor_Model_Collection_Store */
	private function getStoresWithFeatureEnabled() {
		return df_h()->licensor()->getContext()->getStores()
			->getStoresWithFeatureEnabled($this->getFeature())
		;
	}

	/** @return string */
	private function getStoresWithFeatureEnabledAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					$this->isEnabledForAllStoresInCurrentScope()
				||
					$this->isDisabledForAllStoresInCurrentScope()
				? ''
				: rm_sprintf(
					'(%s)'
					,implode(
						", "
						,array_map(
							array(df_text(), 'quote')
							,df_clean($this->getStoreNames($this->getStoresWithFeatureEnabled()))
						)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isEnabledForAllStoresInCurrentScope() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				(
						$this->getStoresWithFeatureEnabled()->count()
					===
						df_h()->licensor()->getContext()->getStores()->count()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__FEATURE, Df_Licensor_Model_Feature::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__FEATURE = 'feature';
	/**
	 * @static
	 * @param Df_Licensor_Model_Feature $feature
	 * @return Df_Licensor_Model_Feature_Info
	 */
	public static function i(Df_Licensor_Model_Feature $feature) {
		return new self(array(self::P__FEATURE => $feature));
	}
}