<?php
/**
 * Обратите внимание, что родительский класс — именно @see Mage_Core_Model_Resource_Setup
 * даже в Magento CE 1.4.
 * Также обратите внимание, что родительский класс не наследуется ни от какого другого класса
 * (ни от Mage_Core_Model_Abstract, ни от Varien_Object).
 */
class Df_Core_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Setup
	 */
	public function startSetup() {
		parent::startSetup();
		Df_Core_Boot::run();
		return $this;
	}

	/**
	 * Например: «app/code/local/Df/1C/sql/df_c1_setup/mysql4-upgrade-1.0.2-3.0.0.php»
	 * @param string $file
	 * @return void
	 */
	protected function p($file) {
		/**
		 * «mysql4-upgrade-1.0.2-3.0.0»
		 * @var string $basename
		 */
		$basename = basename($file, '.php');
		/**
		 * «3.0.0»
		 * @var string $versionTo
		 */
		$versionTo = df_last(explode('-', $basename));
		/**
		 * Подсмотрел алгоритм в @see Mage_Core_Model_Resource_Setup::__construct()
		 * «Df_C1»
		 * @var string $moduleName
		 */
		$moduleName = (string)$this->_resourceConfig->{'setup'}->{'module'};
		/**
		 * «Df_C1_Setup_3_0_0»
		 * @var string $class
		 */
		$class = df_cc_class_($moduleName, 'Setup', str_replace('.', '_', $versionTo));
		Df_Core_Setup::pc($class, $this);
	}

	/**
	 * @used-by Df_Core_Setup::_construct()
	 * @used-by Df_Directory_Setup_Processor_InstallRegions::_construct()
	 * @used-by Df_Directory_Setup_Processor_Region::_construct()
	 */

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self('df_core_setup');}
}