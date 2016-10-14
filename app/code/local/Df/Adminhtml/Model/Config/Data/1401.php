<?php
/**
 * Используется методом Df_Adminhtml_Block_Config_Form::initFields_1_4_0_1
 */
class Df_Adminhtml_Model_Config_Data_1401 extends Df_Adminhtml_Model_Config_Data {
	/**
	 * @override
	 * @param string $path
	 * @param bool $full [optional]
	 * @param array(string => string|array(string => string|int)) $oldConfig [optional]
	 * @return array(string => string|array(string => string|int))
	 */
	public function extendConfig($path, $full = true, $oldConfig = array()) {
		/** @var array(string => string|array(string => string|int)) $result */
		$result = $this->_getPathConfig($path, $full);
		if (is_array($oldConfig) && !empty($oldConfig)) {
			/**
			 * 2015-02-07
			 * Операция «+» для массивов используется в оригинале:
			 * @see Mage_Adminhtml_Model_Config_Data::extendConfig()
			 * Обратите внимание, что операция «+» игнорирует те элементы второго массива,
			 * ключи которого присутствуют в первом массиве:
			 * «The keys from the first array will be preserved.
			 * If an array key exists in both arrays,
			 * then the element from the first array will be used
			 * and the matching key's element from the second array will be ignored.»
			 * http://php.net/manual/function.array-merge.php
			 * Например:
			 * array(1,2,3) + array(3,4,5) вернёт array(1,2,3).
			 * http://3v4l.org/utnNp
			 */
			return $oldConfig + $result;
		}
		return $result;
	}

	/**
	 * @override
	 * @param string $path
	 * @param bool $full [optional]
	 * @return array(string => string|array(string => string|int))
	 */
	protected function _getPathConfig($path, $full = true) {
		/** @var Df_Core_Model_Resource_Config_Data_Collection $configDataCollection */
		$configDataCollection = Df_Core_Model_Config_Data::c();
		$configDataCollection->addScopeFilter($this->getScope(), $this->getScopeId(), $path);
		/** @var array(string => string|array(string => string|int)) $result */
		$result = array();
		foreach ($configDataCollection as $data) {
			/** @var Df_Core_Model_Config_Data $data */
			$result[$data->getPath()] =
				!$full
				? $data->getValue()
				: array(
					'path' => $data->getPath()
					, 'value' => $data->getValue()
					, 'config_id' => $data->getConfigId()
				)
			;
		}
		return $result;
	}

	/** @return Df_Adminhtml_Model_Config_Data_1401 */
	public static function i() {return new self;}
	/** @return Df_Adminhtml_Model_Config_Data_1401 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}