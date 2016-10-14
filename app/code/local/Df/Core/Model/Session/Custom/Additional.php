<?php
abstract class Df_Core_Model_Session_Custom_Additional extends Df_Core_Model_Session_Custom {
	/** @return Df_Core_Model_Session_Custom_Additional */
	public function begin() {
		if (self::$_currentSession) {
			$this->_previousSession = self::$_currentSession;
		}
		if ($this->isSessionStarted()) {
			$this->_previousName = session_name();
			session_write_close();
			$_SESSION = array();
		}
		$this->start($this->getName());
		$this->init($this->getNamespace());
		return $this;
	}

	/** @return Df_Core_Model_Session_Custom_Additional */
	public function end() {
		session_write_close();
		$_SESSION = array();
		if ($this->_previousName) {
			if ($this->_previousSession) {
				$this->_previousSession->start($this->_previousName);
				$this->_previousSession->init($this->_previousSession->getNamespace());
				$this->_previousSession = null;
			}
			else {
				session_name($this->_previousName);
				session_start();
			}
			$this->_previousName = null;
		}
		return $this;
	}
	/** @return string */
	protected function getName() {return get_class($this);}

	/** @return bool */
	private function isSessionStarted() {
		/** http://php.net/manual/function.session-status.php#111945 */
		return
				('cli' !== php_sapi_name())
			&&
				(
						(function_exists('session_status'))
					?
						/**
						 * Функция @see session_status() и константа @see PHP_SESSION_ACTIVE
						 * появились только в PHP 5.4
						 * http://magento-forum.ru/topic/4627/
						 */
						(PHP_SESSION_ACTIVE === session_status())
					:
						('' !== session_id())
				)
		;
	}

	/** @var string|null */
	private $_previousName = null;
	/** @var Df_Core_Model_Session_Custom|null */
	private $_previousSession = null;
}