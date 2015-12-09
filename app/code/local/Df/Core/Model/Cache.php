<?php
class Df_Core_Model_Cache extends Df_Core_Model_Abstract {
	/** @return Df_Core_Model_Cache */
	public function clean() {
		$this->getCache()->clean($this->getTags());
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
	 * @link http://stackoverflow.com/a/7723730
	 * @link http://stackoverflow.com/a/804053
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
				 * @see Zend_Json::decode() использует json_decode при наличии расширения PHP JSON
				 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
				 * @see Zend_Json::decode
				 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
				 * Обратите внимание,
				 * что расширение PHP JSON не входит в системные требования Magento.
				 * @link http://www.magentocommerce.com/system-requirements
				 * Поэтому использование @see Zend_Json::decode выглядит более правильным,
				 * чем @see json_decode().
				 *
				 * Обратите внимание, что при использовании @see json_decode() напрямую
				 * параметр $assoc = true надо указывать обязательно,
				 * иначе @see json_decode() может вернуть объект даже в том случае,
				 * когда посредством @see json_encode() был кодирован массив.
				 *
				 * При использовании @see Zend_Json::decode()
				 * второй параметр $objectDecodeType имеет значение Zend_Json::TYPE_ARRAY по умолчанию,
				 * поэтому его можно не указывать.
				 *
				 * $result = Zend_Json::decode($serialized);
				 *
				 * P.S. Оно, конечно, правильнее, но @see json_decode() работает заметно быстрее,
				 * чем обёртка @see Zend_Json::decode()
				 */
				$result = rm_unserialize_simple($serialized);
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
				$result = rm_unserialize($serialized);
			}
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
			$object = rm_first($method);
			df_assert(is_object($object));
			/** @var string $function */
			$function = rm_last($method);
			$method = implode('::', array(get_class($object), $function));
		}
		df_param_string_not_empty($method, 0);
		/** @var string[] $keyParts */
		$keyParts = array();
		if ($this->getType()) {
			$keyParts[]= $this->getType();
		}
		$keyParts[]= $method;
		$keyParts[]= Mage::app()->getStore()->getCode();
		if ($params) {
			if (!is_array($params)) {
				$keyParts []= $params;
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
		 * при сохранении уникальности ключа, испольузем функцию @see md5().
		 */
		return md5(implode('::', $keyParts));
	}

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
	 * @link http://stackoverflow.com/a/7723730
	 * @link http://stackoverflow.com/a/804053
	 * @param string $key
	 * @param mixed[] $value
	 * @return void
	 */
	public function saveDataArray($key, array $value) {
		if ($this->isEnabled()) {
			/**
			 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
			 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
			 * @see Zend_Json::encode
			 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
			 * Обратите внимание,
			 * что расширение PHP JSON не входит в системные требования Magento.
			 * @link http://www.magentocommerce.com/system-requirements
			 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
			 *
			 * $this->saveData($key, Zend_Json::encode($value));
			 *
			 * P.S. Оно, конечно, правильнее, но @see json_encode() работает заметно быстрее,
			 * чем обёртка @see Zend_Json::encode()
			 */
			$this->saveData($key, rm_serialize_simple($value));
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function saveDataComplex($key, $value) {
		if ($this->isEnabled()) {
			$this->saveData($key, rm_serialize($value));
		}
	}

	/** @return Mage_Core_Model_Cache */
	protected function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getCacheInstance();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-09
	 * Обратите внимание,
	 * что мы намерернно используем @uses array_key_exists() вместо
	 * потому чтов нашем случае null является полноценным значением и означает «кэшировать вечно»,
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
	protected function getType() {return $this->cfg(self::P__TYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__TAGS, self::V_ARRAY)
			->_prop(self::P__TYPE, self::V_STRING)
		;
	}
	/**
	 * Zend Framework для обозначения безличитного кэширования использует значение null:
	 * @see Zend_Cache_Backend_Interface::save()
	 *
	 * Обратите внимание (это лишь напоминание, к классу @see Df_Core_Model_Cache отношения не имеет),
	 * что при кэшировании блоков посредством встроенного в @see Mage_Core_Block_Abstract алгоритма
	 * значение null почему-то имеет прямо противоположное значение запрет на кэширование блока:
	 * @see Mage_Core_Block_Abstract::_loadCache().
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
	const P__TYPE = 'type';

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
		if (!$tags) {
			$tags = $type ? array($type) : array();
		}
		else if (!is_array($tags)) {
			$tags = array($tags);
		}
		return new self(array(
			self::P__TYPE => $type, self::P__LIFETIME => $lifetime, self::P__TAGS => $tags
		));
	}
}