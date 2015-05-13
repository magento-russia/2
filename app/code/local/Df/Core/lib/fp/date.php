<?php
/**
 * @param Zend_Date $date [optional]
 * @param string|null $format [optional]
 * @return string
 */
function df_dts($date = null, $format = null) {
	if (!$date) {
		$date = Zend_Date::now();
	}
	/** @var string|bool $result */
	$result = $date->toString($format);
	/**
	 * Несмотря на свою спецификацию, @see Zend_Date::toString()
	 * может вернуть не только строку, но и FALSE.
	 * @link http://www.php.net/manual/en/function.date.php
	 * @link http://php.net/gmdate
	 */
	df_result_string_not_empty($result);
	return $result;
}

/**
 * Переводит дату из одного строкового формата в другой
 * @param string $dateInSourceFormat
 * @param string $sourceFormat
 * @param string $resultFormat
 * @param bool $canBeEmpty [optional]
 * @return string
 */
function df_dtss($dateInSourceFormat, $sourceFormat, $resultFormat, $canBeEmpty = false) {
	/** @var string $result */
	$result = '';
	if (!$dateInSourceFormat) {
		df_assert($canBeEmpty, 'Пустая дата недопустима.');
	}
	else {
		$result = df_dts(new Zend_Date($dateInSourceFormat, $sourceFormat), $resultFormat);
	}
	return $result;
}