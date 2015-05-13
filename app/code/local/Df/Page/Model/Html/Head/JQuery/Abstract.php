<?php
abstract class Df_Page_Model_Html_Head_JQuery_Abstract extends Df_Core_Model_Abstract {
	/** @return string */
	abstract protected function getConfigSuffix();

	/**
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	abstract protected function processInternal($format, array &$staticItems);

	/**
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	public function process($format, array &$staticItems) {
		/** @var string $result */
		$result = '';
		if (!$this->_injected && (false !== strpos($format, 'script'))) {
			$this->_injected = true;
			$result = $this->processInternal($format, $staticItems);
		}
		return $result;
	}

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
			$this->{__METHOD__}[$key] = (string)(Mage::getConfig()->getNode(
				implode('/', array('df/jquery', $key, $this->getConfigSuffix()))
			));
		}
		return $this->{__METHOD__}[$key];
	}

	/** @var bool */
	private $_injected = false;
}