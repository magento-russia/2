<?php
abstract class Df_Core_Model_Session_Custom_Primary extends Df_Core_Model_Session_Custom {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Обратите внимание, что хотя @see Mage_Core_Model_Session_Abstract_Varien::init()
		 * способна автоматически вызывать @see Mage_Core_Model_Session_Abstract_Varien::start(),
		 * однако в нашем случае надо явно вызвать start() вручную,
		 * потому что init() не учитывает результат вызова
		 * @see Mage_Core_Model_Session_Abstract_Varien::getSkipEmptySessionCheck(),
		 * и вызывает start() только при отсутствии сессии:
			 if (!isset($_SESSION)) {
				 $this->start($sessionName);
			 }
		 * То есть, если сессия уже существует, то init() не вызовет start(),
		 * и вместо сессии @see Df_Core_Model_Session_Custom::getSessionIdCustom()
		 * будет обычная стандартная сессия.
		 */
		$this->start();
		/**
		 * Параметр $namespace — это ключ, по которому хранятся данные нашей сессии
		 * внутри общего ассоциативного массива $_SESSION.
		 * @see Mage_Core_Model_Session_Abstract_Varien::init():
		  		$this->_data = &$_SESSION[$namespace];
		 */
		$this->init($namespace = $this->getNamespace());
	}
}