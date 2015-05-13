<?php
/**
 * Используется методом Df_Adminhtml_Block_System_Config_Form::initFields_1_4_0_1
 */
class Df_Adminhtml_Model_Config_Data_1401 extends Df_Adminhtml_Model_Config_Data {
	/**
	 * Extend config data with additional config data by specified path
	 *
	 * @param string $path Config path prefix
	 * @param bool $full Simple config structure or not
	 * @param array $oldConfig Config data to extend
	 * @return array
	 */
	public function extendConfig($path, $full = true, $oldConfig = array()) {
		$extended = $this->_getPathConfig($path, $full);
		if (is_array($oldConfig) && !empty($oldConfig)) {
			return $oldConfig + $extended;
		}
		return $extended;
	}

	/**
	 * Return formatted config data for specified path prefix
	 *
	 * @param string $path Config path prefix
	 * @param bool $full Simple config structure or not
	 * @return array
	 */
	protected function _getPathConfig($path, $full = true) {
		/** @var Df_Core_Model_Resource_Config_Data_Collection $configDataCollection */
		$configDataCollection = Df_Core_Model_Resource_Config_Data_Collection::i();
		$configDataCollection
			->addScopeFilter(
				$this->getScope()
				,$this->getScopeId()
				,$path
			)
		;
		$config = array();
		foreach ($configDataCollection as $data) {
			if ($full) {
				$config[$data->getPath()] = array(
					'path'	  => $data->getPath(),'value'	 => $data->getValue(),'config_id' => $data->getConfigId()
				);
			}
			else {
				$config[$data->getPath()] = $data->getValue();
			}
		}
		return $config;
	}

	/** @return Df_Adminhtml_Model_Config_Data_1401 */
	public static function i() {return new self;}
	/** @return Df_Adminhtml_Model_Config_Data_1401 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}