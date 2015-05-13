<?php
/**
 * @method Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter getEvent()
 */
class Df_Directory_Model_Handler_ProcessRegionsAfterLoading extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		self::addTypeToNameStatic($this->getEvent()->getCollection());
		if (true !== ($this->getEvent()->getCollection()->getFlag(self::FLAG__PREVENT_REORDERING))) {
			$this->reorder();
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter::_CLASS;
	}

	/** @return Df_Directory_Model_Handler_ProcessRegionsAfterLoading */
	private function clearCollection() {
		/**
		 * Очищаем коллекцию, но не используем для этого clear(),
		 * потому что clear() переводит коллекцию в незагруженное состояние
		 */
		foreach ($this->getEvent()->getCollection() as $region) {
			/** @var Mage_Directory_Model_Region $region */
			$this->getEvent()->getCollection()->removeItemByKey($region->getId());
		}
		return $this;
	}

	/** @return array */
	private function getPriorityRegions() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $ids */
			$ids = array();
			/** @var Df_Directory_Model_Settings_Regions $settings */
			$settings = df_cfg()->directory()->regionsRu();
			for($i=1; $i <= Df_Directory_Model_Settings_Regions::NUM_PRIORITY_REGIONS; $i++) {
				$ids[]= $settings->getPriorityRegionIdAtPosition($i);
			}
			$ids = df_clean($ids, array(0));
			/** @var array $result */
			$result = array();
			foreach ($ids as $id) {
				/** @var int $id */
				df_assert_integer($id);
				$result[]= $this->getEvent()->getCollection()->getItemById($id);
			}
			$this->{__METHOD__} = df_clean($result);
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
		foreach ($this->getEvent()->getCollection() as $region) {
			/** @var Mage_Directory_Model_Region $region */
			$currentName = self::getRegionName($region);
			df_assert_string($currentName);
			$currentName = mb_strtoupper($currentName);
			df_assert_string($currentName);
			if (rm_contains($currentName, $name)) {
				$result = $region;
				break;
			}
		}
		return $result;
	}

	/** @return Df_Directory_Model_Handler_ProcessRegionsAfterLoading */
	private function reorder() {
		/** @var Mage_Directory_Model_Resource_Region_Collection|Mage_Directory_Model_Mysql4_Region_Collection $originalCollection */
		$originalCollection = clone $this->getEvent()->getCollection();
		df_h()->directory()->assert()->regionCollection($originalCollection);
		/** @var array $priorityRegions */
		$priorityRegions = $this->getPriorityRegions();
		df_assert_array($priorityRegions);
		$this->clearCollection();
		foreach ($priorityRegions as $priorityRegion) {
			/** @var Mage_Directory_Model_Region $priorityRegion */
			$originalCollection->removeItemByKey($priorityRegion->getId());
			$this->getEvent()->getCollection()->addItem($priorityRegion);
		}
		foreach ($originalCollection as $region) {
			/** @var Mage_Directory_Model_Region $region */
			$this->getEvent()->getCollection()->addItem($region);
		}
		return $this;
	}
	const _CLASS = __CLASS__;
	const FLAG__PREVENT_REORDERING = 'df_directory_handler_processRegionsAfterLoading.preventReordering';
	/**
	 * @param Varien_Data_Collection_Db $regions
	 */
	public static function addTypeToNameStatic(Varien_Data_Collection_Db $regions) {
		df_h()->directory()->assert()->regionCollection($regions);
		foreach ($regions as $region) {
			/** @var Mage_Directory_Model_Region $region */
			/** @var string $originalName */
			$originalName = self::getRegionName($region);
			df_assert_string($originalName);
			/** @var int $typeAsInteger */
			$typeAsInteger = rm_nat0($region->getData('df_type'));
			/** @var array $typesMap */
			$typesMap =
				array(
					1 => 'Республика'
					,2 => 'край'
					,3 => 'область'
					,5 => 'автономная область'
					,6 => 'автономный округ'
				)
			;
			df_assert_array($typesMap);
			/** @var $typeAsString $result */
			$typeAsString = df_a($typesMap, $typeAsInteger, '');
			df_assert_string($typeAsString);
			/** @var string $processedName */
			$processedName = rm_concat_clean(' ', $originalName, $typeAsString);
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