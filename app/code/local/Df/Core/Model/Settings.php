<?php
abstract class Df_Core_Model_Settings extends Df_Core_Model {
	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function getFloat($key, $store = null) {return df_float($this->value($key, $store));}

	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return int
	 */
	public function getInteger($key, $store = null) {return df_int($this->value($key, $store));}

	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return int
	 */
	public function getNatural($key, $store = null) {return df_nat($this->value($key, $store));}

	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return int
	 */
	public function getNatural0($key, $store = null) {return df_nat0($this->value($key, $store));}

	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return string
	 */
	public function getPassword($key, $store = null) {
		df_param_string_not_empty($key, 0);
		/** @var string $cacheKey */
		$cacheKey = $key . $this->storeIdCacheSuffix($store);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			$this->{__METHOD__}[$cacheKey] = df_decrypt($this->value($key, $store));
		}
		return $this->{__METHOD__}[$cacheKey];
	}


	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return string
	 */
	public function getString($key, $store = null) {return $this->value($key, $store);}

	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return string
	 */
	public function getStringNullable($key, $store = null) {return df_nts($this->value($key, $store));}

	/**
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return bool
	 */
	public function getYesNo($key, $store = null) {return df_bool($this->value($key, $store));}

	/**
	 * @used-by adaptKey()
	 * @return string
	 */
	protected function getKeyPrefix() {return $this->cfg(self::$P__PREFIX);}

	/**
	 * @used-by Df_1C_Config_Api_General::ccMapFrom1C()
	 * @used-by Df_1C_Config_Api_Product_Prices::_map()
	 * @used-by Df_Directory_Settings_Countries_EACU::::iso2Codes()
	 * @used-by Df_Directory_Settings_Countries_Popular::iso2Codes()
	 * @param string $key
	 * @param string $itemClass
	 * @param string $methodForValue
	 * @param string|null $methodForKey [optional]
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return array(string|int => mixed)
	 */
	protected function map($key, $itemClass, $methodForValue, $methodForKey = null, $store = null) {
		df_param_string_not_empty($key, 0);
		df_param_string_not_empty($itemClass, 1);
		/** @var string $cacheKey */
		$cacheKey = $key . $this->storeIdCacheSuffix($store);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var Varien_Object[] $result */
			$result = array();
			/** @var string|null $mapSerialized */
			$mapSerialized = $this->getStringNullable($key, $store);
			if ($mapSerialized) {
				df_assert_string_not_empty($mapSerialized);
				/** @var array(array(string => string)) $map */
				$map = df_nta(Df_Admin_Config_Backend_Table::unserialize($mapSerialized, $itemClass));
				/** @var array(string|int => mixed) $result */
				$result = array();
				foreach ($map as $itemData) {
					/** @var array(string => $mixed) $itemData */
					/** @var Df_Admin_Config_MapItem $item */
					/**
					 * 2015-02-13
					 * Убираем пустые поля, чтобы при наличии свойства типа
					 * $this->_prop(self::P__ISO2, DF_V_ISO2, false)
					 * валидатор не возбуждал исключительную ситуацию:
					 * «значение «» недопустимо для свойства «iso2»».
					 * Дело в том, что 3-й параметр ($isRequired) метода
					 * @see Df_Core_Model::_prop()
					 * предохраняет от исключительной ситуции при провале валидации только в том случае,
					 * если значение свойства равно null.
					 * @see Df_Core_Model::_validateByConcreteValidator()
					 *
					 * Конкретно для свойства типа @see Df_Core_Model::V_ISO2
					 * я это исправил в методе @see Df_Zf_Validate_String_Iso2::filter(),
					 * однако чтобы у нас не возникало в будущем сбоев для свойств других типов,
					 * я добавил @uses df_clean().
					 */
					$itemData = df_clean($itemData);
					$item = Df_Admin_Config_MapItem::ic($itemClass, $itemData);
					if ($item->isValid()) {
						/** @var mixed $value */
						$value = call_user_func(array($item, $methodForValue));
						if (!$methodForKey) {
							$result[]= $value;
						}
						else {
							$result[call_user_func(array($item, $methodForKey))] = $value;
						}
					}
				}
			}
			$this->{__METHOD__}[$cacheKey] = $result;
		}
		return $this->{__METHOD__}[$cacheKey];
	}

	/**
	 * @used-by _construct()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_store($this->cfg(self::P__STORE));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @return string
	 */
	private function adaptKey($key) {return $this->getKeyPrefix() . $key;}

	/**
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return int
	 */
	private function storeIdCacheSuffix($store) {
		return is_null($store) ? $this->_storeIdDefaultCacheSuffix : '$' . df_store_id($store);
	}

	/**
	 * Как показывает XDebug, стандартный код работает быстрее,
	 * чем напрашивающаяся оптимизация:
			$result = @$this->_valueCacheable[$key][$storeId];
			if (!is_null($result)) {
				$result = df_n_get($result);
			}
			else {
				$result = Mage::getStoreConfig($this->adaptKey($key), $store);
				$this->_valueCacheable[$key][$storeId] = df_n_set($result);
			}
			return $result;
	 *
	 * 2015-02-05
	 * Сегодня обратил внимание на теоретическое обоснование,
	 * почему @uses isset() работает быстрее, чем оператор @:
	 * смотрите комментарий к методу @see Df_Catalog_Model_Product::getId()
	 * Цитирую оттуда (на случай удаления того комментария):
	 * «Согласно документации PHP:
	 * http://php.net/manual/language.operators.errorcontrol.php
	 * «If you have set a custom error handler function with set_error_handler()
	 * then it will still get called,
	 * but this custom error handler can (and should) call error_reporting()
	 * which will return 0 when the call that triggered the error was preceded by an @.»
	 *
	 * В нашем случае, если элемент массива с заданным ключом отсутствует,
	 * то PHP вызовет обработчик сбоев, установленный посредством @see set_error_handler()
	 * По этой причине не думаю, что @ даёт ускорение.
	 *
	 * Более того, там же в документации написано:
	 * «If the track_errors feature is enabled,
	 * any error message generated by the expression
	 * will be saved in the variable $php_errormsg.
	 * This variable will be overwritten on each error,
	 * so check early if you want to use it.»
	 * То есть, если включена опция «track_errors» (по умолчанию она отключена),
	 * http://php.net/manual/errorfunc.configuration.php#ini.track-errors
	 * то интерпретатор PHP ещё и будет запоминать
	 * все подобные несущественные в нашем случае сбои,
	 * когда в массиве отсутствует элемент с заданным ключом.
	 * Какое уж тут ускорение!»
	 *
	 * 2015-03-07
	 * Придумал новую оптимизацию:
	 * в качестве кэша можно использовать одномерный массив вместо двумерного,
	 * объединив ключи $key и $storeId в единый ключ кэша.
	 * Раньше код был таким:
			if (!isset($this->_valueCacheable[$key][$storeId])) {
				$this->_valueCacheable[$key][$storeId] = df_n_set($store->getConfig($this->adaptKey($key)));
			}
			return df_n_get($this->_valueCacheable[$key][$storeId]);
	 * @param string $key
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return mixed|null
	 */
	private function value($key, $store = null) {
		$store = df_store($store);
		/** @var string $cacheKey */
		$cacheKey = $key . $this->storeIdCacheSuffix($store);
		if (!isset($this->_cache[$cacheKey])) {
			$this->_cache[$cacheKey] = df_n_set($store->getConfig($this->adaptKey($key)));
		}
		return df_n_get($this->_cache[$cacheKey]);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__STORE, Df_Core_Model_StoreM::_C, false)
			->_prop(self::$P__PREFIX, DF_V_STRING, false)
		;
		$this->_storeIdDefaultCacheSuffix = '$' . $this->store()->getId();
	}
	const _C = __CLASS__;
	const P__STORE = 'store';

	/** @var array(string => array(int => mixed)) */
	private $_cache;
	/** @var string */
	private $_storeIdDefaultCacheSuffix;

	/** @var string */
	private static $P__PREFIX = 'prefix';

	/**
	 * @used-by Df_Core_Model_Settings_Jquery::s()
	 * @param string $class
	 * @param string $prefix [optional]
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return Df_Core_Model_Settings
	 */
	protected static function sc($class, $prefix = '', $store = null) {
		return df_sc($class, __CLASS__, array(
			self::P__STORE => df_store($store), self::$P__PREFIX => $prefix
		), $prefix);
	}
}