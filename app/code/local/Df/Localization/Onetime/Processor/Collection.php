<?php
class Df_Localization_Onetime_Processor_Collection extends Df_Varien_Data_Collection_Singleton {
	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Processor::_C;}

	/**
	 * @override
	 * @return void
	 */
	protected function loadInternal() {
		foreach (rm_config_a('rm/design-theme-processors') as $processorId => $processorData) {
			/** @var string $processorId */
			/** @var array(string => string) $processorData */
			/** @var string $className */
			$processorData[Df_Localization_Onetime_Processor::P__ID] = $processorId;
			$className = dfa($processorData, 'processor', Df_Localization_Onetime_Processor::_C);
			/** @var Df_Localization_Onetime_Processor $processor */
			$processor = new $className($processorData);
			df_assert($processor instanceof Df_Localization_Onetime_Processor);
			$this->addItem($processor);
		}
		/**
		 * Вручную помечаем коллекцию как загруженную,
		 * чтобы при вызове @uses Df_Varien_Data_Collection::uasort()
		 * не попасть в бесконечную рекурсию.
		 */
		$this->_setIsLoaded(true);
		/** @uses compare() */
		$this->uasort('compare');
	}

	/** @return Df_Localization_Onetime_Processor_Collection */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * @var Df_Localization_Onetime_Processor $processor1
	 * @var Df_Localization_Onetime_Processor $processor2
	 * @return int
	 */
	private static function compare(
		Df_Localization_Onetime_Processor $processor1
		, Df_Localization_Onetime_Processor $processor2
	) {
		return $processor1->getSortWeight() - $processor2->getSortWeight();
	}
}