<?php
class Df_Vk_Model_Settings_Widget_Groups_Page extends Df_Core_Model_Settings {
	/** @return string */
	public function getColumn() {return $this->getString($this->getConfigKey('column'));}
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo($this->getConfigKey('show'));}
	/** @return int */
	public function getPosition() {return $this->getInteger($this->getConfigKey('vertical_ordering'));}
	/** @return string */
	public function getType() {
		return $this->_type;
	}
	/** @var string */
	private $_type;

	/**
	 * @param string $type
	 * @return Df_Vk_Model_Settings_Widget_Groups_Page
	 */
	public function setType($type) {
		df_param_string($type, 0);
		$this->_type = $type;
		return $this;
	}

	/**
	 * @param string $uniquePart
	 * @return string
	 */
	private function getConfigKey($uniquePart) {
		df_param_string($uniquePart, 0);
		return rm_config_key(
			'df_vk', 'groups'
			,implode(
				Df_Core_Const::T_CONFIG_WORD_SEPARATOR
				,array($this->getType(), 'page', $uniquePart)
			)
		);
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Vk_Model_Settings_Widget_Groups_Page
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}