<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions
	extends Df_Core_Xml_Parser_Entity {
	/** @return string[] */
	public function getTargetTypes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv_parse($this->leaf('type'), ' ');
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
			/** @var Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract $conditions */
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
	 * @return Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract|null
	 */
	private function getConditions($type, $resultClass = null) {
		if (!isset($this->{__METHOD__}[$type])) {
			df_param_string_not_empty($type, 0);
			/** @var Df_Core_Sxe|null $e */
			$e = $this->child($type);
			$this->{__METHOD__}[$type] = df_n_set(
				!$e ? null : Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract::ic(
					$resultClass
					? $resultClass
					: 'Df_Localization_Onetime_Dictionary_Rule_Conditions_'
						. Df_Localization_Onetime_TypeManager::s()->getClassSuffixByType($type)
					, $e
				)
			);
		}
		return df_n_get($this->{__METHOD__}[$type]);
	}

	const _C = __CLASS__;

	/**
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_Localization_Onetime_Dictionary_Rule_Conditions
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}