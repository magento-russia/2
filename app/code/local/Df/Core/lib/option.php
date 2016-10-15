<?php
/**
 * 2015-02-11
 * Превращает массив вида array('value' => 'label')
 * в массив вида array(array('value' => '', 'label' => ''))
 * Обратная операция: @see df_options_to_map()
 *
 * 2015-03-09
 * Добавил к функции второй опциональный параметр: $module
 * Он позволяет переводить зназвания (labels) опций.
 *
 * @param array(string|int => string) $map
 * @param object|string|null $module [optional]
 * @return array(array(string => string|int))
 */
function df_map_to_options(array $map, $module = null) {
	return array_map('df_option', array_keys($map), !$module ? $map : df_translate_simple($map, $module));
}

/**
 * 2015-02-11
 * Эта функция равноценна вызову df_map_to_options(array_flip($map))
 * Превращает массив вида array('label' => 'value')
 * в массив вида array(array('value' => '', 'label' => ''))
 * @param array(string|int => string) $map
 * @return array(array(string => string|int))
 */
function df_map_to_options_reverse(array $map) {return array_map('df_option', $map, array_keys($map));}

/**
 * @param string|int $value
 * @param string $label
 * @return array(string => string|int)
 */
function df_option($value, $label) {return array('label' => $label, 'value' => $value);}

/**
 * @param array(string => string) $option
 * @param string|null $default [optional]
 * @return string|null
 */
function df_option_v(array $option, $default = null) {return dfa($option, 'value', $default);}

/**
 * Превращает массив вида array(array('value' => '', 'label' => ''))
 * в массив вида array('value').
 * @param array(string => string) $options
 * @return string|null
 */
function df_option_values(array $options) {return array_column($options, 'value');}

/**
 * Превращает массив вида array(array('value' => '', 'label' => ''))
 * в массив вида array('value' => 'label')
 * Обратная операция: @see df_map_to_options()
 * @param array(array(string => string|int)) $options
 * @return array(string|int => string)
 */
function df_options_to_map(array $options) {return array_column($options, 'label', 'value');}

/**
 * 2015-08-10
 * @used-by Df_Dataflow_Model_Importer_Product::importAttributeValues()
 * Превращает массив вида array(array('value' => '', 'label' => ''))
 * в массив вида array('label' => 'value')
 * Обратная операция: @see df_map_to_options_reverse()
 * @param array(array(string => string|int)) $options
 * @return array(string|int => string)
 */
function df_options_to_map_reverse(array $options) {return array_column($options, 'value', 'label');}




