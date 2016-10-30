<?php
/**
 * 2015-03-24
 * Выполняет @uses df_parentheses_clean() для ключей массива.
 * @param array(string => mixed)
 * @return array(string => mixed)
 */
function df_parentheses_clean_k(array $map) {return
	array_combine(df_parentheses_clean(array_keys($map)), array_values($map))
;}

/**
 * 2015-03-24
 * «Николаевка (Ширяевск рн)» => «Николаевка»
 * @param string[] ...$names
 * @return string|string[]
 */
function df_parentheses_clean(...$names) {return
	df_call_a(function($name) {return
		df_first(df_parentheses_explode($name))
	;}, $names)
;}

/**
 * 2015-03-24
 * «Николаевка (Ширяевск рн)» => [«Николаевка», «Ширяевск рн»]
 * @param string $name
 * @return string[]
 */
function df_parentheses_explode($name) {
	/** @var string[] $result */
	$result = explode('(', $name);
	/** @var int $count */
	$count = count($result);
	if (1 < $count) {
		/**
		 * 2016-10-31
		 * Раньше тут стояло: df_assert_eq(2, $count);
		 * Однако в справочнике модуля доставки «ПЭК» встречаются значения
		 * с двумя группами скобок, например: «Советская (Краснодарский край) (Новокубанский р-н)».
		 * Две группы скобок после @see explode дают 3 строки.
		 * В общем, проверку убрал.
		 */
		$result = df_trim($result, '()');
	}
	return $result;
}