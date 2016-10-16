<?php
abstract class Df_Core_Model extends Mage_Core_Model_Abstract implements Df_Core_Destructable {
	/**
	 * Обратите внимание,
	 * что родительский деструктор вызывать не надо и по правилам PHP даже нельзя,
	 * потому что в родительском классе (и всех классах-предках)
	 * метод @see __destruct() не объявлен.
	 * @return void
	 */
	public function __destruct() {
		/**
		 * Для глобальных объекто-одиночек,
		 * чей метод @uses isDestructableSingleton() возвращает true,
		 * метод @see _destruct()
		 * будет вызван на событие «controller_front_send_response_after»:
		 * @see Df_Core_Observer::controller_front_send_response_after().
		 *
		 * 2015-08-14
		 * Как правило, это связано с кэшированием данных на диск.
		 * Единственное на данный момент исключение:
		 * метод @see Df_Eav_Model_Translator::_destruct(),
		 * который использует деструктор не для кэширования на диск, а для логирования.
		 */
		if (!$this->isDestructableSingleton()) {
			$this->_destruct();
		}
	}

	/**
	 * Размещайте программный код деинициализации объекта именно в этом методе,
	 * а не в стандартном деструкторе @see __destruct().
	 *
	 * Не все потомки класса @see Df_Core_Model
	 * деинициализируется посредством стандартного деструктора.
	 *
	 * В частности, глобальные объекты-одиночки
	 * деинициализировать посредством стандартного деструктора опасно,
	 * потому что к моменту вызова стандартного деструктора
	 * сборщик мусора Zend Engine мог уже уничтожить другие объекты,
	 * которые требуются для деинициализации.
	 *
	 * Для глобальных объекто-одиночек,
	 * чей метод @see Df_Core_Model::isDestructableSingleton() возвращает true,
	 * метод @see Df_Core_Model::_destruct()
	 * будет вызван на событие «controller_front_send_response_after»:
	 * @see Df_Core_Observer::controller_front_send_response_after().
	 *
	 * 2015-08-14
	 * Как правило, это связано с кэшированием данных на диск.
	 * Единственное на данный момент исключение:
	 * метод @see Df_Eav_Model_Translator::_destruct(),
	 * который использует деструктор не для кэширования на диск, а для логирования.
	 *
	 * @override
	 * @see Df_Core_Destructable::_destruct()
	 * @used-by __destruct()
	 * @used-by Df_Core_GlobalSingletonDestructor::process()
	 * @return void
	 */
	public function _destruct() {$this->cacheSave();}

	/**
	 * @param mixed[] $arguments
	 * @return mixed
	 * @throws Exception
	 */
	public function callByMixin(array $arguments) {
		/** @var string $method */
		$method = df_first($arguments);
		// Временно отключаем миксин для данного метод
		// чтобы не попадать в бескнечную рекурсию.
		$this->_disabledMixins[$method] = true;
		/** @var mixed $result */
		try {
			$result = call_user_func_array(array($this, $method), df_tail($arguments));
			unset($this->_disabledMixins[$method]);
		}
		catch (Exception $e) {
			unset($this->_disabledMixins[$method]);
			df_error($e);
		}
		return $result;
	}

