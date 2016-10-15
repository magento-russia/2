<?php
namespace Df\Core;
// инструмент парадигмы «convention over configuration»
class Convention {
	/**
	 * Проверяет наличие следующих классов в указанном порядке:
	 * 1) <имя конечного модуля>\<окончание класса>
	 * 2) $defaultResult
	 * Возвращает первый из найденных классов.
	 * @param object|string $caller
	 * @param string $classSuffix
	 * @param string|null $defaultResult [optional]
	 * @param bool $throwOnError [optional]
	 * @return string|null
	 */
	public function getClass($caller, $classSuffix, $defaultResult = null, $throwOnError = true) {
		df_param_string_not_empty($classSuffix, 1);
		/** @var string $callerClassName */
		$callerClassName = df_cts($caller);
		/** @var string $cacheKey */
		$cacheKey = df_ckey($callerClassName, $classSuffix, $defaultResult);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var string $delimiter */
			$delimiter = df_class_delimiter($callerClassName);
			/** @var string $callerModuleName */
			$callerModuleName = df_module_name($callerClassName, $delimiter);
			/** @var string $class */
			$class = $callerModuleName . $delimiter . $classSuffix;
			/** @var string|null $result */
			if (df_class_exists($class)) {
				$result = $class;
			}
			else {
				if ($defaultResult) {
					df_param_string_not_empty($defaultResult, 2);
					$result = $defaultResult;
				}
				else {
					if ($throwOnError) {
						df_error('Системе требуется класс «%s».', $class);
					}
					else {
						$result = null;
					}
				}
			}
			$this->{__METHOD__}[$cacheKey] = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__}[$cacheKey]);
	}

	/**
	 * 2016-07-10
	 * @param object|string $caller
	 * @param string $classSuffix
	 * @param string|null $defaultResult [optional]
	 * @param bool $throwOnError [optional]
	 * @return string|null
	 */
	public function getClassInTheSameFolder(
		$caller, $classSuffix, $defaultResult = null, $throwOnError = true
	) {
		df_param_string_not_empty($classSuffix, 1);
		/** @var string $callerClassName */
		$callerClassName = df_cts($caller);
		/** @var string $cacheKey */
		$cacheKey = df_ckey($callerClassName, $classSuffix, $defaultResult);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var string $class */
			$class = df_class_replace_last($callerClassName, $classSuffix);
			/** @var string|null $result */
			if (df_class_exists($class)) {
				$result = $class;
			}
			else {
				if ($defaultResult) {
					df_param_string_not_empty($defaultResult, 2);
					$result = $defaultResult;
				}
				else {
					if ($throwOnError) {
						df_error('Системе требуется класс «%s».', $class);
					}
					else {
						$result = null;
					}
				}
			}
			$this->{__METHOD__}[$cacheKey] = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__}[$cacheKey]);
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}