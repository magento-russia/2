<?php
class Df_Admin_Model_Config_Backend extends Mage_Core_Model_Config_Data {
	/** @return string */
	public function getFailureMessageTemplate() {return '';}

	/** @return Mage_Core_Model_Config_Element */
	public function getFieldConfig() {return $this->_getData('field_config');}

	/**
	 * @param string $name
	 * @param bool $required [optional]
	 * @param string|int|float|null $defaultValue[optional]
	 * @return string|int|float|null
	 */
	public function getFieldConfigParam($name, $required = false, $defaultValue = null) {
		/** @var string|int|float|null $result */
		$result = $this->getFieldConfigParamInternal($name);
		if (is_null($result)) {
			if ($required) {
				df_error(
					'Требуется непустое значение для параметра «%s».'
					, rm_config_key($this->getData('path'), $name)
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
			$configPathAsArray = explode('/', $configPath);
			df_assert_eq(3, count($configPathAsArray));
			$this->{__METHOD__} =
				df_mage()->adminhtml()->getConfig()->getSystemConfigNodeLabel(
					$configPathAsArray[0]
					,$configPathAsArray[1]
				)
			;
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
			$result = Mage::app()->getStore()->getResourceCollection();
			df()->assert()->storeCollection($result);
			switch($this->getScope()) {
				case self::SCOPE__DEFAULT:
					$result->setWithoutDefaultFilter();
					break;
				case self::SCOPE__STORES:
					$result->addIdFilter($this->getScopeId());
					break;
				case self::SCOPE__WEBSITES:
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
				case self::SCOPE__DEFAULT:
					$result = array_keys(Mage::app()->getWebsites($withDefault = false, $codeKey = false));
					break;
				case self::SCOPE__STORES:
					$result = array(Mage::app()->getStore($this->getScopeId())->getWebsiteId());
					break;
				case self::SCOPE__WEBSITES:
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
	 * Этот метод вызывается из @see Df_Admin_Model_Config_BackendChecker::check() при провале проверки
	 * @param Exception $e
	 * @return void
	 */
	public function handleCheckerException(Exception $e) {}

	/**
	 * @param string $paramName
	 * @return string|int|float|null
	 */
	private function getFieldConfigParamInternal($paramName) {
		if (!isset($this->{__METHOD__}[$paramName])) {
			$this->{__METHOD__}[$paramName] = rm_n_set(
				rm_xml_child_simple($this->getFieldConfig(), $paramName)
			);
		}
		return rm_n_get($this->{__METHOD__}[$paramName]);
	}

	/** @return Mage_Core_Model_Resource_Website_Collection|Mage_Core_Model_Mysql4_Website_Collection */
	private function getWebsites() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Resource_Website_Collection|Mage_Core_Model_Mysql4_Website_Collection $result */
			$result = Mage::app()->getWebsite()->getResourceCollection();
			df()->assert()->websiteCollection($result);
			switch($this->getScope()) {
				case self::SCOPE__DEFAULT:
					$result = Mage::app()->getWebsites($withDefault = false);
					break;
				case self::SCOPE__STORES:
					$result->addIdFilter($this->getScopeId());
					break;
				case self::SCOPE__WEBSITES:
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

	const _CLASS = __CLASS__;
	const SCOPE__DEFAULT = 'default';
	const SCOPE__STORES = 'stores';
	const SCOPE__WEBSITES = 'websites';
}