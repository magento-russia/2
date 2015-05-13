<?php
class Df_Core_Helper_Date extends Mage_Core_Helper_Abstract {
	/**
	 * @param Zend_Date $date
	 * @return Zend_Date
	 */
	public function cleanTime(Zend_Date $date) {return $date->setHour(0)->setMinute(0)->setSecond(0);}

	/**
	 * @param int|int[] $arguments
	 * @return Zend_Date
	 */
	public function create($arguments) {
		/**
		 * Обратите внимание,
		 * что функция func_get_args() не может быть параметром другой функции.
		 */
		$arguments = is_array($arguments) ? $arguments : func_get_args();
		/** @var int $numberOfArguments */
		$numberOfArguments = count($arguments);
		/** @var string[] $paramKeys */
		$paramKeys = array('year', 'month', 'day', 'hour', 'minute', 'second');
		/** @var int $countOfParamKeys */
		$countOfParamKeys = count($paramKeys);
		df_assert_between($numberOfArguments, 1, $countOfParamKeys);
		if ($countOfParamKeys > $numberOfArguments) {
			$arguments =
				array_merge($arguments, array_fill(0, $countOfParamKeys - $numberOfArguments, 0))
			;
		}
		return new Zend_Date(array_combine($paramKeys, $arguments));
	}

	/**
	 * @param string $dateAsString
	 * @param string $format[optional]
	 * @throws Exception
	 * @return Zend_Date
	 */
	public function createForDefaultTimezone($dateAsString, $format = Zend_Date::W3C) {
		return $this->createForTimezone($dateAsString, $format, Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
	}

	/**
	 * @param string $dateAsString
	 * @param string $format[optional]
	 * @throws Exception
	 * @return Zend_Date
	 */
	public function createForMoscow($dateAsString, $format = Zend_Date::W3C) {
		return $this->createForTimezone($dateAsString, $format, 'Europe/Moscow');
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
			catch(Exception $e) {
				if ($throw) {
					throw $e;
				}
			}
		}
		return $result;
	}

	/**
	 * Создаёт объект-дату по строке вида «20131115153657».
	 * @param string $timestamp
	 * @param string|null $offsetType [optional]
	 * @return Zend_Date
	 */
	public function fromTimestamp14($timestamp, $offsetType = null) {
		df_assert(ctype_digit($timestamp));
		df_assert_eq(14, strlen($timestamp));
		// Почему-то new Zend_Date($timestamp, 'yMMddHHmmss') у меня не работает
		/** @var string $pattern */
		$pattern = '#(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})#';
		/** @var int[] $matches */
		$matches = array();
		/** @var int $r */
		$r = preg_match($pattern, $timestamp, $matches);
		df_assert_eq(1, $r);
		/** @var int $hour */
		$hour = rm_nat0(df_a($matches, 4));
		if ($offsetType) {
			df_assert_in($offsetType, array('UTC', 'GMT'));
			/** @var int $offsetFromGMT */
			$offsetFromGMT = rm_round(rm_int(df_dts(Zend_Date::now(), Zend_Date::TIMEZONE_SECS)) / 3600);
			$hour += $offsetFromGMT;
			if ('UTC' === $offsetType) {
				$hour++;
			}
		}
		return new Zend_Date(array(
			'year' => df_a($matches, 1)
		   ,'month' => df_a($matches, 2)
		   ,'day' => df_a($matches, 3)
		   ,'hour' => $hour
		   ,'minute' => df_a($matches, 5)
		   ,'second' => df_a($matches, 6)
		));
	}

	/**
	 * @param Zend_Date $startDate
	 * @param int $numWorkingDays
	 * @param Mage_Core_Model_Store|string|int|null $store [optional]
	 * @return int
	 */
	public function getNumCalendarDaysByNumWorkingDays(Zend_Date $startDate, $numWorkingDays, $store = null) {
		/** @var int $result */
		$result = $numWorkingDays;
		if ((0 === $result) && $this->isDayOff($startDate)) {
			$result++;
		}
		/** @var int[] $daysOff */
		$daysOff = df()->date()->getDaysOff($store);
		// все дни недели не могут быть выходными, иначе программа зависнет в цикле ниже
		df_assert_lt(7, count($daysOff));
		/** @var int $currentDayOfWeek */
		$currentDayOfWeek = $this->getDayOfWeekAsDigit($startDate);
		while (0 < $numWorkingDays) {
			while (in_array($currentDayOfWeek, $daysOff)) {
				$result++;
				$currentDayOfWeek = (++$currentDayOfWeek) % 7;
			}
			$numWorkingDays--;
		}
		return $result;
	}

	/**
	 * @param Zend_Date|null $date [optional]
	 * @return int
	 */
	public function getDayOfWeekAsDigit(Zend_Date $date = null) {
		if (is_null($date)) {
			$date = Zend_Date::now();
		}
		return rm_nat0($date->toString(Zend_Date::WEEKDAY_8601, 'iso'));
	}

