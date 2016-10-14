<?php
/** @used-by Df_Core_Model_Logger::getWriter() */
class Df_Zf_Log_Formatter_Benchmark extends Zend_Log_Formatter_Simple {
	/**
	 * @override
	 * @param array(string => mixed) $event
	 * @return string
	 */
	public function format($event) {
		$timeCurrent = microtime(true);
		/** @var float $timeStart */
		static $timeStart; if (!$timeStart) {$timeStart = $timeCurrent;}
		/** @var float $timePrev */
		static $timePrev; if (!$timePrev) {$timePrev = $timeCurrent;}
		/** @var string $message */
		$message = dfa($event, 'message');
		/** @var bool $isRaw */
		$isRaw = dfa($event, self::FORMAT__RAW);
		/** @var string $result */
		$result =
			$isRaw
			? $message . "\n"
			: rm_sprintf(
				"%s [%s]: %s\n"
				, df_dts(Zend_Date::now(), 'HH:mm:ss')
				, $this->formatTime($timeCurrent - $timePrev)
				, $message
			)
		;
		$timePrev = $timeCurrent;
		return $result;
	}

	/**
	 * @param string $timeAsFloatInSeconds
	 * @return string
	 */
	private function formatTime($timeAsFloatInSeconds) {
		return rm_sprintf('%.3f', $timeAsFloatInSeconds);
	}

	/**
	 * @used-by Df_Core_Model_Logger::logRaw()
	 * @used-by format()
	 */
	const FORMAT__RAW = 'raw';
}