<?php
class Df_Core_Autoload extends Varien_Autoload {
	/**
	 * @param string $class
	 * @return mixed|null
	 */
	public function autoload($class) {
		if ($this->_collectClasses) {
			$this->_arrLoadedClasses[self::$_scope][]= $class;
		}
		/** @var string $classFile */
		$classFile =
			$this->_isIncludePathDefined
			? $class
			: str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)))
		;
		$classFile .= '.php';
		// начало заплатки
		ob_start();
		/** @var mixed|null $result */
		$result = include($classFile);
		/** @var string|bool $errorMessage */
		/**
		 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
		 * в другой точке программы при аналогичном вызове @uses ob_get_clean().
		 */
		$errorMessage = @ob_get_clean();
		if ($errorMessage) {
			if (1 === mb_strlen($errorMessage) && 0 === mb_strlen(df_t()->bomRemove($errorMessage))) {
				$errorMessage = sprintf('Дефект: файл «%s» начинается с символа BOM.', $classFile);
				Mage::log($errorMessage);
			}
			else {
				Mage::log(
					"При загрузке интерпретатором PHP программного файла «%s» произошёл сбой.\n"
					. "Сообщение интерпретатора: «%s»."
					,$classFile
					,$errorMessage
				);
			}
			echo $errorMessage;
		}
		// конец заплатки
		return $result;
	}

	/**
	 * @used-by Df_Core_Boot::initCore()
	 * @return void
	 */
	public static function register() {
		/** @var bool $r */
		static $r; if (!$r) {$r = spl_autoload_register(array(new self, 'autoload'), true, true);}
	}
}