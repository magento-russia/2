<?php
class Df_Localization_Model_Onetime_Processor_Collection extends Df_Varien_Data_Collection_Singleton {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Localization_Model_Onetime_Processor::_CLASS;}

	/**
	 * @override
	 * @return void
	 */
	protected function loadInternal() {
		/** @var array(string => string|array()) $configNodes */
		$configNodes = Mage::getConfig()->getNode('rm/design-theme-processors')->asCanonicalArray();
		foreach ($configNodes as $processorId => $processorData) {
			/** @var string $processorId */
			/** @var array(string => string) $processorData */
			/** @var string $className */
			$processorData[Df_Localization_Model_Onetime_Processor::P__ID] = $processorId;
			$className = df_a($processorData, 'processor', Df_Localization_Model_Onetime_Processor::_CLASS);
			/** @var Df_Localization_Model_Onetime_Processor $processor */
			$processor = new $className($processorData);
			df_assert($processor instanceof Df_Localization_Model_Onetime_Processor);
			$this->addItem($processor);
		}
		$this->sort();
	}

	/** @return void */
	private function sort() {
		/**
		 * @link http://php.net/manual/en/function.uasort.php#100485
		 * @link http://stackoverflow.com/a/6054036/254475
		 */
		/**
		 * Подавляем предупреждение «Array was modified by the user comparison function»
		 * @link http://stackoverflow.com/a/10985500/254475
		 */
		@uasort($this->_items, array('self', 'compare'));
	}

	/** @return Df_Localization_Model_Onetime_Processor_Collection */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * @var Df_Localization_Model_Onetime_Processor $processor1
	 * @var Df_Localization_Model_Onetime_Processor $processor2
	 * @return int
	 */
	private static function compare(
		Df_Localization_Model_Onetime_Processor $processor1
		, Df_Localization_Model_Onetime_Processor $processor2
	) {
		return $processor1->getSortWeight() - $processor2->getSortWeight();
	}
}