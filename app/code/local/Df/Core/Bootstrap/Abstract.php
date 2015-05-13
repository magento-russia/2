<?php
abstract class Df_Core_Bootstrap_Abstract {
	/** @return void */
	abstract public function init();

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function resource_get_tablename(Varien_Event_Observer $observer) {
		if (!$this->_alreadyInitialized && $this->needInitNow($observer->getData('table_name'))) {
			$this->_alreadyInitialized = true;
			$this->init();
		}
	}

	/**
	 * @param string $tableName
	 * @return bool
	 */
	private function needInitNow($tableName) {
		/** @var bool $result */
		$result = false;
		/**
		 * Мы бы рады инициализировать нашу библиотеку при загрузке таблицы «core_resource»,
		 * однако в тот момент система оповещений о событиях ещё не работает,
		 * и мы сюда всё равно не попадём.
		 *
		 * Обратите внимание, что проблема инициализации Российской сборки Magento
		 * при работе стороронних установочных скриптов
		 * удовлетворительно решается методом @see Df_Core_Helper_DataM::useDbCompatibleMode()
		 */
		if ('core_website' === $tableName) {
			$result = true;
		}
		else {
			if ('index_process' === $tableName) {
				/** @var bool */
				static $isItCompilationProcessFromCommandLine;
				if (!isset($isItCompilationProcessFromCommandLine)) {
					$isItCompilationProcessFromCommandLine = @class_exists('Mage_Shell_Compiler', false);
				}
				if ($isItCompilationProcessFromCommandLine) {
					$result = true;
				}
			}
		}
		return $result;
	}
	/** @var bool */
	private $_alreadyInitialized = false;
}