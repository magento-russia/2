<?php
// инструмент парадигмы «convention over configuration»
class Df_Core_Model_Convention extends Df_Core_Model {
	/**
	 * Возвращает или Df_<имя конечного модуля>_<окончание класса>,
	 * если данный класс присутствует, или $defaultResult, если отсутствует
	 * 2015-02-28
	 * Обратите внимание, что мы не можем сделать @see Varien_Object типом параметра $caller,
	 *
	 * @param Varien_Object $caller
	 * @param string $classSuffix
	 * @param string|null $defaultResult [optional]
	 * @param bool $throwOnError [optional]
	 * @return string|null
	 */
	public function getClass(
		Varien_Object $caller, $classSuffix, $defaultResult = null, $throwOnError = true
	) {
		df_param_string_not_empty($classSuffix, 1);
		/** @var string $callerClassName */
		$callerClassName = get_class($caller);
		/** @var string $cacheKey */
		$cacheKey = implode('_', array($callerClassName, $classSuffix));
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var string $callerModuleName */
			$callerModuleName = rm_module_name($callerClassName);
			/** @var string $class */
			$class = df_concat_class($callerModuleName, $classSuffix);
			/** @var string|null $result */
			if (@class_exists($class)) {
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
			$this->{__METHOD__}[$cacheKey] = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__}[$cacheKey]);
	}

	/** @return Df_Core_Model_Convention */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}