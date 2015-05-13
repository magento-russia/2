<?php
abstract class Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string */
	abstract protected function getEntityClass();

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return array(string => string)
	 */
	abstract protected function getTestMap(Mage_Core_Model_Abstract $entity);

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return bool
	 */
	public function isApplicable(Mage_Core_Model_Abstract $entity) {
		/** @var string $entityClass */
		$entityClass = $this->getEntityClass();
		df_assert($entity instanceof $entityClass);
		/** @var bool $result */
		$result = true;
		foreach ($this->getTestMap($entity) as $paramName => $expectedValue) {
			/** @var string $paramName */
			/** @var string $expectedValue */
			$result = $this->test($paramName, $expectedValue);
			if (!$result) {
				break;
			}
		}
		return $result;
	}

	/**
	 * @param string $paramName
	 * @param string $expectedValue
	 * @return bool
	 */
	private function test($paramName, $expectedValue) {
		/** @var string|null $paramValue */
		$paramValue = $this->getEntityParam($paramName);
		return !$paramValue || ($paramValue === $expectedValue);
	}

	const _CLASS = __CLASS__;
}