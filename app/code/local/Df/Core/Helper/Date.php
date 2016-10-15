<?php
class Df_Core_Helper_Date extends Mage_Core_Helper_Abstract {
	/**
	 * @param int|int[] $arguments
	 * @return Zend_Date
	 */
	public function create($arguments) {
		/** @uses func_get_args() не может быть параметром другой функции */
		$arguments = is_array($arguments) ? $arguments : func_get_args();
		/** @var int $numberOfArguments */
		$numberOfArguments = count($arguments);
		/** @var string[] $paramKeys */
		$paramKeys = array('year', 'month', 'day', 'hour', 'minute', 'second');
		/** @var int $countOfParamKeys */
		$countOfParamKeys = count($paramKeys);
		df_assert_between($numberOfArguments, 1, $countOfParamKeys);
		if ($countOfParamKeys > $numberOfArguments) {
			$arguments = array_merge(
				$arguments, array_fill(0, $countOfParamKeys - $numberOfArguments, 0)
			);
		}
		return new Zend_Date(array_combine($paramKeys, $arguments));
	}

	/**
	 * @param string $dateAsString
	 * @param string $format
	 * @param string $timezone
	 * @throws Exception
	 * @return Zend_Date
	 */
	public function createForTimezone($dateAsString, $format, $timezone) {
		df_param_string($dateAsString, 0);
		/** @var Zend_Date $zendDateForFormatting */
		$zendDateForFormatting = new Zend_Date($dateAsString, $format);
		/** @var string $timeInDateTimeFormat */
		$timeInDateTimeFormat = df_dts($zendDateForFormatting, 'y-MM-dd HH:mm:ss');
		/** @var DateTime $dateTime */
		$time = new DateTime($timeInDateTimeFormat, new DateTimeZone($timezone));
		/** @var Zend_Date $result */
		$result = new Zend_Date($time->getTimestamp());
		return $result;
	}

	/**
	 * @param string $datetime
	 * @param bool $throw
	 * @return Zend_Date|null
	 * @throws Exception
	 */
	public function fromDb($datetime, $throw = true) {
		df_param_string($datetime, 0);
		/** @var Zend_Date|null $result */
		$result = null;
		if ($datetime) {
			try {
				$result = new Zend_Date($datetime, Zend_Date::ISO_8601);
			}
			catch (Exception $e) {
				if ($throw) {
					df_error($e);
				}
			}
		}
		return $result;
	}

	/**
	 * @param Zend_Date|null $date [optional]
	 * @return int
	 */
	public function getHour(Zend_Date $date = null) {
		if (is_null($date)) {
			$date = Zend_Date::now();
		}
		return df_nat0($date->toString(Zend_Date::HOUR_SHORT, 'iso'));
	}

	/**
	 * @param Zend_Date $date1
	 * @param Zend_Date $date2
	 * @return bool
	 */
	public function gt(Zend_Date $date1, Zend_Date $date2) {
		return $date1->getTimestamp() > $date2->getTimestamp();
	}

	/**
	 * @param Zend_Date $date1
	 * @param Zend_Date $date2
	 * @return bool
	 */
	public function lt(Zend_Date $date1, Zend_Date $date2) {
		return $date1->getTimestamp() < $date2->getTimestamp();
	}

	/**
	 * @param Zend_Date $date1
	 * @param Zend_Date $date2
	 * @return Zend_Date
	 */
	public function max(Zend_Date $date1, Zend_Date $date2) {
		return $this->gt($date1, $date2) ? $date1 : $date2;
	}

	/**
	 * @param Zend_Date $date1
	 * @param Zend_Date $date2
	 * @return Zend_Date
	 */
	public function min(Zend_Date $date1, Zend_Date $date2) {
		return $this->lt($date1, $date2) ? $date1 : $date2;
	}

	/**
	 * @param Zend_Date $date
	 * @param bool $inCurrentTimeZone [optional]
	 * @return string
	 */
	public function toDb(Zend_Date $date, $inCurrentTimeZone = true) {
		return $date->toString($inCurrentTimeZone ? 'Y-MM-dd HH:mm:ss' : Zend_Date::ISO_8601);
	}

	/** @return Df_Core_Helper_Date */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}