	/**
	 * Этот метод отличается от методов @see getData(), @see offsetGet(), @see _getData()
	 * возможностью указать значение по умолчанию.
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function cfg($key, $default = null) {
		/** @var mixed $result */
		/**
		 * 2015-03-26
		 * Раньше здесь стоял вызов @see getData()
		 * Однако при новой реализации @see getData()
		 * разумнее вызывать сразу @uses offsetGet():
		 * нам тогда не приходится обрабатывать ситуацию с пустым ключом $key:
		 * при вызове @see cfg() ключ не может быть пустым.
		 *
		 * Обратите внимание, что вызывать @see _getData() здесь ошибочно,
		 * потому что тогда могут не сработать валидаторы и фильтры.
		 */
		$result = $this->offsetGet($key);
		// Некоторые фильтры заменяют null на некоторое другое значение,
		// поэтому обязательно учитываем равенство null
		// значения свойства ДО применения фильтров.
		//
		// Раньше вместо !is_null($result) стояло !$result.
		// !is_null выглядит логичней.
		//
		// 2015-02-10
		// Раньше код был таким:
		// $valueWasNullBeforeFilters = dfa($this->_valueWasNullBeforeFilters, $key, true);
		// return !is_null($result) && !$valueWasNullBeforeFilters ? $result : $default;
		// Изменил его ради ускорения.
		// Неожиданным результатом стала простота и понятность нового кода.
		return
			null === $result
			|| !isset($this->_valueWasNullBeforeFilters[$key])
			|| $this->_valueWasNullBeforeFilters[$key]
			? $default
			: $result
		;
	}

	/**
	 * @override
	 * Обратите внимание, что мы сознательно никак не используем параметр $index
	 * и не поддерживаем сложные ключи $key, как это делает родительский метод.
	 *
	 * Фильтры и валидаторы для присутствующих в @see $_data ключей
	 * уже были применены при вызове @see _prop(),
	 * поэтому данные уже проверены и отфильтрованы,
	 * и при вызове @see getData() без параметров
	 * мы можем спокойно вернуть массив @see $_data.
	 *
	 * @see Varien_Object::getData()
	 * @param string $key
	 * @param null|string|int $index
	 * @return mixed
	 */
	public function getData($key = '', $index = null) {
		return '' === $key ? $this->_data : $this->offsetGet($key);
	}

	/**
	 * @override
	 * @see Varien_Object::getId()
	 * @used-by Varien_Data_Collection::addItem()
	 * @return string|int
	 */
	public function getId() {
		return
			empty($this->_idFieldName) && empty($this->_resourceName) && is_null($this->_getData('id'))
			/**
			 * Объект родительского класса такую ситуации переводит в исключительную.
			 * Мы же, для использования модели в коллекциях, создаём идентификатор.
			 * Конечно, таким образом мы лишаемся возможности проверки объекта на новизну,
			 * однако, раз ресурсная модель не установлена,
			 * то такая проверка нам вроде бы и не нужна.
			 */
			? $this->getAutoGeneratedId()
			: parent::getId()
		;
	}

	/**
	 * 2015-02-09
	 * Если потомки используют коллекции, то они должны перекрыть этот метод.
	 * Отключаем унаследованную реализацию,
	 * потому что в Российской сборке Magento другая архитетура работы с ресурсными моделями.
	 * Смотрите также комментарии к методам:
	 * @see Df_Core_Model::_getResource()
	 * @see Df_Core_Model_Resource_Collection::getResource()
	 * Родительский метод: @see Mage_Core_Model_Abstract::getResourceCollection()
	 * @override
	 * @return Df_Core_Model_Resource_Collection
	 */
	public function getResourceCollection() {df_abstract($this); return null;}

	/**
	 * 2015-02-09
	 * Этот метод никто в Magento не использует.
	 * Родительский метод: @see Mage_Core_Model_Abstract::getResourceName()
	 * @override
	 * @return string
	 */
	public function getResourceName() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @used-by getData()
	 * @see Varien_Object::offsetGet()
	 * @see ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	*/
	public function offsetGet($offset) {
		/** @var mixed $result */
		if (array_key_exists($offset, $this->_data)) {
			/**
			 * Фильтры и валидаторы для присутствующих в @see $_data ключей
			 * уже были применены при вызове @see _prop(),
			 * поэтому данные уже проверены и отфильтрованы.
			 */
			$result = $this->_data[$offset];
		}
		else {
			// Обратите внимание, что фильтры и валидаторы применяются только единократно,
			// и повторно мы в эту ветку кода не попадём
			// из-за срабатывания условия array_key_exists($key, $this->_data) выше
			// (даже если фильтры для null вернут null, наличие ключа array('ключ' => null))
			// достаточно, чтобы не попадать в данную точку программы повторно.
			//
			// Обрабатываем здесь только те случаи,
			// когда запрашиваются значения неицициализированных свойств объекта
			$result = $this->_applyFilters($offset, null);
			$this->_validate($offset, $result);
			$this->_data[$offset] = $result;
		}
		return $result;
	}

	/**
	 * @override
	 * @see Varien_Object::setData()
	 * @param string|array(string => mixed) $key
	 * @param mixed $value
	 * @return Df_Core_Model
	 */
	public function setData($key, $value = null) {
		/**
		 * Раньше мы проводили валидацию лишь при извлечении значения свойства,
		 * в методе @see Df_Core_Model::getData().
		 * Однако затем мы сделали улучшение:
		 * перенести валидацию на более раннюю стадию — инициализацию свойства
		 * @see Df_Core_Model::setData(),
		 * и инициализацию валидатора/фильтра
		 * @see Df_Core_Model::_prop().
		 * Это улучшило диагностику случаев установки объекту некорректных значений свойств,
		 * потому что теперь мы возбуждаем исключительную ситуацию
		 * сразу при попытке установки некорректного значения.
		 * А раньше, когда мы проводили валидацию лишь при извлечении значения свойства,
		 * то при диагностике было не вполне понятно,
		 * когда конкретно объекту было присвоено некорректное значение свойства.
		 */
		if (is_array($key)) {
			$this->_checkForNullArray($key);
			$key = $this->_applyFiltersToArray($key);
			$this->_validateArray($key);
		}
		else {
			$this->_checkForNull($key, $value);
			$value = $this->_applyFilters($key, $value);
			$this->_validate($key, $value);
		}
		parent::setData($key, $value);
		return $this;
	}

	/**
	 * @override
	 * @param mixed $value
	 * @return Df_Core_Model
	 */
	public function setId($value) {
		parent::setId($value ? $value : null);
		return $this;
	}

	/**
	 * @param string $class
	 * @return void
	 */
	public function setMixin($class) {
		$this->_data[self::P__MIXIN] = Df_Core_Model_Mixin::ic($class, $this);
	}

	/**
	 * @param string $key
	 * @param Zend_Validate_Interface|\Df\Zf\Validate\Type|string|mixed[] $validator
	 * @param bool|null $isRequired [optional]
	 * @throws \Df\Core\Exception
	 * @return Df_Core_Model
	 */
	protected function _prop($key, $validator, $isRequired = null) {
		/**
		 * Полезная проверка!
		 * Как-то раз ошибочно описал поле без значения:
			private static $P__TYPE;
		 * И при вызове $this->_prop(self::$P__TYPE, DF_V_STRING_NE)
		 * получил диагностическое сообщение: «значение «» недопустимо для свойства «».»
		 */
		df_param_string_not_empty($key, 0);
		/**
		 * Обратите внимание, что если метод @see _prop() был вызван с двумя параметрами,
		 * то и count($arguments) вернёт 2,
		 * хотя в методе @see _prop() всегда доступен и 3-х параметр: $isRequired.
		 * Другими словами, @see func_get_args() не возвращает параметры по умолчанию,
		 * если они не были реально указаны при вызове текущего метода.
		 */
		/**
		 * Хотя документация к PHP говорит,
		 * что @uses func_num_args() быть параметром других функций лишь с версии 5.3 PHP,
		 * однако на самом деле @uses func_num_args() быть параметром других функций
		 * в любых версиях PHP 5 и даже PHP 4.
		 * http://3v4l.org/HKFP7
		 * http://php.net/manual/function.func-num-args.php
		 */
		if (2 < func_num_args()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$isRequired = df_last($arguments);
			/** @var bool $hasRequiredFlag */
			$hasRequiredFlag = is_bool($isRequired) || is_null($isRequired);
			if ($hasRequiredFlag) {
				$validator = array_slice($arguments, 1, -1);
			}
			else {
				$isRequired = null;
				$validator = df_tail($arguments);
			}
		}
		/** @var Zend_Validate_Interface[] $additionalValidators */
		$additionalValidators = array();
		/** @var Zend_Filter_Interface[] $additionalFilters */
		$additionalFilters = array();
		if (!is_array($validator)) {
			$validator = \Df\Core\Validator::resolveForProperty(
				$this, $validator, $key, $skipOnNull = false === $isRequired
			);
			df_assert($validator instanceof Zend_Validate_Interface);
		}
		else {
			/** @var array(Zend_Validate_Interface|\Df\Zf\Validate\Type|string) $additionalValidatorsRaw */
			$additionalValidatorsRaw = df_tail($validator);
			$validator = \Df\Core\Validator::resolveForProperty(
				$this, df_first($validator), $key, $skipOnNull = false === $isRequired
			);
			df_assert($validator instanceof Zend_Validate_Interface);
			foreach ($additionalValidatorsRaw as $additionalValidatorRaw) {
				/** @var Zend_Validate_Interface|Zend_Filter_Interface|string $additionalValidatorsRaw */
				/** @var Zend_Validate_Interface|Zend_Filter_Interface $additionalValidator */
				$additionalValidator = \Df\Core\Validator::resolveForProperty(
					$this, $additionalValidatorRaw, $key
				);
				if ($additionalValidator instanceof Zend_Validate_Interface) {
					$additionalValidators[]= $additionalValidator;
				}
				if ($additionalValidator instanceof Zend_Filter_Interface) {
					$additionalFilters[]= $additionalValidator;
				}
			}
		}
		$this->_validators[$key][] = $validator;
		if ($validator instanceof Zend_Filter_Interface) {
			/** @var Zend_Filter_Interface $filter */
			$filter = $validator;
			$this->_addFilter($key, $filter);
		}
		foreach ($additionalFilters as $additionalFilter) {
			/** @var Zend_Filter_Interface $additionalFilter */
			$this->_addFilter($key, $additionalFilter);
		}
		/**
		 * Раньше мы проводили валидацию лишь при извлечении значения свойства,
		 * в методе @see getData().
		 * Однако затем мы сделали улучшение:
		 * перенести валидацию на более раннюю стадию — инициализацию свойства @see setData(),
		 * и инициализацию валидатора/фильтра @see _prop().
		 * Это улучшило диагностику случаев установки объекту некорректных значений свойств,
		 * потому что теперь мы возбуждаем исключительную ситуацию
		 * сразу при попытке установки некорректного значения.
		 * А раньше, когда мы проводили валидацию лишь при извлечении значения свойства,
		 * то при диагностике было не вполне понятно,
		 * когда конкретно объекту было присвоено некорректное значение свойства.
		 */
		/** @var bool $hasValueVorTheKey */
		$hasValueVorTheKey = array_key_exists($key, $this->_data);
		if ($hasValueVorTheKey) {
			\Df\Core\Validator::checkProperty($this, $key, $this->_data[$key], $validator);
		}
		foreach ($additionalValidators as $additionalValidator) {
			/** @var Zend_Validate_Interface $additionalValidator */
			$this->_validators[$key][] = $additionalValidator;
			if ($hasValueVorTheKey) {
				\Df\Core\Validator::checkProperty($this, $key, $this->_data[$key], $additionalValidator);
			}
		}
		return $this;
	}

	/**
	 * 2015-02-09
	 * Если потомки используют ресурсную модель, то они должны перекрыть этот метод.
	 * Отключаем унаследованную реализацию,
	 * потому что в Российской сборке Magento другая архитетура работы с ресурсными моделями.
	 * Смотрите также комментарии к методам:
	 * @see Df_Core_Model::getResourceCollection()
	 * @see Df_Core_Model_Resource_Collection::getResource().
	 * Родительский метод: @see Mage_Core_Model_Abstract::_getResource()
	 * @override
	 * @return void
	 */
	protected function _getResource() {df_abstract($this); return null;}

	/**
	 * 2015-02-09
	 * Этот метод никто извне класса не использует,
	 * и классы-предки тоже его не используют,
	 * а классы-потомки не должны его использовать,
	 * потому что архитектура инициализации моделей Российской сборке Magento
	 * дне подразумевает использования метода @see _init().
	 * Читайте комментарий к методу @see _getResource().
	 * @see Df_Core_Model_Resource_Collection::::_init()
	 * Родительский метод: @see Mage_Core_Model_Abstract::_init()
	 * @override
	 * @param string $resourceModel
	 * @return Df_Core_Model
	 */
	protected function _init($resourceModel) {df_should_not_be_here(); return null;}

	/**
	 * 2015-02-09
	 * Этот метод никто в Magento не использует, кроме перекрытого нами метода
	 * @see Mage_Core_Model_Abstract::_init().
	 * Родительский метод: @see Mage_Core_Model_Abstract::_setResourceModel()
	 * @param string $resourceName
	 * @param string|null $resourceCollectionName [optional]
	 * @return void
	 */
	protected function _setResourceModel($resourceName, $resourceCollectionName = null) {
		df_should_not_be_here();
	}

	/**
	 * @used-by cachedI()
	 * @return string[]
	 */
	protected function cached() {return array();}

	/**
	 * 2015-08-14
	 * Отныне значения свойств по умолчанию кэшируются для каждой витрины отдельно.
	 * Если нужно, чтобы кэшированным значением свойства
	 * могли пользоваться сразу все витрины, то перечислите это свойсто массиве,
	 * возвращаемом данным методом @see cachedGlobal()
	 * @used-by cachedGlobalI()
	 * @return string[]
	 */
	protected function cachedGlobal() {return array();}

	/**
	 * 2015-08-14
	 * @used-by cachedGlobalObjectsI()
	 * @return string[]
	 */
	protected function cachedGlobalObjects() {return array();}

	/**
	 * 2015-08-14
	 * Отныне по умолчанию для кэшируемых свойств
	 * используются упрощённые быстрый алгоритмы сериализации и десериализации
	 * @uses json_encode() / @uses json_decode()
	 * Эти алгоритмы быстры, но не умеют работать с объектами.
	 *
	 * Если Вам нужно кэшировать свойства, содержащее объекты,
	 * то перечислите это свойсто массиве,
	 * возвращаемом данным методом @see cachedObjects()
	 * Тогда для сериализации и десериализации этих свойств
	 * будут использоваться более медленные функции @see serialize() / @see unserialize().
	 *
	 * http://stackoverflow.com/a/7723730
	 * http://stackoverflow.com/a/804053
	 * @used-by cachedObjectsI()
	 * @return string[]
	 */
	protected function cachedObjects() {return array();}

	/**
	 * @used-by cacheKeyGlobal()
	 * @see Df_Core_Model_Cache_Store::cacheKeySuffix()
	 * @see Df_Localization_Realtime_Dictionary::cacheKeySuffix()
	 * @return string
	 */
	protected function cacheKeySuffix() {return '';}

	/**
	 * @used-by cacheSaveProperty()
	 * @see Df_Core_Model_Geo_Cache::cacheLifetime()
	 * @see Df_YandexMarket_Model_Category_Adviser::cacheLifetime()
	 * @return int|null
	 */
	protected function cacheLifetime() {return null; /* пожизненно*/}

	/**
	 * @used-by cacheSave()
	 * @return void
	 */
	protected function cacheSaveBefore() {}

	/**
	 * @used-by cacheSaveProperty()
	 * @return string|string[]
	 */
	protected function cacheTags() {return array();}

	/**
	 * @used-by isCacheEnabled()
	 * @return string
	 */
	protected function cacheType() {return '';}

	/** @return Df_Core_Model_Mixin */
	protected function createMixin() {return Df_Core_Model_Mixin::ic(Df_Core_Model_Mixin::class, $this);}

	/**
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @used-by Df_Core_Model_Cache_Url::getUrl()
	 * @return bool
	 */
	protected function isCacheEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->hasPropertiesToCache()
				&& (!$this->cacheType() || df_cache_enabled($this->cacheType()))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Если этот метод вернёт true,
	 * то система вызовет метод @see Df_Core_Model::_destruct()
	 * не в стандартном деструкторе __destruct(),
	 * а на событие «controller_front_send_response_after»:
	 * @see Df_Core_Observer::controller_front_send_response_after().
	 *
	 * Опасно проводить деинициализацию глобальных объектов-одиночек
	 * в стандартном деструкторе @see __destruct(),
	 * потому что к моменту вызова деструктора для данного объекта-одиночки
	 * сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
	 * требуемые при деинициализации (например, для сохранения кэша).
	 *
	 * 2015-08-14
	 * Как правило, это связано с кэшированием данных на диск.
	 * Единственное на данный момент исключение:
	 * метод @see Df_Eav_Model_Translator::_destruct(),
	 * который использует деструктор не для кэширования на диск, а для логирования.
	 *
	 * @used-by __destruct()
	 * @used-by _construct()
	 * @return bool
	 */
	protected function isDestructableSingleton() {
		// 2015-08-14
		// Я так понял, что если объекту нужно сохранить кэш на диск,
		// то он — 100% должен делать это на событие «controller_front_send_response_after»
		// а не когда стандартный сборщик мусора будет всё рушить.
		return $this->hasPropertiesToCache();
	}

	/**
	 * Вызывайте этот метод для тех свойств,чьё кэшрованное значение изменилось.
	 * Такие свойства система заново сохранит в кэше в конце сеанса работы.
	 * Например, такое свойство может быть ассоциативным массивом,
	 * который заполняется постепенно, от сеанса к сеансу.
	 * Во время первого сеанса (начальное формирование кэша)
	 * могут быть заполнены лишь некоторые ключи такого массива
	 * (те, в которых была потребность в данном сеане),
	 * а вот во время следующих сеансов этот массив может дополняться новыми значениями.
	 * @param string $propertyName
	 * @return void
	 */
	protected function markCachedPropertyAsModified($propertyName) {
		$this->_cachedPropertiesModified[$propertyName] = true;
	}

	/**
	 * @param string $method
	 * @return mixed|null
	 */
	protected function mixin($method) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		/** @var string $method */
		$method = df_first($arguments);
		return
			isset($this->_disabledMixins[$method])
			? null
			: call_user_func_array(array($this->getMixin(), $method), df_tail($arguments))
		;
	}

	/**
	 * 2015-08-14
	 * @used-by Df_Localization_Dictionary::e()
	 * @param string $localPath [optional]
	 * @return string
	 */
	protected function modulePath($localPath = '') {
		if (!isset($this->{__METHOD__}[$localPath])) {
			$this->{__METHOD__}[$localPath] = df_cc_path(
				Mage::getConfig()->getModuleDir('', df_module_name($this))
				,df_path_n($localPath)
			);
		}
		return $this->{__METHOD__}[$localPath];
	}

	/**
	 * @used-by Df_Catalog_Model_XmlExport_Product::getConfigurableParent()
	 * @used-by Df_Core_Model_Action::getErrorMessage_moduleDisabledByAdmin()
	 * @used-by Df_Shipping_Config_Backend_Validator_Strategy_Origin_SpecificCountry::validate()
	 * @return string
	 */
	protected function moduleTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_module_title();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @param Zend_Filter_Interface $filter
	 * @return void
	 */
	private function _addFilter($key, Zend_Filter_Interface $filter) {
		$this->_filters[$key][] = $filter;
		/**
		 * Не используем @see isset(), потому что для массива
		 * $array = array('a' => null)
		 * isset($array['a']) вернёт false,
		 * что не позволит нам фильтровать значения параметров,
		 * сознательно установленные в null при конструировании объекта.
		 */
		if (array_key_exists($key, $this->_data)) {
			$this->_data[$key] = $filter->filter($this->_data[$key]);
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	private function _applyFilters($key, $value) {
		/** @var Zend_Filter_Interface[] $filters */
		/** @noinspection PhpParamsInspection */
		$filters = dfa($this->_filters, $key, array());
		foreach ($filters as $filter) {
			/** @var Zend_Filter_Interface $filter */
			$value = $filter->filter($value);
		}
		return $value;
	}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	private function _applyFiltersToArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$params[$key] = $this->_applyFilters($key, $value);
		}
		return $params;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	private function _checkForNull($key, $value) {
		$this->_valueWasNullBeforeFilters[$key] = is_null($value);
	}

	/**
	 * @param array(string => mixed) $params
	 * @return void
	 */
	private function _checkForNullArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$this->_checkForNull($key, $value);
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @throws \Df\Core\Exception
	 * @return void
	 */
	private function _validate($key, $value) {
		/** @var @var array(Zend_Validate_Interface|\Df\Zf\Validate\Type) $validators */
		/** @noinspection PhpParamsInspection */
		$validators = dfa($this->_validators, $key, array());
		foreach ($validators as $validator) {
			/** @var Zend_Validate_Interface|\Df\Zf\Validate\Type $validator */
			\Df\Core\Validator::checkProperty($this, $key, $value, $validator);
		}
	}

	/**
	 * @param array(string => mixed) $params
	 * @throws \Df\Core\Exception
	 * @return void
	 */
	private function _validateArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$params[$key] = $this->_validate($key, $value);
		}
	}

	/**
	 * 2015-08-14
	 * @used-by hasPropertiesToCache()
	 * @return string[]
	 */
	private function cachedAll() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedAllGlobal(), $this->cachedAllPerStore());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAll() 
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string[]
	 */
	private function cachedAllGlobal() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedGlobalI(), $this->cachedGlobalObjectsI());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAll()
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string[]
	 */
	private function cachedAllPerStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedI(), $this->cachedObjectsI());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by _construct()
	 * @return string[]
	 */
	private function cachedAllSimple() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedI(), $this->cachedGlobalI());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAllGlobal()
	 * @return string[]
	 */
	private function cachedGlobalI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cachedGlobal();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAllGlobal()
	 * @return string[]
	 */
	private function cachedGlobalObjectsI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cachedGlobalObjects();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @used-by isCacheEnabled()
	 * @return string[]
	 */
	private function cachedI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cached();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by _construct()
	 * @return string[]
	 */
	private function cachedObjectsI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cachedObjects();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by cacheKeyPerStore()
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string
	 */
	private function cacheKeyGlobal() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $suffix */
			$suffix = (string)$this->cacheKeySuffix();
			if ('' !== $suffix) {
				/**
				 * 2015-08-15
				 * Не все символы позволены в качестве символов ключа кэширования.
				 * Как ни странно, неизвестно, что быстрее: @uses md5() или @see sha1()
				 * http://stackoverflow.com/questions/2722943
				 */
				$suffix = md5($suffix);
			}
			$this->{__METHOD__} = get_class($this) . $suffix;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string
	 */
	private function cacheKeyPerStore() {
		if (!isset($this->{__METHOD__})) {
			if (!Df_Core_State::s()->isStoreInitialized()) {
				df_error(
					'При кэшировании в разрезе магазина для объекта класса «%s» произошёл сбой,'
					. ' потому что система ещё не инициализировала текущий магазин.'
					, get_class($this)
				);
			}
			$this->{__METHOD__} = $this->cacheKeyGlobal() . '[' . df_store()->getCode() . ']';
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by _construct()
	 * @return void
	 */
	private function cacheLoad() {
		if ($this->isCacheEnabled()) {
			$this->cacheLoadArea($this->cachedAllGlobal(), $this->cacheKeyGlobal());
			/**
			 * При вызове метода @uses Df_Core_Model::getCacheKeyPerStore()
			 * может произойти исключительная ситуация в том случае,
			 * когда текущий магазин системы ещё не инициализирован
			 * (вызов Mage::app()->getStore() приводит к исключительной ситуации),
			 * поэтому вызываем @uses Df_Core_Model::getCacheKeyPerStore()
			 * только если в этом методе есть реальная потребность,
			 * т.е. если класс действительно имеет свойства, подлежащие кэшированию в разрезе магазина,
			 * и текущий магазин уже инициализирован.
			 */
			if ($this->cachedAllPerStore() && Df_Core_State::s()->isStoreInitialized()) {
				$this->cacheLoadArea($this->cachedAllPerStore(), $this->cacheKeyPerStore());
			}
		}
	}

	/**
	 * @param string[] $propertyNames
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheLoadArea(array $propertyNames, $cacheKey) {
		if ($propertyNames) {
			$cacheKey = $cacheKey . '::';
			foreach ($propertyNames as $propertyName) {
				/** @var string $propertyName */
				$this->cacheLoadProperty($propertyName, $cacheKey);
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheLoadProperty($propertyName, $cacheKey) {
		$cacheKey =  $cacheKey . $propertyName;
		/** @var string|bool $propertyValueSerialized */
		$propertyValueSerialized = df_cache()->load($cacheKey);
		if ($propertyValueSerialized) {
			/** @var mixed $propertyValue */
			/**
			 * Обратите внимание,
			 * что @see json_decode() в случае невозможности деколирования возвращает NULL,
			 * а @see unserialize в случае невозможности деколирования возвращает FALSE.
			 */
			$propertyValue =
				isset($this->_cachedPropertiesSimpleMap[$propertyName])
				? df_unserialize_simple($propertyValueSerialized)
				: df_ftn(df_unserialize($propertyValueSerialized))
			;
			if (!is_null($propertyValue)) {
				$this->_cachedPropertiesLoaded[$propertyName] = true;
				$this->$propertyName = $propertyValue;
			}
		}
	}

	/**
	 * @used-by _destruct()
	 * @return void
	 */
	private function cacheSave() {
		if ($this->isCacheEnabled()) {
			$this->cacheSaveBefore();
			$this->cacheSaveArea($this->cachedAllGlobal(), $this->cacheKeyGlobal());
			/**
			 * При вызове метода @uses Df_Core_Model::cacheKeyPerStore()
			 * может произойти исключительная ситуация в том случае,
			 * когда текущий магазин системы ещё не инициализирован
			 * (вызов Mage::app()->getStore() приводит к исключительной ситуации),
			 * поэтому вызываем @uses Df_Core_Model::cacheKeyPerStore()
			 * только если в этом методе есть реальная потребность,
			 * т.е. если класс действительно имеет свойства, подлежащие кэшированию в разрезе магазина,
			 * и если текущий магазин уже инициализирован.
			 */
			if ($this->cachedAllPerStore() && Df_Core_State::s()->isStoreInitialized()) {
				$this->cacheSaveArea($this->cachedAllPerStore(), $this->cacheKeyPerStore());
			}
		}
	}

	/**
	 * @buyer {buyer}
	 * @param string[] $propertyNames
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheSaveArea(array $propertyNames, $cacheKey) {
		if (!!$propertyNames) {
			$cacheKey = $cacheKey . '::';
			foreach ($propertyNames as $propertyName) {
				/** @var string $propertyName */
				if (
						isset($this->$propertyName)
					&&
						(
								/**
								 * Сохраняем в кэше только те свойства,
								 * которые либо еще не сохранены там,
								 * либо чьё значение изменилось после загрузки из кэша:
								 * @see Df_Core_Model::markCachedPropertyAsModified()
								 */
								!isset($this->_cachedPropertiesLoaded[$propertyName])
							||
								isset($this->_cachedPropertiesModified[$propertyName])
						)

				) {
					$this->cacheSaveProperty($propertyName, $cacheKey);
				}
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheSaveProperty($propertyName, $cacheKey) {
		$cacheKey = $cacheKey . $propertyName;
		/** @var mixed $propertyValue */
		$propertyValue = $this->$propertyName;
		/** @var string|bool $propertyValueSerialized */
		$propertyValueSerialized =
			isset($this->_cachedPropertiesSimpleMap[$propertyName])
			? df_serialize_simple($propertyValue)
			: df_serialize($propertyValue)
		;
		if ($propertyValueSerialized) {
			df_cache()->save(
				$data = $propertyValueSerialized
				,$id = $cacheKey
				,$tags = df_array($this->cacheTags())
				,$lifeTime = $this->cacheLifetime()
			);
		}
	}

	/** @return string */
	private function getAutoGeneratedId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_uid();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Mixin */
	private function getMixin() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Mixin $result */
			$result = $this->_getData(self::P__MIXIN);
			if (!$result) {
				$result = $this->createMixin();
			}
			else {
				df_assert($result instanceof Df_Core_Model_Mixin);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by isDestructableSingleton()
	 * @used-by isCacheEnabled()
	 * @return bool
	 */
	private function hasPropertiesToCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->cachedAll();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_cachedPropertiesSimpleMap = array_flip($this->cachedAllSimple());
		if ($this->_data) {
			$this->_checkForNullArray($this->_data);
			/**
			 * Обратите внимание, что @see Mage::getModel()
			 * почему-то не устанавливает поле @see _hasDataChanges в true.
			 */
			$this->setDataChanges(true);
			/**
			 * Фильтры мы здесь пока применять не можем,
			 * потому что они ещё не инициализированы
			 * (фильтры будут инициализированы потомками
			 * уже после вызова @see Df_Core_Model::_construct()).
			 * Вместо этого применяем фильтры для начальных данных
			 * в методе @see Df_Core_Model::_prop(),
			 * а для дополнительных данных — в методе @see Df_Core_Model::setData().
			 */
		}
		parent::_construct();
		$this->cacheLoad();
		if ($this->isDestructableSingleton()) {
			df_destructable_singleton($this);
		}
		// Нельзя вызывать здесь $this->_prop(self::P__MIXIN, Df_Core_Model_Mixin::class, false);
		// потому что библиотеки Российской сборки ещё не инициализированы
		// (по какой причине — не разбирался).
	}

	const P__MIXIN = 'mixin';

	/** @var string  */
	protected $_eventObject = 'object';
	/** @var string  */
	protected $_eventPrefix = 'df_core_abstract';

	/** @var array(string => bool) */
	private $_cachedPropertiesLoaded = array();
	/** @var array(string => bool) */
	private $_cachedPropertiesModified = array();
	/** @var array(string => null) */
	private $_cachedPropertiesSimpleMap;
	/** @var array(string => bool) */
	private $_disabledMixins = array();
	/** @var array(string => Zend_Filter_Interface[]) */
	private $_filters = array();
	/** @var array(string => Zend_Validate_Interface[]) */
	private $_validators = array();
	/** @var array(string => bool) */
	private $_valueWasNullBeforeFilters = array();

	/**
	 * @param string $class
	 * @param string|string[] $functions
	 * @return string[]
	 */
	protected static function m($class, $functions) {
		df_assert($functions);
		/** @var string[] $result */
		$result = array();
		if (!is_array($functions)) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$functions = df_tail($arguments);
		}
		foreach ($functions as $function) {
			/** @var string $function */
			$result[]= $class . '::' . $function;
		}
		return $result;
	}
}