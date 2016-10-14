<?php
/** @method Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter getEvent() */
class Df_Directory_Model_Handler_ProcessRegionsAfterLoading extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		self::addTypeToNameStatic($this->getRegions());
		if (true !== ($this->getRegions()->getFlag(self::FLAG__PREVENT_REORDERING))) {
			$this->reorder();
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter::_C;}

	/**
	 * Очищаем коллекцию, но не используем для этого
	 * @see Varien_Data_Collection::clear(),
	 * потому что @see Varien_Data_Collection::clear()
	 * переводит коллекцию в незагруженное состояние.
	 * @uses Varien_Data_Collection::removeItemByKey()
	 *
	 * 2015-02-12
	 * Намеренно не используем @see Mage_Core_Model_Resource_Db_Collection_Abstract::getAllIds()
	 * потому что:
	 * 1) этот метод делает новый запрос к БД
	 * 2) этот метод дефектен:
	 * он вызывает $this->getConnection()->fetchCol($idsSelect)
	 * @uses Varien_Db_Adapter_Interface::fetchCol()
	 * без второго параметра $bind, и это может приводить к сбою:
	 * «Invalid parameter number: no parameters were bound, query was:
	 * SELECT `main_table`.`region_id` FROM `directory_country_region` AS `main_table`
	 * LEFT JOIN `directory_country_region_name` AS `rname`
	 * ON main_table.region_id = rname.region_id AND rname.locale = :region_locale
	 * WHERE (main_table.country_id = 'RU')».
	 * Этот дефект был устранён в методе @see Df_Core_Model_Resource_Collection::getAllIds(),
	 * однако делать лишний запрос к БД нам всё равно не нужно.
	 * @return void
	 */
	private function clearCollection() {
		array_map(array($this->getRegions(), 'removeItemByKey'), $this->getRegions()->walk('getId'));
	}

	/** @return array */
	private function getPriorityRegions() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $ids */
			$ids = array();
			foreach (array('RU', 'KZ', 'UA') as $iso2) {
				/** @var array $iso2 */
				$ids = array_merge($ids,
					df_cfg()->directory()->getRegions($iso2)->getPriorityRegionIds()
				);
			}
			$this->{__METHOD__} =
				array_filter(dfa_select_ordered($this->getRegions()->getItems(), array_filter($ids)))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $name
	 * @return Mage_Directory_Model_Region|null
	 */
	private function getRegionByName($name) {
		df_param_string($name, 0);
		/** @var Mage_Directory_Model_Region|null $result */
		$result = null;
		$name = mb_strtoupper($name);
		df_assert_string($name);
		foreach ($this->getRegions() as $region) {
			/** @var Mage_Directory_Model_Region $region */
			$currentName = self::getRegionName($region);
			df_assert_string($currentName);
			$currentName = mb_strtoupper($currentName);
			df_assert_string($currentName);
			if (df_contains($currentName, $name)) {
				$result = $region;
				break;
			}
		}
		return $result;
	}

	/** @return Mage_Directory_Model_Resource_Region_Collection|Df_Directory_Model_Resource_Region_Collection */
	private function getRegions() {return $this->getEvent()->getCollection();}

	/** @return void */
	private function reorder() {
		/** @var Mage_Directory_Model_Resource_Region_Collection|Df_Directory_Model_Resource_Region_Collection $originalCollection */
		$originalCollection = clone $this->getRegions();
		/** @var array $priorityRegions */
		$priorityRegions = $this->getPriorityRegions();
		df_assert_array($priorityRegions);
		$this->clearCollection();
		foreach ($priorityRegions as $priorityRegion) {
			/** @var Mage_Directory_Model_Region $priorityRegion */
			$originalCollection->removeItemByKey($priorityRegion->getId());
			$this->getRegions()->addItem($priorityRegion);
		}
		/** @uses Mage_Directory_Model_Resource_Region_Collection::addItem() */
		$originalCollection->walk(array($this->getRegions(), 'addItem'));
	}
	/** @used-by Df_Directory_Observer::core_collection_abstract_load_after() */
	const _C = __CLASS__;
	/**
	 * @used-by handle()
	 * @used-by Df_Directory_Config_Source_Region_Kazakhstan::getAsOptionArray()
	 * @used-by Df_Directory_Config_Source_Region_Russia::getAsOptionArray()
	 * @used-by Df_Directory_Config_Source_Region_Ukraine::getAsOptionArray()
	 */
	const FLAG__PREVENT_REORDERING = 'df_directory_handler_processRegionsAfterLoading.preventReordering';
	/**
	 * @param Varien_Data_Collection_Db $regions
	 */
	public static function addTypeToNameStatic(Varien_Data_Collection_Db $regions) {
		foreach ($regions as $region) {
			/** @var Mage_Directory_Model_Region $region */
			/** @var string $originalName */
			$originalName = self::getRegionName($region);
			df_assert_string($originalName);
			/** @var int $typeAsInteger */
			$typeAsInteger = df_nat0($region->getData('df_type'));
			/** @var array $typesMap */
			$typesMap = array(
				1 => 'Республика'
				,2 => 'край'
				,3 => 'область'
				,5 => 'автономная область'
				,6 => 'автономный округ'
			);
			df_assert_array($typesMap);
			/** @var $typeAsString $result */
			$typeAsString = dfa($typesMap, $typeAsInteger, '');
			df_assert_string($typeAsString);
			/** @var string $processedName */
			$processedName = df_ccc(' ', $originalName, $typeAsString);
			$region->addData(array(
				Df_Directory_Model_Region::P__NAME => $processedName
				,Df_Directory_Model_Region::P__DEFAULT_NAME => $processedName
				,Df_Directory_Model_Region::P__ORIGINAL_NAME => $originalName
			));
		}
	}
	/**
	 * @param Mage_Directory_Model_Region $region
	 * @return string
	 */
	public static function getRegionName(Mage_Directory_Model_Region $region) {
		/** @var string $result */
		$result = $region->getData('name');
		return $result ? $result : $region->getData(Df_Directory_Model_Region::P__DEFAULT_NAME);
	}
}