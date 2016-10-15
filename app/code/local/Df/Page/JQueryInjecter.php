<?php
abstract class Df_Page_JQueryInjecter extends Df_Core_Model {
	/** @return string */
	abstract protected function getConfigSuffix();

	/**
	 * @used-by process()
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	abstract protected function _process($format, array &$staticItems);

	/** @return string */
	protected function getPath() {return $this->getConfigValue('core');}

	/** @return string */
	protected function getPathMigrate() {return $this->getConfigValue('migrate');}

	/**
	 * @param string $key
	 * @return string
	 */
	private function getConfigValue($key) {
		if (!isset($this->{__METHOD__}[$key])) {
			df_param_string_not_empty($key, 0);
			$this->{__METHOD__}[$key] = df_leaf_sne(df_config_node(
				'df/jquery', $key, $this->getConfigSuffix()
			));
		}
		return $this->{__METHOD__}[$key];
	}

	/**
	 * @used-by p()
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	private function process($format, array &$staticItems) {
		/** @var string $result */
		$result = '';
		if (!$this->_injected && (false !== strpos($format, 'script'))) {
			$this->_injected = true;
			$result = $this->_process($format, $staticItems);
		}
		return $result;
	}

	/** @var bool */
	private $_injected = false;

	/**
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	public static function p($format, array &$staticItems) {
		/** @var bool $needLoad */
		static $needLoad; if (is_null($needLoad)) {$needLoad = df_cfg()->jquery()->needLoad();}
		/** @var Df_Page_JQueryInjecter $processor */
		static $processor;
		if ($needLoad && !$processor) {
			$processor =
				df_cfg()->jquery()->fromGoogle()
				? new Df_Page_JQueryInjecter_Google
				: new Df_Page_JQueryInjecter_Local
			;
		}
		return !$processor ? '' : $processor->process($format, $staticItems);
	}
}