<?php
class Df_Core_Model_Cache extends Df_Core_Model {
	/**
	 * @used-by df_eav_reset()
	 * @return Df_Core_Model_Cache
	 */
	public function clean() {
		$this->getCache()->clean($this->getTags());
		/**
		 * 2015-08-10
		 * Централизованный сброс кэша оперативной памяти.
		 * Это особенно важно в сценарии @used-by df_eav_reset()
		 */
		$this->ramReset();
		return $this;
	}

	/** @return bool */
	public function isEnabled() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание,
			 * что если указать несуществующий тип кэша, то кэширование работать не будет.
			 * В то же время, если указать в качестве типа значение «null»,
			 * то кэширование будет работать всегда,
			 * но в этом случае администратор не сможет удалить отдельно данный вид кэша
			 * (сможет удалить только кэш целиком).
			 */
			$this->{__METHOD__} = !$this->getType() || Mage::app()->useCache($this->getType());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @return string|bool
	 */
	public function loadData($key) {return !$this->isEnabled() ? false : $this->getCache()->load($key);}

	/**
	 * Функции @see json_encode() / @see json_decode() работают быстрее,
	 * чем @see serialize / @see unserialize(),
	 * поэтому для простых массивов (массивов, не содержащих объекты),
	 * используйте методы @see saveDataArray() / @see loadDataArray()
	 * вместо @see saveDataComplex() / @see loadDataComplex().
	 * http://stackoverflow.com/a/7723730
	 * http://stackoverflow.com/a/804053
	 * @param string $key
	 * @return mixed[]|bool
	 */
	public function loadDataArray($key) {
		/** @var mixed[]|bool $result */
		$result = false;
		if ($this->isEnabled()) {
			/** @var string|bool $serialized */
			$serialized = $this->loadData($key);
			if (false !== $serialized) {
				/**
				 * @see df_json_decode() использует json_decode при наличии расширения PHP JSON
				 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
				 * @see df_json_decode
				 * http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
				 * Обратите внимание,
				 * что расширение PHP JSON не входит в системные требования Magento.
				 * http://www.magentocommerce.com/system-requirements
				 * Поэтому использование @see df_json_decode выглядит более правильным,
				 * чем @see json_decode().
				 *
				 * Обратите внимание, что при использовании @see json_decode() напрямую
				 * параметр $assoc = true надо указывать обязательно,
				 * иначе @see json_decode() может вернуть объект даже в том случае,
				 * когда посредством @see json_encode() был кодирован массив.
				 *
				 * При использовании @see df_json_decode()
				 * второй параметр $objectDecodeType имеет значение Zend_Json::TYPE_ARRAY по умолчанию,
				 * поэтому его можно не указывать.
				 *
				 * $result = df_json_decode($serialized);
				 *
				 * P.S. Оно, конечно, правильнее, но @uses json_decode() работает заметно быстрее,
				 * чем обёртка @see df_json_decode()
				 */
				$result = df_unserialize_simple($serialized);
				if (!is_array($result)) {
					$result = false;
				}
			}
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @return mixed|bool
	 */
	public function loadDataComplex($key) {
		/** @var mixed|bool $result */
		$result = false;
		if ($this->isEnabled()) {
			/** @var string|bool $serialized */
			$serialized = $this->loadData($key);
			if (false !== $serialized) {
				$result = df_unserialize($serialized);
			}
		}
		return $result;
	}

	/**
	 * 2015-08-11
	 * @used-by p()
	 * @param string $key
	 * @param bool $complex [optional]
	 * @return mixed|bool
	 */
	public function loadDataGeneric($key, $complex = false) {
		return $complex ? $this->loadDataComplex($key) : $this->loadDataArray($key);
	}

	/**
	 * 2015-08-10
	 * Этот метод значительно упрощает двуступенчатое кэширование.
	 * @used-by df_eav_cache()
	 * @param object|null $object
	 * @param string $function
	 * @param string|string[]|null|array(string => mixed) $params [optional]
	 * @param bool $complex [optional]
	 * @param bool $ramOnly [optional]
	 * @return mixed|false
	 */
	public function p($object, $function, $params = null, $complex = false, $ramOnly = false) {
		/** @var bool $paramsIsArray */
		$paramsIsArray = is_array($params);
		/** @var bool $paramsIsAssoc */
		$paramsIsAssoc = $paramsIsArray && df_is_assoc($params);
		/** @var string|string[]|null $cacheKeyParams */
		$cacheKeyParams = !$paramsIsAssoc ? $params : array_keys($params);
		/** @var string $key */
		$key = $this->makeKey($object ? array($object, $function) : $function, $cacheKeyParams);
		/** @var mixed|false $result */
		$result = $this->ramGet($key);
		if (false === $result) {
			if (!$ramOnly) {
				$result = $this->loadDataGeneric($key, $complex);
			}
			if (false === $result) {
				$params = is_null($params) ? array() : (!$paramsIsArray ? array($params) : $params);
				$function .= '_';
				/** @var callable $callback */
				$callback = $object ? array($object, $function) : $function;
				$result = call_user_func_array($callback, $params);
				if (!$ramOnly) {
					$this->saveDataGeneric($key, $result, $complex);
				}
			}
			$this->ramSet($key, $result);
		}
		return $result;
	}

	/**
	 * @param string|array(object, string) $method
	 * @param string|string[]|null $params [optional]
	 * @return string
	 */
	public function makeKey($method, $params = null) {
		/**
		 * Иногда первым параметром вместо __METHOD__ передаётся array($this, __FUNCTION__).
		 * Это позволяет сохранить уникальность ключа,
		 * когда вызов makeKey() производится в родительском классе.
		 * Ведь в таком случае __METHOD__ не будет уникальным значением,
		 * ибо будет содержать имя родительского класса, а не класса-потомка.
		 * А вот значение implode('::', array(get_class($this), __FUNCTION__)) останется уникальным,
		 * ибо будет содержать название класса-потомка, а не родителя.
		 */
		if (is_array($method)) {
			/** @var object $object */
			$object = df_first($method);
			df_assert(is_object($object));
			/** @var string $function */
			$function = df_last($method);
			$method = implode('::', array(get_class($object), $function));
		}
		/** @var string[] $keyParts */
		$keyParts = [];
		if ($this->getType()) {
			$keyParts[]= $this->getType();
		}
		$keyParts[]= $method;
		$keyParts[]= df_store()->getCode();
		if ($params) {
			if (!is_array($params)) {
				$keyParts[]= $params;
			}
			else {
				$keyParts = array_merge($keyParts, $params);
			}
		}
		/**
		 * Обратите внимание,
		 * что ключ кэширования не должен содержать русские буквы и некоторые другие символы,
		 * потому что когда кэш хранится в файлах,
		 * то русские буквы и недопустимые символы будут заменены на символ «_»,
		 * и имя файла будет выглядеть как «mage---b26_DF_LOCALIZATION_MODEL_MORPHER________».
		 * Чтобы избавиться от русских букв и других недопустимых символов
		 * при сохранении уникальности ключа, используем функцию @гыуы md5().
		 */
		return md5(implode('::', $keyParts));
	}

	/**
	 * 2015-08-10
	 * В случае отсутствия значения в кэше возвращаем не null, а false
	 * ради согласованности с долгосрочным кэшем.
	 * @param string $key
	 * @return mixed|bool
	 */
	public function ramGet($key) {return dfa($this->_ram, $key, false);}

	/**
	 * 2015-08-10
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function ramSet($key, $value) {$this->_ram[$key] = $value;}

	/**
	 * @param string $key
	 * @return void
	 */
	public function removeData($key) {!$this->isEnabled() ? false : $this->getCache()->remove($key);}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function saveData($key, $value) {
		if ($this->isEnabled()) {
			$this->getCache()->save($value, $key, $this->getTags(), $this->getLifetime());
		}
	}

	/**
	 * Функции @see json_encode() / @see json_decode() работают быстрее,
	 * чем @see serialize / @see unserialize(),
	 * поэтому для простых массивов (массивов, не содержащих объекты),
	 * используйте методы @see saveDataArray() / @see loadDataArray()
	 * вместо @see saveDataComplex() / @see loadDataComplex().
	 * http://stackoverflow.com/a/7723730
	 * http://stackoverflow.com/a/804053
	 * @param string $key
	 * @param mixed[] $value
	 * @return void
	 */
	public function saveDataArray($key, array $value) {
		if ($this->isEnabled()) {
			$this->saveData($key, df_serialize_simple($value));
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function saveDataComplex($key, $value) {
		if ($this->isEnabled()) {
			$this->saveData($key, df_serialize($value));
		}
	}

	/**
	 * @used-by p()
	 * @param string $key
	 * @param mixed $value
	 * @param bool $complex [optional]
	 * @return void
	 */
	public function saveDataGeneric($key, $value, $complex = false) {
		$complex ? $this->saveDataComplex($key, $value) : $this->saveDataArray($key, $value);
	}

	/** @return Mage_Core_Model_Cache */
	protected function getCache() {return df_cache();}

	/**
	 * 2015-12-09
	 * Обратите внимание,
	 * что мы намерернно используем @uses array_key_exists() вместо @see dfa()
	 * потому что в нашем случае null является полноценным значением и означает «кэшировать вечно»,
	 * в то время как значение по умолчанию — «кэшировать на время, заданное в настройках ядра».
	 * @return int|bool|null
	 */
	protected function getLifetime() {
		return array_key_exists(self::P__LIFETIME, $this->_data)
			? $this->_data[self::P__LIFETIME]
			: self::LIFETIME_STANDARD
		;
	}

	/** @return string[] */
	protected function getTags() {
		return $this->cfg(self::P__TAGS, $this->getType() ? array($this->getType()) : array());
	}

	/** @return string */
	protected function getType() {return $this[self::$P__TYPE];}

	/**
	 * 2015-08-10
	 * @return void
	 */
	protected function ramReset() {$this->_ram = [];}

	/**
	 * 2015-08-10
	 * Централизованный кэш в оперативной памяти.
	 * Централизация кэша позволяет нам централизованно его сбрасывать:
	 * в частности, в случае @see df_eav_reset()
	 * @used-by ramGet()
	 * @used-by ramReset()
	 * @used-by ramSet()
	 * @var array(string => mixed)
	 */
	private $_ram = [];

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__TAGS, DF_V_ARRAY)
			->_prop(self::$P__TYPE, DF_V_STRING)
		;
	}
	/**
	 * Zend Framework для обозначения безлимитного кэширования использует значение null:
	 * @see Zend_Cache_Backend_Interface::save()
	 *
	 * Обратите внимание (это лишь напоминание, к классу @see Df_Core_Model_Cache отношения не имеет),
	 * что при кэшировании блоков посредством встроенного в @see Mage_Core_Block_Abstract алгоритма
	 * значение null почему-то имеет прямо противоположное значение запрет на кэширование блока:
	 * @used-by Mage_Core_Block_Abstract::_loadCache().
	 */
	const LIFETIME_INFINITE = null;
	/**
	 * Значение «false» означает «использовать стандартную продолжительность кэширования».
	 *
	 * Стандартная продолжительность кэширования в Zend Framework составляет 1 час:
	 * @see Zend_Cache_Backend::$_directives
	 * Стандартная продолжительность кэширования в Magento составляет 2 часа:
	 * @see Mage_Core_Model_Cache::DEFAULT_LIFETIME
	 * Обратите внимание, что блоки по умолчанию вообще не кэшируются:
	 * @see Mage_Core_Block_Abstract::getCacheLifetime() по умолчанию возвращает null,
	 * что означает запрет на кэширование.
	 *
	 * Почему Magento кэширует данные (в данном случае — экранные блоки) не безлимитно, а на 2 часа?
	 * Потому что администратор может что-то поменять в интернет-магазине
	 * и забыть/полениться обновить кэш.
	 * Тогда если Magento бы кэшировала данные безлимитно,
	 * то витрина так и не обновится до тех пор,
	 * пока администратор не вспомнит/соблагоизволит обновить кэш.
	 * С настройками же по умолчанию Magento сама гарантированно обновит витрину магазина
	 * через 2 часа.
	 */
	const LIFETIME_STANDARD = false;
	const P__LIFETIME = 'lifetime';
	const P__TAGS = 'tags';
	/** @var string */
	private static $P__TYPE = 'type';

	/**
	 * @param string|null $type [optional]
	 * @param int|bool|null $lifetime [optional]
	 * @param string|string[]|null $tags [optional]
	 * @return Df_Core_Model_Cache
	 */
	public static function i(
		/**
		 * Обратите внимание,
		 * что если указать несуществующий тип кэша, то кэширование работать не будет.
		 * В то же время, если указать в качестве типа значение «null»,
		 * то кэширование будет работать всегда,
		 * но в этом случае администратор не сможет удалить отдельно данный вид кэша
		 * (сможет удалить только кэш целиком).
		 */
		$type = null
		/**
		 * Значение «false» означает «использовать стандартную продолжительность кэширования».
		 *
		 * Стандартная продолжительность кэширования в Zend Framework составляет 1 час:
		 * @see Zend_Cache_Backend::$_directives
		 * Стандартная продолжительность кэширования в Magento составляет 2 часа:
		 * @see Mage_Core_Model_Cache::DEFAULT_LIFETIME
		 * Обратите внимание, что блоки по умолчанию вообще не кэшируются:
		 * @see Mage_Core_Block_Abstract::getCacheLifetime() по умолчанию возвращает null,
		 * что означает запрет на кэширование.
		 *
		 * Почему Magento кэширует данные (в данном случае — экранные блоки) не безлимитно, а на 2 часа?
		 * Потому что администратор может что-то поменять в интернет-магазине
		 * и забыть/полениться обновить кэш.
		 * Тогда если Magento бы кэшировала данные безлимитно,
		 * то витрина так и не обновится до тех пор,
		 * пока администратор не вспомнит/соблагоизволит обновить кэш.
		 * С настройками же по умолчанию Magento сама гарантированно обновит витрину магазина
		 * через 2 часа.
		 */
		, $lifetime = self::LIFETIME_STANDARD
		, $tags = null
	) {
		/** @var bool $infinite */
		$infinite = (true === $lifetime);
		if ($infinite) {
			$lifetime = self::LIFETIME_INFINITE;
		}
		$tags =
			$tags
		 	? df_array($tags)
			: ($type ? array($type) : array())
		;
		return new self(array(
			self::$P__TYPE => $type, self::P__LIFETIME => $lifetime, self::P__TAGS => $tags
		));
	}
}