<?php
class Df_Core_Autoload extends Varien_Autoload {
	/**
	 * @param string $class
	 * @return mixed|null
	 */
	public function autoload($class)
	{
		if ($this->_collectClasses) {
			$this->_arrLoadedClasses[self::$_scope][]= $class;
		}
		if ($this->_isIncludePathDefined) {
			$classFile = $class;
		} else {
			$classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)));
		}
		$classFile.= '.php';
		//echo $classFile;die();
		/********************************
		 * Начало заплатки
		 *******************************/
		ob_start();
		/** @var mixed|null $result */
		$result = include($classFile);
		/** @var string|bool $errorMessage */
		/**
		 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
		 * в другой точке программы при аналогичном вызове @see ob_get_clean.
		 */
		$errorMessage = @ob_get_clean();
		if ((false !== $errorMessage) && ('' !== $errorMessage)) {
			/** @var bool $isItBom */
			$isItBom = false;
			/** @var int $errorMessageLength */
			$errorMessageLength = mb_strlen($errorMessage);
			if (1 === $errorMessageLength) {
				if (0 === mb_strlen(df_text()->bomRemove($errorMessage))) {
					$isItBom = true;
				}
			}
			if (!$isItBom) {
				Mage::log(
					"При загрузке интерпретатором PHP программного файла %s произошёл сбой.\n"
					. "Сообщение интерпретатора: «%s»."
					,$classFile
					,$errorMessage
				);
				echo $errorMessage;
			}
			else {
				Mage::log('Дефект: файл начинается с символа BOM: ' . $classFile);
			}
		}
		/********************************
		 * Конец заплатки
		 *******************************/
		return $result;
	}

	/**
	 * Singleton pattern implementation
	 * @return Varien_Autoload
	 */
	static public function instance() {
		if (!self::$_instance) {
			self::$_instance = new Df_Core_Autoload();
		}
		return self::$_instance;
	}

	/**
	 * @var Varien_Autoload
	 */
	static protected $_instance;

	/** @return void */
	static public function register()
	{
		if (!self::$_registered) {
			spl_autoload_register(array(self::instance(), 'autoload'), true, true);
			self::$_registered = true;
		}
	}
	/** @var bool */
	static protected $_registered = false;
}