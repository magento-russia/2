<?php
class Df_Localization_Onetime_Processor_Rule extends Df_Core_Model {
	/** @return void */
	public function process() {
		foreach ($this->getRule()->getConditions()->getTargetTypes() as $type) {
			/** @var string $type */
			foreach ($this->getRule()->getApplicableEntities($type) as $entity) {
				/** @var Mage_Core_Model_Abstract $entity */
				Df_Localization_Onetime_Processor_Entity::processStatic(
					$this->getProcessorClass($type), $entity, $this->getRule()->getActions($type)
				);
			}
		}
	}

	/**
	 * @param string $type
	 * @return string
	 */
	private function getProcessorClass($type) {
		return Df_Localization_Onetime_TypeManager::s()->getType($type)->getProcessorClass();
	}

	/** @return Df_Localization_Onetime_Dictionary_Rule */
	private function getRule() {return $this->cfg(self::$P__RULE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__RULE, Df_Localization_Onetime_Dictionary_Rule::class);
	}
	/** @var string */
	protected static $P__RULE = 'rule';

	/**
	 * @param Df_Localization_Onetime_Dictionary_Rule $rule
	 * @return Df_Localization_Onetime_Processor_Rule
	 */
	public static function i(Df_Localization_Onetime_Dictionary_Rule $rule) {
		return new self(array(self::$P__RULE => $rule));
	}
}