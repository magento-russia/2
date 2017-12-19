<?php
/**
 * 2016-10-23
 * Используемой реализации, видимо, идентична такая: sprintf('%.2F', df_float($value))
 * В то же время реализация sprintf('%.2f', df_float($value)) вовсе не идентична используемой,
 * потому что она использует десятичный разделитель текущей локали: для России — запятую.
 * http://php.net/manual/en/function.sprintf.php
 * 3 => 3.00
 * 3.333 => 3.33
 * 3.300 => 3.30
 * https://3v4l.org/AUTCA
 *
 * @used-by Df_RussianPost_Model_Collector::getMethods()
 * @param float $value
 * @param int $precision [optional]
 * @return string
 */
function dff_2($value, $precision = 2) {return number_format($value, $precision, '.', '');}

