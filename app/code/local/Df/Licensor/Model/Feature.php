<?php
class Df_Licensor_Model_Feature extends Df_Core_Model_Abstract {
	/** @return string */
	public function __() {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		return call_user_func_array(array(Mage::helper($this->getModule()), '__'), $arguments);
	}

	/** @return string */
	public function getCode() {return $this->cfg(self::P__CODE);}

	/**
	 * Возвращает доступность данной функции для данного магазина
	 *
	 * @param Mage_Core_Model_Store|null $store[optional]
	 * @return bool
	 */
	public function isEnabled($store = null) {
		if (is_null($store)) {
			// Не указали магазин — используем текущий
			$store = Mage::app()->getStore();
		}
		df_assert($store instanceof Mage_Core_Model_Store);
		if (!isset($this->{__METHOD__}[$store->getId()])) {
			$this->{__METHOD__}[$store->getId()] = $this->calculateEnabled($store);
		}
		return $this->{__METHOD__}[$store->getId()];
	}

	/** @return bool */
	public function isSuper() {return Df_Core_Feature::ALL === $this->getCode();}

	/**
	 * Вычисляет доступность данной функции для данного магазина
	 *
	 * @param Mage_Core_Model_Store $magentoStore
	 * @return bool
	 */
	private function calculateEnabled(Mage_Core_Model_Store $magentoStore) {
		/** @var Df_Licensor_Model_Store $store */
		$store = df_h()->licensor()->getStores()->getItemById($magentoStore->getId());
		if (!($store instanceof Df_Licensor_Model_Store)) {
			df_error(
				self::INVALID_STORE_MESSAGE
				,$magentoStore->getId()
				,$magentoStore->getName()
				,$magentoStore->getCode()
			);
		}
		return $store->isFeatureEnabled($this);
	}

	/**
	 * Возвращает список идентификаторов сайтов (для применения в фильтрах),
	 * для которых данная функция активна
	 * @return array
	 */
	public function getWebsiteIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $result */
			$result = array();
			foreach ($this->getStores() as $store) {
				/** @var Df_Licensor_Model_Store $store */
				$websiteId = (int)$store->getMagentoStore()->getWebsiteId();
				/** @var int $websiteId */
				df_assert_integer($websiteId);
 				$result[]= $websiteId;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает список идентификаторов магазинов (для применения в фильтрах),
	 * для которых данная функция активна
	 * @return int[]
	 */
	public function getStoreIds() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getStores()->getAllIds();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает список магазинов, для которых данная функция активна
	 * @return Df_Licensor_Model_Collection_Store
	 */
	public function getStores() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Licensor_Model_Collection_Store $result; */
			$result = Df_Licensor_Model_Collection_Store::i();
			foreach (df_h()->licensor()->getStores() as $store) {
				/** @var Df_Licensor_Model_Store $store */
				if ($store->isFeatureEnabled($this)) {
					$result->addItem($store);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Используется для идентификации элемента коллекции Df_Licensor_Model_Collection_Feature.
	 * Наличие идентификатора позволяет коллекции использовать его в качестве ключа
	 * ассоциативного массива.
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getCode();}

	/** @return string */
	public function getUrl() {return $this->getParam(self::XML_CONFIG_PARAM__URL);}

	/** @return string */
	public function getTitle() {
		return $this->getParam(self::XML_CONFIG_PARAM__TITLE, $this->getCode());
	}

	/** @return string */
	public function getModule() {
		return $this->getParam(self::XML_CONFIG_PARAM__MODULE, self::XML_CONFIG_PARAM__MODULE_DEFAULT);
	}

	/**
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	protected function getParam($name, $default = '') {
		df_param_string_not_empty($name, 0);
		if (!isset($this->{__METHOD__}[$name])) {
			/** @var string $result */
			$result =
				!$this->getNode()
				? $default
				: df_a($this->getNode()->asArray(), $name, $default)
			;
			df_result_string($result);
			$this->{__METHOD__}[$name] = $result;
		}
		return $this->{__METHOD__}[$name];
	}
	/** @return Mage_Core_Model_Config_Element|null */
	private function getNode() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element|null $result */
			$result = df()->config()->getNodeByKey($this->getNodePath());
			if (!is_null($result)) {
				df_assert($result instanceof Mage_Core_Model_Config_Element);
			}
			else {
				/**
				 * Ветка настроек отсутствует.
				 * Такая ситуация может случиться, когда функция объявлена в лицензии,
				 * но отсутствует в программном коде.
				 * Мы не считаем это ошибкой.
				 */
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getNodePath() {return rm_config_key('df/features', $this->getCode());}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CODE, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const INVALID_STORE_MESSAGE = 'Invalid store. Id: «%d», name: «%s», code: «%s».';
	const P__CODE = 'code';
	const XML_CONFIG_PARAM__MODULE = 'module';
	const XML_CONFIG_PARAM__MODULE_DEFAULT = 'df_admin';
	const XML_CONFIG_PARAM__TITLE = 'title';
	const XML_CONFIG_PARAM__URL = 'url';
	/**
	 * @static
	 * @param string $code
	 * @return Df_Licensor_Model_Feature
	 */
	public static function i($code) {return new self(array(self::P__CODE => $code));}
}