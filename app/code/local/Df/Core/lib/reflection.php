<?php
/**
 * @param string[] $parts
 * @return string
 */
function rm_concat_class($parts) {
	/** @uses func_get_args() не может быть параметром другой функции */
	$parts = is_array($parts) ? $parts : func_get_args();
	return implode('_', $parts);
}

/**
 * @param string|object $class
 * @return string
 */
function rm_cts($class) {return is_object($class) ? get_class($class) : $class;}

/**
 * @param string|object $class
 * @return string[]
 */
function rm_explode_class($class) {return explode('_', rm_cts($class));}

/**
 * «Df_SalesRule_Model_Event_Validator_Process» => «Df_SalesRule»
 * @param Varien_Object|string $object
 * @return string
 */
function rm_module_name($object) {return Df_Core_Model_Reflection::s()->getModuleName(rm_cts($object));}

