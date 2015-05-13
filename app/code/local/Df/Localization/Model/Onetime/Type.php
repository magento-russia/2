<?php
abstract class Df_Localization_Model_Onetime_Type extends Df_Core_Model_Abstract {
	/** @return Df_Dataflow_Model_Registry_Collection|Df_Dataflow_Model_Registry_MultiCollection|Mage_Core_Model_Abstract[] */
	abstract public function getAllEntities();

	/** @return string */
	public function getActionsClass() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $defaultResult */
			$defaultResult = Df_Localization_Model_Onetime_Dictionary_Rule_Actions::_CLASS;
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
				'Df_Localization_Model_Onetime_Processor_' . $this->getProcessorClassSuffix()
			;
			if (!@class_exists($this->{__METHOD__})) {
				df_error('Не могу найти класс процессора: %s.' . $this->{__METHOD__});
			}
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	public function saveModifiedEntities() {
		if (
				($this->getAllEntities() instanceof Df_Dataflow_Model_Registry_Collection)
			||
				($this->getAllEntities() instanceof Df_Dataflow_Model_Registry_MultiCollection)
		) {
			$this->getAllEntities()->save();
		}
		else {
			df_assert(
					is_array($this->getAllEntities())
				||
					($this->getAllEntities() instanceof Traversable)
			);
			Df_Varien_Data_Collection::saveModified($this->getAllEntities());
		}
	}

	/** @return string */
	private function getName() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Df_Localization_Model_Onetime_Type_AttributeSet => AttributeSet
			 * Df_Localization_Model_Onetime_Type_Em_Megamenupro => Em_Megamenupro
			 */
			$this->{__METHOD__} = str_replace(__CLASS__ . '_', '', get_class($this));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getProcessorClassSuffix() {return $this->getName();}
}