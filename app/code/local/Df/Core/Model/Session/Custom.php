<?php
abstract class Df_Core_Model_Session_Custom extends Mage_Core_Model_Session_Abstract_Varien {
	/** @return string */
	abstract protected function getSessionIdCustom();

	/**
	 * @override
	 * @return bool
	 * @see Mage_Core_Model_Session_Abstract_Varien::start():
		if (isset($_SESSION) && !$this->getSkipEmptySessionCheck()) {
			return $this;
		}
	 */
	public function getSkipEmptySessionCheck() {return true;}

	/**
	 * @override
	 * @param string|null $id [optional]
	 * @return \Df\C1\Cml2\Session\ByCookie\C1
	 */
	public function setSessionId($id = null) {
		/**
		 * Обратите внимание, что параметр $id мы намеренно никак не используем
		 * по следующим причинам:
		 * 1) никто не вызовет этот наш метод с параметром $id
		 * 2) если кто-то всё-таки потом когда-нибудь попробует вызвать этот наш метод
		 * с параметром $id, то это всё равно будет неверно,
		 * потому что идентификатор сессии подраумевается брать только из метода
		 * @see Df_Core_Model_Session_Custom::getSessionIdCustom().
		 */
		parent::setSessionId($this->getSessionIdCustom());
		return $this;
	}

	/**
	  * @override
	  * @param string $sessionName [optional]
	  * @return Df_Core_Model_Session_Custom
	  */
	public function start($sessionName = null) {
		self::$_currentSession = $this;
		parent::start($sessionName);
		return $this;
	}

	/** @return string */
	protected function getNamespace() {return get_class($this);}

	/** @var Df_Core_Model_Session_Custom|null */
	protected static $_currentSession = null;
}