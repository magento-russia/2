<?php
class Df_Core_Autoload extends Varien_Autoload {
	/**
	 * @param string $c
	 * @return mixed|null
	 */
	public function autoload($c) {
		if ($this->_collectClasses) {
			$this->_arrLoadedClasses[self::$_scope][]= $c;
		}
		/**
		 * 2018-06-07
		 * 1) "«Warning: include(Phpseclib\Crypt\Base.php): failed to open stream:
		 * No such file or directory app/code/local/Df/Core/Autoload.php on line 24»
		 * при использовании PHP 7.2 и `mcrypt_compat`":
		 * https://github.com/magento-russia/2/issues/15
		 * 2) "Check if an include (or require) exists":
		 * https://stackoverflow.com/a/13118081
		 * http://php.net/manual/function.stream-resolve-include-path.php
		 */
		if ($r =
			!function_exists('stream_resolve_include_path')
			|| stream_resolve_include_path(
				$f = ($this->_isIncludePathDefined ? $c :
					str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $c)))
				) . '.php'
			)
		) {  /** @var string $f */
			ob_start();
			$r = include $f;
			/**
			 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
			 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
			 * в другой точке программы при аналогичном вызове @see ob_get_clean.
			 */
			if (false !== ($m = @ob_get_clean()) && '' !== $m) { /** @var string|bool $m */
				if (1 === mb_strlen($m) && 0 === mb_strlen(df_text()->bomRemove($m))) {
					Mage::log("Дефект: файл начинается с символа BOM: $f");
				}
				else {
					Mage::log(
						"При загрузке интерпретатором PHP программного файла $f произошёл сбой.\n"
						. "Сообщение интерпретатора: «{$m}»."
					);
					echo $m;
				}
			}
		}
		return $r;
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