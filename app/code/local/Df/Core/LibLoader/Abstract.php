<?php
abstract class Df_Core_LibLoader_Abstract {
	/** @return Df_Core_LibLoader_Abstract */
	public function __construct() {
		$this->includeScripts();
		set_include_path(
			/**
			 * PATH_SEPARATOR — это символ «;» для Windows и «:» для Unix,
			 * он разделяет пути к известным интерпретатору PHP папкам со скриптами.
			 * @link http://stackoverflow.com/questions/9769052/why-is-there-a-path-separator-constant
			 */
			get_include_path() . PATH_SEPARATOR . $this->getLibDir()
		);
		return $this;
	}

	/** @return Df_Core_LibLoader_Abstract */
	public function restoreErrorReporting() {
		if (isset($this->_errorReporting)) {
			error_reporting($this->_errorReporting);
		}
		return $this;
	}

	/** @return Df_Core_LibLoader_Abstract */
	public function setCompatibleErrorReporting() {
		$this->_errorReporting = error_reporting();
		/**
		 * Обратите внимание, что ошибочно использовать ^ вместо &~,
		 * потому что ^ — это побитовое XOR,
		 * и если предыдущее значение error_reporting не содержало getIncompatibleErrorLevels(),
		 * то вызов с оператором ^ наоборот добавит в error_reporting getIncompatibleErrorLevels().
		 */
		error_reporting($this->_errorReporting &~ $this->getIncompatibleErrorLevels());
		return $this;
	}
	/** @var int */
	private $_errorReporting;

	/** @return int */
	protected function getIncompatibleErrorLevels() {return 0;}

	/** @return string[] */
	protected function getScriptsToInclude() {return array();}

	/** @return string */
	private function getLibDir() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				is_dir($this->getLibDirCompiled())
				? $this->getLibDirCompiled()
				: $this->getLibDirStandard()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLibDirCompiled() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * @see df_concat_path здесь использовать ещё нельзя,
			 * потому что библиотеки Российской сборки ещё не загружены
			 */
			$this->{__METHOD__} =
				!defined('COMPILER_INCLUDE_PATH')
				? ''
				: COMPILER_INCLUDE_PATH . DS . $this->getLibDirLocal()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает, например, строку «Df/Core/lib»
	 * @return string
	 */
	private function getLibDirLocal() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(DS, array_slice(explode('_', get_class($this)), 0, 2)) . DS . 'lib';
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLibDirStandard() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * @see df_concat_path() здесь использовать ещё нельзя,
			 * потому что библиотеки Российской сборки ещё не загружены
			 */
			$this->{__METHOD__} = implode(DS, array(BP, 'app', 'code', 'local', $this->getLibDirLocal()));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_LibLoader_Abstract */
	private function includeScripts() {
		$this->setCompatibleErrorReporting();
		/** @var string $libPath */
		$libPath = $this->getLibDir() . DS;
		foreach ($this->getScriptsToInclude() as $script) {
			/** @var string $script */
			require_once $libPath . $script . '.php';
		}
		$this->restoreErrorReporting();
		return $this;
	}
}