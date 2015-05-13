<?php
class Df_Core_Helper_Debug extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $fileNameTemplate
	 * @param string $message
	 * @return Df_Core_Helper_Debug
	 */
	public function report($fileNameTemplate, $message) {
		df_param_string_not_empty($fileNameTemplate, 0);
		df_param_string_not_empty($message, 1);
		rm_file_put_contents(
			Df_Core_Model_Fs_GetNotUsedFileName::i(
				Mage::getBaseDir('var') . DS . 'log', $fileNameTemplate
			)->getResult()
			, $message
		);
		return $this;
	}

	/**
	 * @param mixed[] $data
	 * @return string
	 */
	public function log($data) {

	}

	/**
	 * @param mixed[] $data
	 * @return string
	 */
	public function logArray(array $data) {
		/** @var string $inner */
		$inner = '';
		foreach ($data as $key => $value)  {
			/** @var string|int $key */
			/** @var mixed $value */
			$inner .= "\n" . '[' . $key . '] => ' . rm_log($value);
			if (!$this->canLog($value)) {
				unset($data[$key]);
			}
		}
		return "array(" . df_tab_multiline($inner) . "\n)";
	}

	/**
	 * @param int $levelsToSkip	<p>
	 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
	 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
	 * </p>
	 *
	 * @param int $levelsToSkip[optional]
	 * @param array $bt[optional]
	 * @return Df_Core_Helper_Debug
	 */
	public function logCompactBacktrace($levelsToSkip = 0, array $bt = array()) {
		/** @var array $bt */
		$bt = $bt ? $bt : debug_backtrace();
		$bt = array_slice($bt, $levelsToSkip);
		/** @var array $compactBT */
		$compactBT = array();
		/** @var int $traceLength */
		$traceLength = count($bt);
		for ($traceIndex = 0; $traceIndex < $traceLength; $traceIndex++) {
			/** @var array $currentState */
			$currentState = df_a($bt, $traceIndex);
			/** @var array(string => string) $nextState */
			$nextState = df_a($bt, 1 + $traceIndex, array());
			$compactBT[]=
				array(
					'Файл' => df_a($currentState, 'file')
					,'Строка' => df_a($currentState, 'line')
					,'Субъект' =>
						!$nextState
						? ''
						: rm_concat_clean('::', df_a($nextState, 'class'), df_a($nextState, 'function'))
					,'Объект' =>
						!$currentState
						? ''
						: rm_concat_clean('::', df_a($currentState, 'class'), df_a($currentState, 'function'))
				)
			;
		}
		df()->debug()->report('bt-{date}-{time}.log', print_r($compactBT, true));
		return $this;
	}

	/**
	 * @param Varien_Object $object
	 * @return string
	 */
	public function logObject(Varien_Object $object) {
		return $this->logArray($object->getData());
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	private function canLog($value) {
		/** @var bool $result */
		$result = !is_object($value);
		if ($result) {
			if (is_array($value)) {
				foreach ($value as $subValue) {
					/** @var mixed $subValue */
					$result = $result && $this->canLog($subValue);
					if (!$result) {
						break;
					}
				}
			}
		}
		return $result;
	}

	/** @return Df_Core_Helper_Debug */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}