<?php
/**
 * @param bool $allowedOnly [optional]
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return Df_Directory_Model_Resource_Country_Collection
 */
function rm_countries($allowedOnly = false, $store = null) {
	return
		!$allowedOnly
		? Df_Directory_Model_Resource_Country_Collection::s()
		: rm_countries_allowed($store)
	;
}

/**
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return Df_Directory_Model_Resource_Country_Collection
 */
function rm_countries_allowed($store = null) {
	/** @var array(int => Df_Directory_Model_Resource_Country_Collection) $cache */
	static $cache;
	/** @var int $storeId */
	$storeId = rm_store_id($store);
	if (!isset($cache[$storeId])) {
		$cache[$storeId] = Df_Directory_Model_Country::c()->loadByStore($storeId);
	}
	return $cache[$storeId];
}

/**
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
function rm_countries_ctn($locale = null) {return rm_countries()->getMapFromCodeToName($locale);}

/**
 * @uses rm_countries_ctn()
 * @return array(string => string)
 */
function rm_countries_ctn_ru() {return rm_countries_ctn('ru_RU');}

/**
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
function rm_countries_ctn_uc($locale = null) {return rm_countries()->getMapFromCodeToNameUc($locale);}

/**
 * @uses rm_countries_ctn_uc()
 * @return array(string => string)
 */
function rm_countries_ctn_uc_ru() {return rm_countries_ctn_uc('ru_RU');}

/**
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
function rm_countries_ntc($locale = null) {return rm_countries()->getMapFromNameToCode($locale);}

/**
 * @uses rm_countries_ntc()
 * @return array(string => string)
 */
function rm_countries_ntc_ru() {return rm_countries_ntc('ru_RU');}

/**
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
function rm_countries_ntc_uc($locale = null) {return rm_countries()->getMapFromNameToCodeUc($locale);}

/**
 * @uses rm_countries_ntc_uc()
 * @return array(string => string)
 */
function rm_countries_ntc_uc_ru() {return rm_countries_ntc_uc('ru_RU');}

/**
 * $emptyLabel задаёт заголовок пустой опции.
 * Если в качестве $emptyLabel передать false, то результат не будет содержать пустой опции.
 * @param string|bool $emptyLabel [optional]
 * @param Mage_Core_Model_Locale|string|null $locale [optional]
 * @return array(array(string => string))
 */
function rm_countries_options($emptyLabel = ' ', $locale = null) {
	return rm_countries()->toOptionArrayRmCached($emptyLabel, $locale, $groupAndOrder = true);
}

/**
 * Возвращает страну по её 2-буквенному коду по стандарту ISO 3166-1 alpha-2.
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * @param string $iso2
 * @param bool $throw [optional]
 * @return Df_Directory_Model_Country|null
 */
function rm_country($iso2, $throw = true) {
	/** @var array(string => Df_Directory_Model_Country|RM_NULL) */
	static $cache;
	if (!isset($cache[$iso2])) {
		/** @var Df_Directory_Model_Country|null $result */
		$result = !df_check_iso2($iso2) ? null : rm_countries()->getItemById($iso2);
		if ($result) {
			df_assert($result instanceof Df_Directory_Model_Country);
		}
		else if ($throw) {
			df_error('Не могу найти страну по 2-буквенному коду «%s».', $iso2);
		}
		$cache[$iso2] = rm_n_set($result);
	}
	return rm_n_get($cache[$iso2]);
}

/**
 * Возвращает название страны для заданной локали (или системной локали по умолчанию)
 * по 2-буквенному коду по стандарту ISO 3166-1 alpha-2.
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * @param string $iso2
 * @param Mage_Core_Model_Locale|string|null $locale [optional]
 * @return string
 */
function rm_country_ctn($iso2, $locale = null) {
	df_param_iso2($iso2, 0);
	/** @var string $result */
	$result = dfa(rm_countries_ctn($locale), $iso2);
	if (!$result) {
		df_error(
			'Система не смогла узнать название страны с кодом «%s» для локали «%s».'
			, $iso2
			, df_locale($locale)
		);
	}
	return $result;
}

/**
 * @uses rm_country_ctn()
 * @param string $iso2
 * @return string
 */
function rm_country_ctn_ru($iso2) {return rm_country_ctn($iso2, 'ru_RU');}

/**
 * Возвращает 2-буквенный код страны по стандарту ISO 3166-1 alpha-2
 * по названию страны для заданной локали (или системной локали по умолчанию)
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * @param string $name
 * @param Mage_Core_Model_Locale|string|null $locale [optional]
 * @return string|null
 */
function rm_country_ntc($name, $locale = null) {
	df_param_string_not_empty($name, 0);
	return dfa(rm_countries_ntc($locale), mb_strtoupper(df_trim($name)));
}

/**
 * @uses rm_country_ntc()
 * @param string $name
 * @return string|null
 */
function rm_country_ntc_ru($name) {return rm_country_ntc($name, 'ru_RU');}

