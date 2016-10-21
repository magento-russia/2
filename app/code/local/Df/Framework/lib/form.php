<?php
use Df\Framework\Form\Element as E;
use Varien_Data_Form_Element_Abstract as AE;

/**
 * 2016-01-29
 * @param AE|E $e
 * @param string|null $key [optional]
 * @param string|null|callable $default [optional]
 * @return string|null|array(string => mixed)
 */
function df_fe_fc(AE $e, $key = null, $default = null) {
	/** @var Mage_Core_Model_Config_Element $result */
	$result = df_fe_top($e)->getFieldConfig();
	df_assert($result instanceof Mage_Core_Model_Config_Element);
	return $key ? df_leaf_child($result, $key, $default) : $result->asArray();
}

/**
 * 2016-05-30
 * @param AE|E $e
 * @param string $key
 * @param bool|null|callable $default [optional]
 * @return bool
 */
function df_fe_fc_b(AE $e, $key, $default = false) {return df_bool(df_fe_fc($e, $key, $default));}

/**
 * 2016-01-29
 * @param AE|E $e
 * @param string $key
 * @param int|null|callable $default [optional]
 * @return int
 */
function df_fe_fc_i(AE $e, $key, $default = 0) {return df_int(df_fe_fc($e, $key, $default));}

/**
 * 2016-01-29
 * К сожалению, нельзя использовать @see is_callable(),
 * потому что эта функция всегда вернёт true из-за наличия магического метода 
 * @see Varien_Object::__call()
 * @param AE|E $e
 * @return AE|E
 */
function df_fe_top(AE $e) {return method_exists($e, 'top') ? $e->top() : $e;}

