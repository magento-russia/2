<?php
abstract class Df_Localization_Onetime_Type extends Df_Core_Model {
	/** @return Df_Dataflow_Model_Registry_Collection|Df_Dataflow_Model_Registry_MultiCollection|Mage_Core_Model_Abstract[] */
	abstract public function getAllEntities();

	/** @return string */
	public function getActionsClass() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $defaultResult */
			$defaultResult = Df_Localization_Onetime_Dictionary_Rule_Actions::_C;
			/** @var string $concreteResult */
			$concreteResult = $defaultResult . '_' . $this->getName();
			$this->{__METHOD__} = @class_exists($concreteResult) ? $concreteResult : $defaultResult;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getProcessorClass() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				'Df_Localization_Onetime_Processor_' . $this->getProcessorClassSuffix()
			;
			if (!@class_exists($this->{__METHOD__})) {
				df_error('Не могу найти класс процессора: %s.' . $this->{__METHOD__});
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return void
	 * @throws Df_Core_Exception_Batch
	 */
	public function saveModifiedEntities() {
		/** @var Df_Dataflow_Model_Registry_Collection|Df_Dataflow_Model_Registry_MultiCollection|Mage_Core_Model_Abstract[] $entities */
		$entities = $this->getAllEntities();
		if (
				($entities instanceof Df_Dataflow_Model_Registry_Collection)
			||
				($entities instanceof Df_Dataflow_Model_Registry_MultiCollection)
		) {
			$entities->save();
		}
		else {
			df_assert(is_array($entities) || ($entities instanceof Traversable));
			Df_Varien_Data_Collection::saveModified($entities);
		}
	}

	/** @return string */
	private function getName() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Df_Localization_Onetime_Type_AttributeSet => AttributeSet
			 * Df_Localization_Onetime_Type_Em_Megamenupro => Em_Megamenupro
			 */
			$this->{__METHOD__} = str_replace(__CLASS__ . '_', '', get_class($this));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getProcessorClassSuffix() {return $this->getName();}

	/**
	 * @used-by Df_Localization_Onetime_TypeManager::getType()
	 * @param string $classSuffix
	 * @return Df_Localization_Onetime_Type
	 */
	public static function ic($classSuffix) {return rm_ic(__CLASS__ . "_{$classSuffix}", __CLASS__);}
}