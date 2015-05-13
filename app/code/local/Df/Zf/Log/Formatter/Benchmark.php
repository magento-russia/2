<?php
class Df_Zf_Log_Formatter_Benchmark extends Zend_Log_Formatter_Simple {
	/**
	 * @override
	 * @param array(string => mixed) $event
	 * @return string
	 */
	public function format($event) {
		$timeCurrent = microtime(true);
		/** @var float $timeStart */
		static $timeStart;
		if (!isset($timeStart)) {
			$timeStart = $timeCurrent;
		}
		/** @var float $timePrev */
		static $timePrev;
		if (!isset($timePrev)) {
			$timePrev = $timeCurrent;
		}
		/** @var string $message */
		$message = df_a($event, 'message');
		/** @var bool $isRaw */
		$isRaw = df_a($event, Df_Core_Model_Logger::FORMAT__RAW);
		/** @var string $result */
		$result =
			$isRaw
			? $message . "\r\n"
			: rm_sprintf(
				"%s [%s]: %s\r\n"
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
}