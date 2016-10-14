<?php
class Df_Admin_Config_Backend extends Mage_Core_Model_Config_Data {
	/** @return string */
	public function getFailureMessageTemplate() {return '';}

	/**
	 * @used-by getFieldConfigParamInternal()
	 * @return Mage_Core_Model_Config_Element
	 */
	public function getFieldConfig() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element $result */
			$result = $this['field_config'];
			/**
			 * 2015-04-18
			 * Обнаружил, что в сценарии вызова из @see Df_Admin_Config_Backend_Table::_afterLoad()
			 * поле «field_config» незаполнено.
			 * Однако заполнено поле «path», и мы можем получить узел настроек через него.
			 */
			$this->{__METHOD__} = $result ? $result : rm_config_adminhtml_field($this['path']);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Admin_Config_Backend_Table::_afterLoad()
	 * @used-by Df_Admin_Config_Backend_Validator::getStrategyClass()
	 * @used-by Df_Shipping_Config_Backend_Validator_Strategy_Origin::getStrategyClass()
	 * @used-by Df_Shipping_Config_Backend_Validator_Strategy_Origin_SpecificCountry::getLimitationCountry(
	 * @param string $name
	 * @param bool $required [optional]
	 * @param string|int|float|null $defaultValue [optional]
	 * @return string|int|float|null
	 */
	public function getFieldConfigParam($name, $required = false, $defaultValue = null) {
		/** @var string|int|float|null $result */
		$result = $this->getFieldConfigParamInternal($name);
		if (is_null($result)) {
			if ($required) {
				df_error(
					'Требуется непустое значение для параметра «%s».'
					, df_concat_xpath($this['path'], $name)
				);
			}
			else {
				$result = $defaultValue;
			}
		}
		return $result;
	}

	/** @return string */
	public function getModuleName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $configPath */
			$configPath = $this->_getData('path');
			df_assert_string_not_empty($configPath);
			/** @var string[] $configPathAsArray */
			$configPathAsArray = df_explode_xpath($configPath);
			df_assert_eq(3, count($configPathAsArray));
			$this->{__METHOD__} = rm_config_adminhtml()->getSystemConfigNodeLabel(
				$configPathAsArray[0], $configPathAsArray[1]
			);
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает перечень магазинов, к которым относится текущее значение настройки
	 * @return Mage_Core_Model_Resource_Store_Collection|Mage_Core_Model_Mysql4_Store_Collection
	 */
	public function getStores() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Resource_Store_Collection|Mage_Core_Model_Mysql4_Store_Collection $result */
			$result = rm_store()->getResourceCollection();
			df()->assert()->storeCollection($result);
			switch($this->getScope()) {
				case self::$SCOPE__DEFAULT:
					$result->setWithoutDefaultFilter();
					break;
				case self::$SCOPE__STORES:
					$result->addIdFilter($this->getScopeId());
					break;
				case self::$SCOPE__WEBSITES:
					$result->addWebsiteFilter($this->getScopeId());
					break;
				default:
					df_error();
			}
			$this->{__METHOD__} = $result;
		}
		df()->assert()->storeCollection($this->{__METHOD__});
		return $this->{__METHOD__};
	}

	/** @return int[] */
	public function getWebsiteIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			switch($this->getScope()) {
				case self::$SCOPE__DEFAULT:
					$result = array_keys(Mage::app()->getWebsites($withDefault = false, $codeKey = false));
					break;
				case self::$SCOPE__STORES:
					$result = array(rm_store($this->getScopeId())->getWebsiteId());
					break;
				case self::$SCOPE__WEBSITES:
					$result = array($this->getScopeId());
					break;
				default:
					df_error();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Admin_Config_BackendChecker::check() при провале проверки
	 * @param Exception $e
	 * @return void
	 */
	public function handleCheckerException(Exception $e) {}

	/**
	 * @param string $name
	 * @return string|int|float|null
	 */
	private function getFieldConfigParamInternal($name) {
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = rm_n_set(rm_leaf_child($this->getFieldConfig(), $name));
		}
		return rm_n_get($this->{__METHOD__}[$name]);
	}

	/** @return Mage_Core_Model_Resource_Website_Collection|Mage_Core_Model_Mysql4_Website_Collection */
	private function getWebsites() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Resource_Website_Collection|Mage_Core_Model_Mysql4_Website_Collection $result */
			$result = rm_website()->getResourceCollection();
			switch($this->getScope()) {
				case self::$SCOPE__DEFAULT:
					$result = Mage::app()->getWebsites($withDefault = false);
					break;
				case self::$SCOPE__STORES:
					$result->addIdFilter($this->getScopeId());
					break;
				case self::$SCOPE__WEBSITES:
					//$result->addWebsiteFilter($this->getScopeId());
					$result->addIdFilter($this->getScopeId());
					break;
				default:
					df_error();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Admin_Config_BackendChecker::_construct()
	 * @used-by Df_Admin_Config_Backend_Validator_Strategy::_construct()
	 */
	const _C = __CLASS__;
	/** @var string */
	private static $SCOPE__DEFAULT = 'default';
	/** @var string */
	private static $SCOPE__STORES = 'stores';
	/** @var string */
	private static $SCOPE__WEBSITES = 'websites';
}