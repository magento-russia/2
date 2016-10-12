<?php
/**
 * Инструмент парадигмы «convention over configuration»
 */
class Df_Core_Model_ClassManager extends Df_Core_Model {
	/**
	 * @param Varien_Object $caller
	 * @return string
	 */
	public function getFeatureCode(Varien_Object $caller) {
		/** @var string $callerClassName */
		$callerClassName = get_class($caller);
		if (!isset($this->{__METHOD__}[$callerClassName])) {
			/** @var string $callerModuleName */
			$callerModuleName = df()->reflection()->getModuleName($callerClassName);
			$callerModuleNameShort =
				rm_last(explode(Df_Core_Model_Reflection::PARTS_SEPARATOR, $callerModuleName))
			;
			$this->{__METHOD__}[$callerClassName] = mb_strtolower($callerModuleNameShort);
		}
		return $this->{__METHOD__}[$callerClassName];
	}
	
	/**
	 * @param Varien_Object $caller
	 * @return string
	 */
	public function getFeatureSuffix(Varien_Object $caller) {
		/** @var string $callerClassName */
		$callerClassName = get_class($caller);
		if (!isset($this->{__METHOD__}[$callerClassName])) {
			$this->{__METHOD__}[$callerClassName] =
				mb_strtolower (
					rm_last(explode(Df_Core_Model_Reflection::PARTS_SEPARATOR, $callerClassName))
				)
			;
		}
		return $this->{__METHOD__}[$callerClassName];
	}

	/**
	 * Возвращает или Df_<имя конечного модуля>_<окончание класса ресурса>,
	 * если данный класс присутствует, или $defaultResult, если отсутствует
	 * @param Varien_Object $caller
	 * @param string $resourceSuffix
	 * @param string|null $defaultResult [optional]
	 * @param bool $throwOnError [optional]
	 * @return string|null
	 */
	public function getResourceClass(
		Varien_Object $caller, $resourceSuffix, $defaultResult = null, $throwOnError = true
	) {
		df_param_string_not_empty($resourceSuffix, 1);
		/** @var string $callerClassName */
		$callerClassName = get_class($caller);
		/** @var string $cacheKey */
		$cacheKey = implode('_', array($callerClassName, $resourceSuffix));
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var string $callerModuleName */
			$callerModuleName = df()->reflection()->getModuleName($callerClassName);
			/** @var string $resourceClass */
			$resourceClass =
				implode(
					Df_Core_Model_Reflection::PARTS_SEPARATOR
					, array($callerModuleName, $resourceSuffix)
				)
			;
			/** @var string|null $result */
			if (@class_exists($resourceClass)) {
				$result = $resourceClass;
			}
			else {
				if ($defaultResult) {
					df_param_string_not_empty($defaultResult, 2);
					$result = $defaultResult;
				}
				else {
					if ($throwOnError) {
						df_error_internal('Системе требуется класс «%s».', $resourceClass);
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

	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_ClassManager */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}