<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Conditions
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string|null */
	public function getTitleNew() {return $this->getEntityParam('new_title');}

	/** @return string[] */
	public function getTargetTypes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_parse_csv($this->getEntityParam('type'), ' ');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @param string $type
	 * @return bool
	 */
	public function isApplicableToEntity(Mage_Core_Model_Abstract $entity, $type) {
		/** @var bool $result */
		$result = $this->isApplicableToType($type);
		if ($result) {
			/** @var Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract $conditions */
			$conditions = $this->getConditions($type);
			$result = !$conditions || $conditions->isApplicable($entity);
		}
		return $result;
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public function isApplicableToType($type) {return in_array($type, $this->getTargetTypes());}

	/**
	 * @param string $type
	 * @param string|null $resultClass [optional]
	 * @return Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract|null
	 */
	private function getConditions($type, $resultClass = null) {
		if (!isset($this->{__METHOD__}[$type])) {
			df_param_string_not_empty($type, 0);
			/** @var Df_Varien_Simplexml_Element|null $e */
			$e = $this->getChildSingleton($type);
			/** @var Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract|null $result */
			if (!$e) {
				$result = null;
			}
			else {
				if (!$resultClass) {
					$resultClass =
						'Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_'
						. Df_Localization_Model_Onetime_TypeManager::s()->getClassSuffixByType($type)
					;
				}
				$result = new $resultClass(array(
					Df_Localization_Model_Onetime_Dictionary_Rule_Conditions::P__SIMPLE_XML => $e)
				);
				df_assert(
						$result
					instanceof
						Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract
				);
			}
			$this->{__METHOD__}[$type] = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__}[$type]);
	}

	const _CLASS = __CLASS__;

	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $simpleXml
	 * @return Df_Localization_Model_Onetime_Dictionary_Rule_Conditions
	 */
	public static function i(Df_Varien_Simplexml_Element $simpleXml) {
		return new self(array(self::P__SIMPLE_XML => $simpleXml));
	}
}