<?php
/**
 * @param Mage_Core_Model_Locale|Zend_Locale|string|null $locale [optional]
 * @return string
 * @throws Df_Core_Exception
 */
function df_locale($locale = null) {
	/** @var string $result */
	if (!$locale) {
		$result = Mage::app()->getLocale()->getLocaleCode();
	}
	else if (is_string($locale)) {
		$result = $locale;
	}
	else if ($locale instanceof Mage_Core_Model_Locale) {
		$result = $locale->getLocaleCode();
	}
	else if ($locale instanceof Zend_Locale) {
		/** По примеру @see Mage_Core_Model_Locale::getLocale() */
		$result = $locale->__toString();
	}
	else {
		df_error(
			'Функция df_locale получила аргумент недопустимого типа «{type}».'
			."\nАргумент функции df_locale должен иметь один из следующих типов: {allowedTypes}."
			,array(
				'{type}' => df_string_debug($locale)
				,'{allowedTypes}' => 'строка, null, Mage_Core_Model_Locale, Zend_Locale'
			)
		);
	}
	return $result;
}

/**
 * Аргумент $text может быть массивом строк:
 * в этом случае будут переведены все строки массива.
 * @param mixed[]|string $text
 * @param string|object|string[]|object[] $module
 * @return string|string[]
 */
function df_translate($text, $module) {
	/** @var string|string[] $result */
	if (is_array($module)) {
		$result = Df_Localization_Helper_Translation::s()->translateByModules(df_array($text), $module);
	}
	else {
		/** @var int $argumentsCount */
		$argumentsCount = func_num_args();
		if ($argumentsCount < 2) {
			df_error('Недопустимое количество параметров.');
		}
		/** @var mixed[] $variables */
		if ($argumentsCount > 2) {
			/** @var mixed $arguments */
			$arguments = func_get_args();
			$text = array_shift($arguments);
			$module = array_pop($arguments);
			$variables = $arguments;
		}
		else if (is_array($text)) {
			$variables = $text;
			$text = array_shift($variables);
		}
		else {
			$variables = array();
		}
		if (is_object($module)) {
			$module = rm_module_name($module);
		}
		/** @var Df_Core_Model_Translate $t */
		static $t; if(!$t) {$t = Df_Core_Model_Translate::s();};
		$result = $t->translateFast($text, $module, $variables);
	}
	return $result;
}

/**
 * 2015-03-10
 * Работает быстрее, чем @see df_translate()
 * Не поддерживает дополнительные параметры переводимой строки,
 * а также @see Df_Localization_Realtime_Translator.
 * @param string|string[] $text
 * @param string|object $module
 * @return string|string[]
 */
function df_translate_simple($text, $module) {
	/** @var Df_Core_Model_Translate $t */
	static $t; if(!$t) {$t = Df_Core_Model_Translate::s();};
	return
		is_array($text)
		? df_map(__FUNCTION__, $text, $module)
		: $t->translateSimple($text, is_object($module) ? rm_module_name($module) : $module)
	;
}

/* @return Df_Localization_Helper_Translation */
function df_translator() {return Df_Localization_Helper_Translation::s();}

