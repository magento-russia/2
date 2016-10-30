<?php
/**
 * @used-by df_f2i()
 * @param float $value
 * @param int $precision [optional]
 * @return string
 *
 * 2016-10-23
 * Используемой реализации, видимо, идентична такая: sprintf('%.2F', df_float($value))
 * В то же время реализация sprintf('%.2f', df_float($value)) вовсе не идентична используемой,
 * потому что она использует десятичный разделитель текущей локали: для России — запятую.
 * http://php.net/manual/en/function.sprintf.php
 * 3 => 3.00
 * 3.333 => 3.33
 * 3.300 => 3.30
 * https://3v4l.org/AUTCA
 */
function df_f2($value, $precision = 2) {return number_format($value, $precision, '.', '');}

/**
 * 2016-09-08
 * @param float|int|string $value
 * @return float
 */
function df_f2f($value) {return floatval(df_f2(floatval($value)));}

/**
 * @param int|float $value
 * @param int $precision [optional]
 * @return string
 * 2016-10-23
 * Для нецелых чисел работает как @see df_f2(),
 * а для целых — отбрасывает десятичную часть.
 * 3 => 3
 * 3.333 => 3.33
 * 3.300 => 3.30
 * https://3v4l.org/AUTCA
 */
function df_f2i($value, $precision = 2) {return
	is_int($value) ? (string)$value : df_f2($value, $precision)
;}

/**
 * 2015-04-09
 * Форматирует вещественное число с отсечением незначащих нулей после запятой.
 * 2016-10-23
 * 3 => 3
 * 3.333 => 3.333
 * 3.300 => 3.3
 * @used-by Df_Tax_Setup_3_0_0::rate()
 * @param float|int $value
 * @return string
 */
function df_fchop0($value) {
	/** @var float $valueF */
	$valueF = df_float($value);
	/** @var int $intPart */
	$intPart = (int)$valueF;
	// намеренно используем «==»
	return $valueF == $intPart ? (string)$intPart : rtrim(sprintf('%f', $valueF), '0');
}

/**
 * 2016-09-08
 * @param float $amount
 * @return bool
 */
function df_is0($amount) {return abs($amount) < 0.01;}

/**
 * 2015-04-09
 * @param float|int $value
 * @param string $curencyCode
 * @return string
 */
function df_money_fl($value, $curencyCode) {return
	df_currency_zf($curencyCode)->toCurrency($value, ['precision' => df_currency_precision()])
;}