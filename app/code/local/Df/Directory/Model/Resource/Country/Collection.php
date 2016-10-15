<?php
class Df_Directory_Model_Resource_Country_Collection
	extends Mage_Directory_Model_Mysql4_Country_Collection {
	/**
	 * Обратите внимание, что родительский метод @see getItemById() реализован очень странно
	 * (и, видимо, ошибочно):
			 public function getItemById($countryId) {
				 foreach ($this->_items as $country) {
					 if ($country->getCountryId() == $countryId) {
						 return $country;
					 }
				 }
				 return Mage::getResourceModel('directory/country');
			 }
	 * 1) он зачем-то делает цикл про элементам коллекции
	 * 2) в случае отсутствия искомого элемента
	 * он почему-то возвращает не объект класса @see Mage_Directory_Model_Country,
	 * а ресурсную модель @see Mage_Directory_Model_Mysql4_Country
	 * Причём данный код как в Magento CE 1.4.0.1, так и в Magento CE 1.9.1.0.
	 *
	 * У меня такое мнение, что данный ошибочный метод просто нигде не используется,
	 * (так и не нашёл, чтобы Magento CE/EE его где-то использовала),
	 * поэтому его ошибочность до сих пор не вскрылась.
	 *
	 * Перекрываем метод для нормального поиска страны
	 * по стандартному 2-буквенному коду по стандарту ISO 3166-1 alpha-2
	 * реализуем наш метод.
	 * @override
	 * @param string $idValue
	 * @return Df_Directory_Model_Country|null
	 */
	public function getItemById($idValue) {
		/** по сути, берём реализацию из @see Varien_Data_Collection::getItemById() */
		$this->load();
		return dfa($this->_items, $idValue);
	}

	/**
	 * @used-by df_countries_ctn()
	 * Возвращает массив,
	 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
	 * а значениями — названия стран для заданной локали (или системной локали по умолчанию).
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'AU' => 'Австралия'
	 		,'AT' => 'Австрия'
		)
	 * @param Mage_Core_Model_Locale|string|null $locale [optional]
	 * @return array(string => string)
	 */
	public function getMapFromCodeToName($locale = null) {
		/** @var string $localeCode */
		$localeCode = df_locale($locale);
		if (!isset($this->{__METHOD__}[$localeCode])) {
			$this->{__METHOD__}[$localeCode] =
				df_options_to_map($this->toOptionArrayRmCached(
					// Обратите внимание, что в качестве $groupAndOrder надо передать значение «false»,
					// чтобы в результате опции не были сгруппированы.
					$emptyLabel = false, $localeCode, $groupAndOrder = false
				))
			;
		}
		return $this->{__METHOD__}[$localeCode];
	}

	/**
	 * @used-by df_countries_ctn_uc()
	 * Возвращает массив,
	 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
	 * а значениями — названия стран в верхнем регистре для заданной локали
	 * (или системной локали по умолчанию).
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'AU' => 'АВСТРАЛИЯ'
	 		,'AT' => 'АВСТРИЯ'
		)
	 * @param Mage_Core_Model_Locale|string|null $locale [optional]
	 * @return array(string => string)
	 */
	public function getMapFromCodeToNameUc($locale = null) {
		/** @var string $localeCode */
		$localeCode = df_locale($locale);
		if (!isset($this->{__METHOD__}[$localeCode])) {
			$this->{__METHOD__}[$localeCode] =
				df_strtoupper($this->getMapFromCodeToName($localeCode))
			;
		}
		return $this->{__METHOD__}[$localeCode];
	}

	/**
	 * @used-by df_countries_ntc()
	 * Возвращает массив,
	 * в котором ключами являются
	 * названия стран для заданной локали (или системной локали по умолчанию)
	 * а значениями — 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'Австралия' => 'AU'
	 		,'Австрия' => 'AT'
		)
	 * @param Mage_Core_Model_Locale|string|null $locale [optional]
	 * @return array(string => string)
	 */
	public function getMapFromNameToCode($locale = null) {
		/** @var string $localeCode */
		$localeCode = df_locale($locale);
		if (!isset($this->{__METHOD__}[$localeCode])) {
			$this->{__METHOD__}[$localeCode] = array_flip($this->getMapFromCodeToName($localeCode));
		}
		return $this->{__METHOD__}[$localeCode];
	}

	/**
	 * @used-by df_countries_ntc_uc()
	 * Возвращает массив,
	 * в котором ключами являются
	 * названия стран в верхнем регистре для заданной локали (или системной локали по умолчанию)
	 * а значениями — 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'АВСТРАЛИЯ' => 'AU'
	 		,'АВСТРИЯ' => 'AT'
		)
	 * @param Mage_Core_Model_Locale|string|null $locale [optional]
	 * @return array(string => string)
	 */
	public function getMapFromNameToCodeUc($locale = null) {
		/** @var string $localeCode */
		$localeCode = df_locale($locale);
		if (!isset($this->{__METHOD__}[$localeCode])) {
			$this->{__METHOD__}[$localeCode] = array_flip($this->getMapFromCodeToNameUc($localeCode));
		}
		return $this->{__METHOD__}[$localeCode];
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Country
	 */
	public function getResource() {return Df_Directory_Model_Resource_Country::s();}

	/**
	 * @param string $iso2
	 * @return bool
	 */
	public function isIso2CodePresent($iso2) {
		$this->load();
		return isset($this->_items[$iso2]);
	}

	/**
	 * Цель перекрытия —
	 * добавление возможности ставить популярные страны выше остальных в списке стран.
	 * $emptyLabel задаёт заголовок пустой опции.
	 * Если в качестве $emptyLabel передать false, то результат не будет содержать пустой опции.
	 * @override
	 * @see Mage_Directory_Model_Mysql4_Country_Collection::toOptionArray()
	 * @used-by Mage_Adminhtml_Model_System_Config_Source_Country::toOptionArray()
	 *
	 * При инициализации поля «Перечень стран, куда разрешена доставка данным способом»:
	 * @see Mage_Adminhtml_Block_System_Config_Form::initFields():
		 $optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
		 (...)
		 $field->setValues($optionArray);
	 * Элемент управления $field имеет тип @see Varien_Data_Form_Element_Multiselect
	 *
	 * @param string|bool $emptyLabel [optional]
	 * @return array(array(string => string))
	 */
	public function toOptionArray($emptyLabel = ' ') {
		return $this->toOptionArrayRm($emptyLabel, null, $groupAndOrder = true);
	}

	/**
	 * 2015-02-04
	 * Этот метод реализует функциональность,
	 * которая в похожем (но не полностью!) виде уже присутствует в Magento CE / EE:
	 *
	 * 1) @see Mage_Checkout_Block_Onepage_Abstract::getCountryOptions()
	 * Этот метод присутствует как в Magento CE 1.4.0.1, так и в Magento CE 1.9.1.0.
	 * Однако этот метод возвращает результат только для $emptyLabel = ' ' и локали по умолчанию,
	 * что нас не устраивает: нам важна возможность явно задавать локаль,
	 * потому что иногда нам нужны результаты конкретно на русском языке
	 * вне зависимости от текущей локали магазина
	 * (например, для интеграции с внешними российскими системами,
	 * когда названия стран должны быть конкретно на русском языке).
	 *
	 * 2) @see Mage_Catalog_Model_Product_Attribute_Source_Countryofmanufacture::getAllOptions()
	 * Этот метод идентичен методу пункта №1
	 * (видимо, продублирован программистами Magento CE/EE по неряшливости)
	 * и обладает указанным в пункте №1 недостатком.
	 * Более того, он отсутствует в Magento CE 1.4.0.1.
	 *
	 * 3) @see Mage_Directory_Block_Data::getCountryHtmlSelect()
	 * Этот метод  возвращает разметку HTML, а не массив опций.
	 * Более того, он обладает указанным в пункте №1 недостатком.
	 *
	 * 4) @see Mage_XmlConnect_Block_Checkout_Address_Form::_getCountryOptions()
	 * Этот метод идентичен методу пункта №1
	 * (видимо, продублирован программистами Magento CE/EE по неряшливости)
	 * и обладает указанным в пункте №1 недостатком.
	 * Более того, он отсутствует в Magento CE 1.4.0.1.
	 * Более того, его область видимости — «protected».
	 * Более того, в Magento CE 1.9.1.0 он помечен как «deprecated».
	 *
	 * 5) @see Mage_XmlConnect_Model_Simplexml_Form_Element_CountryListSelect::_getCountryOptions()
	 * Этот метод идентичен методу пункта №1
	 * (видимо, продублирован программистами Magento CE/EE по неряшливости)
	 * и обладает указанным в пункте №1 недостатком.
	 * Более того, он отсутствует в Magento CE 1.4.0.1.
	 * Более того, его область видимости — «protected».
	 *
	 * Наш метод отличается от перечисленных выше следующим:
	 * 1) Он всегда доступен в Российской сборке Magento
	 * (не зависит от наличия той или иной функциональности в той конкретной версии Magento CE/EE,
	 * на базе которой работает Российская сборка Magento).
	 * 2) Он поддерживает параметр $locale
	 * 3) Он кэширует результаты при нестандартных значениях $emptyLabel.
	 * 4) Он реализует дополнительный быстрый кэш в оперативной памяти:
			$this->{__METHOD__}[$emptyLabel][$localeCode]
	 *
	 * $emptyLabel задаёт заголовок пустой опции.
	 * Если в качестве $emptyLabel передать false, то результат не будет содержать пустой опции.
	 *
	 * @used-by df_countries_options()
	 * @param string|bool $emptyLabel [optional]
	 * @param Mage_Core_Model_Locale|string|null $locale [optional]
	 * @param bool $groupAndOrder [optional]
	 * @return array(array(string => string))
	 */
	public function toOptionArrayRmCached($emptyLabel = ' ', $locale = null, $groupAndOrder = true) {
		/** @var string $localeCode */
		$localeCode = df_locale($locale);
		if (!isset($this->{__METHOD__}[$emptyLabel][$localeCode][$groupAndOrder])) {
			/** @var array(array(string => string)) $result */
			$result = null;
			/** @var string $cacheKey */
			/**
			 * Это — стандартный идентификатор кэша для опций-стран:
			 * @see Mage_Checkout_Block_Onepage_Abstract::getCountryOptions()
			 * @see Mage_Catalog_Model_Product_Attribute_Source_Countryofmanufacture::getAllOptions()
			 * @see Mage_Directory_Block_Data::getCountryHtmlSelect()
			 * @see Mage_XmlConnect_Block_Checkout_Address_Form::_getCountryOptions()
			 * @see Mage_XmlConnect_Model_Simplexml_Form_Element_CountryListSelect::_getCountryOptions()
			 */
			$cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . df_store()->getCode();
			// Если в наш метод переданы нестандартные значения параметров $emptyLabel и $locale,
			// то мы добавим в конец данного идентификатора идентификаторы значений этих параметров.
			if (' ' !== $emptyLabel) {
				$cacheKey = implode('_', array(
					$cacheKey, (false === $emptyLabel ? 'false' : $emptyLabel), df_locale($locale)
				));
			}
			/** @var bool $canUseCache */
			$canUseCache = Mage::app()->useCache('config');
			if ($canUseCache && $cache = Mage::app()->loadCache($cacheKey)) {
				$result = @unserialize($cache);
			}
			if (!is_array($result)) {
				$result = $this->toOptionArrayRm($emptyLabel, $locale, $groupAndOrder);
				if ($canUseCache) {
					Mage::app()->saveCache(serialize($result), $cacheKey, array('config'));
				}
			}
			$this->{__METHOD__}[$emptyLabel][$localeCode][$groupAndOrder] = $result;
		}
		return $this->{__METHOD__}[$emptyLabel][$localeCode][$groupAndOrder];
	}

	/**
	 * @param string|null $localeCode [optional]
	 * @return array(string => string)
	 */
	private function getMapFromIso2CodeToLocalizedName($localeCode = null) {
		if (!$localeCode) {
			$localeCode = df_locale();
		}
		if (!isset($this->{__METHOD__}[$localeCode])) {
			/** @var array(string => string) $result */
			$result = array();
			foreach ($this as $country) {
				/** @var Df_Directory_Model_Country $country */
				/** @var string $localizedName */
				$localizedName = null;
				/**
				 * При наличии в названии страны апострофа (например, «Кот-д'Ивуар»)
				 * в Magento CE 1.9.1.0 в методе @see Zend_Locale_Data::_findRoute()
				 * происходит сбой «SimpleXMLElement::xpath(): Invalid predicate»
				 * при вызове @see SimpleXMLElement::xpath()
				 * для выражения вида
				 * /ldml/localeDisplayNames/territories/territory[@type='Кот-д'Ивуар'].
				 * Поэтому с апострофами надо что-то делать.
				 */
				if (!df_contains($country->getName(), "'")) {
					$localizedName = Mage::app()->getLocale()->getLocale()->getTranslation(
						$country->getName(), 'country', $localeCode
					);
				}
				if (!$localizedName) {
					$localizedName = $country->getName();
				}
				/** @var Mage_Directory_Model_Country $country */
				$result[$country->getIso2Code()] = $localizedName;
			}
			$this->{__METHOD__}[$localeCode] = $result;
		}
		return $this->{__METHOD__}[$localeCode];
	}

	/**
	 * $emptyLabel задаёт заголовок пустой опции.
	 * Если в качестве $emptyLabel передать false, то результат не будет содержать пустой опции.
	 * @used-by toOptionArray()
	 * @used-by toOptionArrayRmCached()
	 * @param string|bool $emptyLabel [optional]
	 * @param Mage_Core_Model_Locale|string|null $locale [optional]
	 * @param bool $groupAndOrder [optional]
	 * @return array(array(string => string))
	 */
	private function toOptionArrayRm($emptyLabel = ' ', $locale = null, $groupAndOrder = true) {
		/**
		 * @var array(array(string => string)) $options
		 * Элемент массива:
		  	array(
				'value' => $country->getData('country_id')
				'label' => $country->getData('name')
				'title' => $country->getData('iso2_code')
		  	)
		 * Обратите внимание, что в таблице «directory_country» базе данных Magento
		 * значения колонок «country_id» и «iso2_code»  идентичны.
		 *
		 * 2015-08-08
		 * Обратите внимание, что массив $options вовсе не обязан содержать все страны.
		 * В частности, сюда могли мы попасть из модуля Mage_Paypal,
		 * и тогда массив $options будет содержать только страны, поддерживаемые Paypal.
		 */
		$options = $this->_toOptionArray(
			$valueField = 'country_id'
			, $labelField = 'name'
			, $additional = array('title' => 'iso2_code')
		);
		/** @var array(string => string) $mapFromLocalizedNameToIso2 */
		$mapFromLocalizedNameToIso2 = array();
		/** @var array(string => string) $mapFromIso2ToLocalizedName */
		$mapFromIso2ToLocalizedName = array();
		/** @var Zend_Locale $zendLocale */
		$zendLocale = new Zend_Locale(df_locale($locale));
		foreach ($options as $option) {
			/** @var @var array(string => string) $option */
			/** @var string $iso2 */
			$iso2 = $option['value'];
			/** @var string|null $name */
			/** по аналогии с @see Mage_Core_Model_Locale::getCountryTranslation() */
			$name = Zend_Locale::getTranslation($iso2, 'country', $zendLocale);
			if ($name) {
				$mapFromLocalizedNameToIso2[$name] = $iso2;
				$mapFromIso2ToLocalizedName[$iso2] = $name;
			}
		}
		/** @var Mage_Core_Helper_String $stringHelper */
		$stringHelper = Mage::helper('core/string');
		/**
		 * Метод @uses Mage_Core_Helper_String::ksortMultibyte()
		 * отсутствует, в частности, в Magento CE 1.4.0.1.
		 */
		if (method_exists($stringHelper, 'ksortMultibyte')) {
			$stringHelper->ksortMultibyte($mapFromLocalizedNameToIso2);
		}
		else {
			/**
			 * В Magento CE 1.4.0.1 родительский метод
			 * @see Mage_Directory_Model_Mysql4_Country_Collection::toOptionArray()
			 * использует именно эту функцию.
			 */
			ksort($mapFromLocalizedNameToIso2);
		}
		// НАЧАЛО ЗАПЛАТКИ
		/** @var Df_Directory_Settings_Countries_Popular $settings */
		$settings = Df_Directory_Settings_Countries_Popular::s();
		/** @var array(string => string) $popularMapFromIso2ToLocalizedName */
		if ($groupAndOrder && $settings->isEnabled()) {
			$popularMapFromIso2ToLocalizedName =
				dfa_select_ordered($mapFromIso2ToLocalizedName, $settings->iso2Codes())
			;
			/**
			 * 2015-08-08
			 * Обратите внимание, что не все популярные страны могут быть допустимыми в конкретной ситуации.
			 * В частности, сюда могли мы попасть из модуля Mage_Paypal,
			 * и тогда массивы $options и $mapFromIso2ToLocalizedName
			 * будут содержать только страны, поддерживаемые Paypal.
			 * В такой ситуации для неподдерживаемых страны
			 * в массиве $popularMapFromIso2ToLocalizedName будут элемементы типа 'AM' => null,
			 * и мы теперь удаляем их их массива:
			 * во-первых, чтобы такие страны были недоступны для выбора ниже,
			 * во-вторых,  чтобы дальнейший вызов @see array_flip не давал сбой.
			 */
			$popularMapFromIso2ToLocalizedName = array_filter($popularMapFromIso2ToLocalizedName);
			if (!$settings->needDuplicate()) {
				$mapFromLocalizedNameToIso2 = array_diff_key(
					$mapFromLocalizedNameToIso2, array_flip($popularMapFromIso2ToLocalizedName)
				);
			}
		}
		// КОНЕЦ ЗАПЛАТКИ
		/** @var array(array(string => string)) $result */
		$result = df_map_to_options_reverse($mapFromLocalizedNameToIso2);
		// НАЧАЛО ЗАПЛАТКИ
		/**
		 * 2015-08-08
		 * Массив $popularMapFromIso2ToLocalizedName может быть пустым,
		 * потому что все популярные страны могут оказаться недопустимыми в текущей ситуации:
		 * например, в контексте работы модуля Mage_Paypal,
		 * когда коллекция стран содержит только страны, разрешённые PayPal в качестве страны продавца.
		 */
		if ($groupAndOrder && $settings->isEnabled() && $popularMapFromIso2ToLocalizedName) {
			$result = array(
				array(
					'label' => $settings->labelPopular()
					, 'value' => df_map_to_options($popularMapFromIso2ToLocalizedName)
				)
				,array('label' => $settings->labelAll(), 'value' => $result)
			);
		}
		// КОНЕЦ ЗАПЛАТКИ
		if ($result && (false !== $emptyLabel)) {
			array_unshift($result, df_option('', $emptyLabel));
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Directory_Model_Country::class;}

	/**
	 * @used-by df_countries()
	 * @used-by Df_Directory_Model_Country::cs()
	 * @return Df_Directory_Model_Resource_Country_Collection
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}