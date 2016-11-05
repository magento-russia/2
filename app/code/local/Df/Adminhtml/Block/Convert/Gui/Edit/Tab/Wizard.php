<?php
class Df_Adminhtml_Block_Convert_Gui_Edit_Tab_Wizard
	extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard {
	/**
	 * Цель перекрытия —
	 * Исправить работу Field Mapping в интерфейсе Dataflow
	 * («Система» → «Импорт/Экспорт» → «Стандартные программы»).
	 * В Magento CE элемент управления Field Mapping работает не всегда корректно.
	 * @override
	 * @param string $entityType
	 * @return array(int => string)
	 */
	public function getMappings($entityType) {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded = df_cfgr()->dataflow()->patches()->fixFieldMappingGui();
		}
		return $patchNeeded ? $this->getMappingsDf($entityType) : parent::getMappings($entityType);
	}

	/**
	 * @param string $entityType
	 * @return array(int => string)
	 */
	private function getMappingsDf($entityType) {
		/** @var array(int => string) $mappings */
		$mappings = parent::getMappings($entityType);
		df_assert_array($mappings);
		/** @var array $result */
		$result = [];
		foreach ($mappings as $ordering => $fieldName) {
			/** @var int $ordering */
			df_assert_integer($ordering);
			/** @var string $fieldName */
			df_assert_string_not_empty($fieldName);
			/** @var string|null $valueInFile */
			$valueInFile = $this->getValue('gui_data/map/'.$entityType.'/file/'.$ordering);
			if ($valueInFile) {
				$result[$ordering] = $fieldName;
			}
		}
		return $result;
	}
}