	/**
	 * @param Mage_Core_Model_Store|string|int|null $store [optional]
	 * @return int[]
	 */
	public function getDaysOff($store = null) {
		return rm_int(df_parse_csv(df_nts(Mage::getStoreConfig('general/locale/weekend', $store))));
	}

	/** @return string */
	public function getFormatShort() {
		/** @var string $result */
		static $result;
		if (!isset($result)) {
			/** @var string $result */
			$result = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
			/**
			 * @see Mage_Core_Model_Locale::getDateFormat() может вернуть false
			 */
			df_result_string($result);
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
		return rm_nat0($date->toString(Zend_Date::HOUR_SHORT, 'iso'));
	}
	
	/** @return Zend_Date */
	public function getLeast() {
		return new Zend_Date(0);
	}

	/** @return Zend_Date */
	public function getNowInCurrentTimeZone() {
		/** @var Zend_Date $result */
		$result = Zend_Date::now();
		$result->setTimezone(Mage::app()->getLocale()->getTimezone());
		return $result;
	}

	/**
	 * @param Zend_Date $date1
	 * @param Zend_Date $date2
	 * @return int
	 */
	public function getNumberOfDaysBetweenTwoDates(Zend_Date $date1, Zend_Date $date2) {
		/** @var Zend_Date $dateMin */
		$dateMin = $this->min($date1, $date2);
		/** @var Zend_Date $dateMax */
		$dateMax = $this->max($date1, $date2);
		/**
		 * @link http://stackoverflow.com/a/3118478/254475
		 */
		/** @var Zend_Date $dateMinA */
		$dateMinA = new Zend_Date($dateMin);
		/** @var Zend_Date $dateMaxA */
		$dateMaxA = new Zend_Date($dateMax);
		$dateMinA->setHour(0)->setMinute(0)->setSecond(0);
		$dateMaxA->setHour(0)->setMinute(0)->setSecond(0);
		/**
		 * Zend_Date::sub() возвращает число в виде строки для Magento CE 1.4.0.1
		 * и объект класса Zend_Date для более современных версий Magento
		 */
		$dateMaxA->sub($dateMinA);
		$result = rm_round($dateMaxA->toValue() / 86400);
		return $result;
	}
	
	/** @return Zend_Date */
	public function getUnlimited() {
		$timezone = date_default_timezone_get();
		date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
		/** @var Zend_Date $result */
		$result = new Zend_Date('2050-01-01');
		date_default_timezone_set($timezone);
		return $result;
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
	 * @param Zend_Date $date
	 * @return bool
	 */
	public function isInFuture(Zend_Date $date) {
		return $this->gt($date, Zend_Date::now());
	}

	/**
	 * @param Zend_Date $date
	 * @param Mage_Core_Model_Store|string|int|null $store [optional]
	 * @return bool
	 */
	public function isDayOff(Zend_Date $date, $store = null) {
		return in_array($this->getDayOfWeekAsDigit($date), $this->getDaysOff($store));
	}

	/**
	 * @param Zend_Date $date
	 * @return bool
	 */
	public function isInPast(Zend_Date $date) {
		return $this->lt($date, Zend_Date::now());
	}
	
	/**           
	 * @param Zend_Date $date
	 * @return bool
	 */
	public function isLeast(Zend_Date $date) {
		return $date->equals($this->getLeast());
	}	

	/**
	 * @param Mage_Core_Model_Store|string|int|null $store [optional]
	 * @return bool
	 */
	public function isTodayOff($store = null) {
		return $this->isDayOff(Zend_Date::now(), $store);
	}

	/**
	 * @param Zend_Date $date
	 * @return bool
	 */
	public function isUnlimited(Zend_Date $date) {
		return $date->equals($this->getUnlimited());
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

	/** @return string */
	public function nowInMoscowAsText() {
		/** @var Zend_Date $time */
		$time = Zend_Date::now();
		$time->setTimezone('Europe/Moscow');
		return $time->toString('y-MM-dd HH:mm:ss z');
	}

	/**
	 * @param Zend_Date $date
	 * @param bool $inCurrentTimeZone[optional]
	 * @return string
	 */
	public function toDb(Zend_Date $date, $inCurrentTimeZone = true) {
		return $date->toString($inCurrentTimeZone ? 'Y-MM-dd HH:mm:ss' : Zend_Date::ISO_8601);
	}

	/** @return string */
	public function toDbNow() {return $this->toDb($this->getNowInCurrentTimeZone());}

	/**
	 * @param bool $new [optional]
	 * @return Zend_Date
	 */
	public function tomorrow($new = false) {
		/** @var Zend_Date $result */
		if ($new) {
			$result = $this->createTomorrow();
		}
		else {
			if (!isset($this->{__METHOD__})) {
				$this->{__METHOD__} = $this->createTomorrow();
			}
			$result = $this->{__METHOD__};
		}
		return $result;
	}

	/** @return Zend_Date */
	public function yesterday() {return Zend_Date::now()->subDay(1);}

	/** @return Zend_Date */
	private function createTomorrow() {return clone Zend_Date::now()->addDay(1);}

	/** @return Df_Core_Helper_Date */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}