<?php
class Df_Localization_Onetime_Dictionary_Rule extends Df_Core_Xml_Parser_Entity {
	/**
	 * @param string $type
	 * @return Df_Localization_Onetime_Dictionary_Rule_Actions
	 */
	public function getActions($type) {
		if (!isset($this->{__METHOD__}[$type])) {
			df_param_string_not_empty($type, 0);
			$this->{__METHOD__}[$type] =
				Df_Localization_Onetime_Dictionary_Rule_Actions::createConcrete(
					Df_Localization_Onetime_TypeManager::s()->getType($type)->getActionsClass()
					, $this->child('actions')
				)
			;
		}
		return $this->{__METHOD__}[$type];
	}

	/**
	 * @param string $type
	 * @return Mage_Core_Model_Abstract[]
	 */
	public function getApplicableEntities($type) {
		if (!isset($this->{__METHOD__}[$type])) {
			/** @var Mage_Core_Model_Abstract[] $result */
			$result = array();
			if ($this->getConditions()->isApplicableToType($type)) {
				foreach ($this->getAllEntities($type) as $entity) {
					/** @var Mage_Core_Model_Abstract $entity */
					if ($this->getConditions()->isApplicableToEntity($entity, $type)) {
						$result[]= $entity;
					}
				}
			}
			$this->{__METHOD__}[$type] = $result;
		}
		return $this->{__METHOD__}[$type];
	}

	/** @return Df_Localization_Onetime_Dictionary_Rule_Conditions */
	public function getConditions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Rule_Conditions::i(
				$this->child('conditions', $isRequired = true)
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $type
	 * @return Mage_Core_Model_Abstract[]|Traversable
	 */
	private function getAllEntities($type) {
		return Df_Localization_Onetime_TypeManager::s()->getType($type)->getAllEntities();
	}

	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Rules::itemClass()
	 * @used-by Df_Localization_Onetime_Processor_Rule::_construct()
	 */
	const _C = __CLASS__;
}