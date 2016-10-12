<?php
abstract class Df_Licensor_Model_Server_Time extends Df_Core_Model {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function retrieveTimeAsString();

	/** @return Zend_Date */
	public function getTime() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Date $result */
			$result = null;
			/** @var string|bool $resultAsString */
			$resultAsString = $this->getCache()->loadData($this->getCacheKey_Time());
			if ($resultAsString) {
				try {
					$result = new Zend_Date($resultAsString, Zend_Date::W3C);
				}
				catch(Exception $e) {
					// Кэш повреждён
					$resultAsString = false;
				}
			}
			if (!$resultAsString) {
				/** @var int $numRetries */
				$numRetries = 2;
				while ((0 < $numRetries) && is_null($result)) {
					try {
						$resultAsString = $this->retrieveTimeAsString();
						$result = new Zend_Date($resultAsString, Zend_Date::W3C);
					}
					catch(Exception $e) {
						// Исключительная ситуация здесь допустима,
						// потому что мы можем повторить попытку заново
					}
					$numRetries--;
				}
				if (is_null($result)) {
					/** @var array $errorMessageParts */
					$errorMessageParts = array('Не могу получить время с сервера.');
					if ($resultAsString) {
						$errorMessageParts[]= rm_sprintf('<br/>Ответ сервера: «%s».', $resultAsString);
					}
					df_error(implode("\r\n", $errorMessageParts));
				}
				$this->getCache()->saveData($this->getCacheKey_Time(), $resultAsString);
			}
			df_assert($result instanceof Zend_Date);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(null, $this->getCacheLifetime_Time());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getCacheLifetime_Time() {
		// Обратите внимание, что верное время берется из кэша,
		// поэтому его погрешность равна времени жизни кэша.
		return 86400 * (Df_Licensor_Model_Validator_ServerTime::MAX_CORRECT_INTERVAL_IN_DAYS - 2);
	}

	/** @return string */
	private function getCacheKey_Time() {return md5(get_class($this));}

	const _CLASS = __CLASS__;
}