<?php
/**
 * 2015-04-09
 * @param float|int $value
 * @param string $curencyCode
 * @return string
 */
function df_money_fl($value, $curencyCode) {
	return rm_currency_zf($curencyCode)->toCurrency(
		$value, array('precision' => rm_currency_precision())
	);
}

/**
 * 2015-04-09
 * @param float|int $value
 * @return string
 */
function df_number_2f($value) {return sprintf('.2F', df_float($value));}

/**
 * 2015-04-09
 * Пока нигде не используется.
 * @param float|int $value
 * @return string
 */
function df_number_2fl($value) {return sprintf('.2f', df_float($value));}

/**
 * 2015-04-09
 * Форматирует вещественное число с отсечением незначащих нулей после запятой.
 * @used-by Df_Tax_Setup_3_0_0::addRate()
 * @param float|int $value
 * @return string
 */
function df_number_f($value) {
	/** @var float $valueF */
	$valueF = df_float($value);
	/** @var int $intPart */
	$intPart = (int)$valueF;
	// намеренно используем «==»
	return $valueF == $intPart ? (string)$intPart : rtrim(sprintf('%f', $valueF), '0');
}



