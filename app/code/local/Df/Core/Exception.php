<?php
class Df_Core_Exception extends Mage_Core_Exception {
	/**
	 * @param string|null $key [optional]
	 * @param mixed $defaultValue [optional]
	 * @return mixed
	 */
	public function cfg($key = null, $defaultValue = null) {
		return
			is_null($key)
			? $this->_data
			: df_a($this->_data, $key, $defaultValue)
		;
	}

	/**
	 * Стандартный метод @see Exception::getMessage() объявлен как final.
	 * Чтобы метод для получения диагностического сообщения
	 * можно было переопределять — добавляем свой.
	 * @see Df_Core_Exception::getMessageStatic()
	 * @return string
	 */
	public function getMessageRm() {return $this->getMessage();}

	/** @return int */
	public function getStackLevelsCountToSkip() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 0;
		}
		return $this->{__METHOD__};
	}

	/**
	 * К сожалению, не можем перекрыть Exception::getTraceAsString(),
	 * потому что этот метод — финальный
	 * @return string
	 */
	public function getTraceAsText() {
		return
			Df_Qa_Message_Failure_Exception::i(array(
				Df_Qa_Message_Failure_Exception::P__EXCEPTION => $this
				,Df_Qa_Message_Failure_Exception::P__NEED_LOG_TO_FILE => false
				,Df_Qa_Message_Failure_Exception::P__NEED_NOTIFY_DEVELOPER => false
			))->traceS()
		;
	}

	/**
	 * @param string|array(string =>mixed) $key
	 * @param mixed $value [optional]
	 * @return Df_Core_Exception
	 */
	public function setData($key, $value = null) {
		if (is_array($key)) {
			$this->_data = $key;
		}
		else {
			$this->_data[$key] = $value;
		}
		return $this;
	}

	/**
	 * @param int $count
	 * @return Df_Core_Exception
	 */
	public function setStackLevelsCountToSkip($count) {
		$this->{__CLASS__ . '::getStackLevelsCountToSkip'} = $count;
		return $this;
	}

	/** @var array(string => mixed) */
	protected $_data = array();
}