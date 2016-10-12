<?php
class Df_Admin_Model_Form_Element extends Df_Core_Model {
	/** @return string */
	public function getCheckboxLabel() {
		return
				$this->getWrappedElement()->getData(
					self::FORM_ELEMENT__ATTRIBUTE_CAN_USE_WEBSITE_VALUE
				)
			?
				df_mage()->adminhtml()->__(self::T_USE_WEBSITE)
			:
				(
						$this->getWrappedElement()->getData(
							self::FORM_ELEMENT__ATTRIBUTE_CAN_USE_DEFAULT_VALUE
						)
					?
						df_mage()->adminhtml()->__(self::T_USE_DEFAULT)
					:
						''
				)
		;
	}

	/** @return string */
	public function getComment() {return df_nts($this->getWrappedElement()->getData('comment'));}

	/** @return string */
	public function getDefaultText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getOptions()
				? $this->getDefaultValueFromOptions()
				: $this->getWrappedElement()->getData(self::FORM_ELEMENT__ATTRIBUTE_DEFAULT_VALUE)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Licensor_Model_Feature */
	public function getFeature() {return df_feature($this->getFeatureCode());}

	/** @return string|null */
	public function getFeatureCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				rm_empty_to_null(
					df_trim(df_a($this->getFieldConfig()->asArray(), self::FIELD_ATTRIBUTE_FEATURE))
				)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Licensor_Model_Feature_Info */
	public function getFeatureInfo() {return df_h()->licensor()->getFeatureInfo($this->getFeature());}

	/** @return string */
	public function getHint() {
		return $this->getWrappedElement()->getData(self::FORM_ELEMENT__ATTRIBUTE_HIHT);
	}

	/** @return bool */
	public function getInherit() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_bool($this->getWrappedElement()->getDataUsingMethod(
					Df_Varien_Data_Form_Element_Abstract::P__INHERIT
				))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getNamePrefix() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				preg_replace(
					'#\[value\](\[\])?$#'
					,''
					,$this->getWrappedElement()->getName()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getScopeLabel() {
		return
			$this->getWrappedElement()->getData(self::FORM_ELEMENT__ATTRIBUTE_SCOPE)
			? $this->getWrappedElement()->getData(self::FORM_ELEMENT__ATTRIBUTE_SCOPE_LABEL)
			: ''
		;
	}

	/** @return Mage_Core_Model_Config_Element */
	protected function getFieldConfig() {
		return $this->getWrappedElement()->getData(self::FIELD_CONFIG);
	}

	/** @return string */
	private function getDefaultValueFromOptions() {
		$defTextArr = array();
		foreach ($this->getOptions() as $k=>$v) {
			/** @var string $k */
			/** @var array $v */

			if ($this->isMultiple()) {
				if (is_array($v['value']) && in_array($k, $v['value'])) {
					$defTextArr[]= $v['label'];
				}
			}
			else if (
					$v['value']
				===
					$this->getWrappedElement()
						->getDataUsingMethod(
							Df_Varien_Data_Form_Element_Abstract::P__DEFAULT_VALUE
						)
			) {
				$defTextArr[]= $v['label'];
				break;
			}
		}
		return df_concat_enum($defTextArr);
	}

	/** @return array */
	private function getOptions() {
		/** @var array $result */
		$result = $this->getWrappedElement()
			->getDataUsingMethod(Df_Varien_Data_Form_Element_Abstract::P__VALUES)
		;
		/**
		 * Varien_Data_Form_Element_Abstract::getValues() вполне может вернуть null
		 */
		if (is_null($result)) {
			$result = array();
		}
		df_result_array($result);
		return $result;
	}

	/** @return string */
	public function getHtml() {
		/** @var string $result */
		$result = $this->getWrappedElement()->getElementHtml();
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getId() {return $this->getWrappedElement()->getHtmlId();}

	/** @return string */
	public function getLabel() {return df_nts($this->getWrappedElement()->getData('label'));}

	/** @return Varien_Data_Form_Element_Abstract */
	public function getWrappedElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg(self::P__WRAPPED_ELEMENT);
			df_assert($this->{__METHOD__});
			if (
					$this->{__METHOD__}->getDataUsingMethod(
						Df_Varien_Data_Form_Element_Abstract::P__CAN_USE_WEBSITE_VALUE
					)
				&&
					$this->{__METHOD__}->getDataUsingMethod(
						Df_Varien_Data_Form_Element_Abstract::P__CAN_USE_DEFAULT_VALUE
					)
				&&
					$this->{__METHOD__}->getDataUsingMethod(
						Df_Varien_Data_Form_Element_Abstract::P__INHERIT
					)
			) {
				$this->{__METHOD__}
					->setDataUsingMethod(
						Df_Varien_Data_Form_Element_Abstract::P__DISABLED, true
					)
				;
			}
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function needToAddInheritBox() {
		$result =
				$this->getWrappedElement()
					->getDataUsingMethod(
						Df_Varien_Data_Form_Element_Abstract::P__CAN_USE_WEBSITE_VALUE
					)
			&&
				$this->getWrappedElement()
					->getDataUsingMethod(
						Df_Varien_Data_Form_Element_Abstract::P__CAN_USE_DEFAULT_VALUE
					)
		;
		return $result;
	}

	/** @return bool */
	private function isMultiple() {
		$result =
			(
					self::MULTIPLE
				===
					$this->getWrappedElement()
						->getDataUsingMethod(
							Df_Varien_Data_Form_Element_Abstract::P__EXT_TYPE
						)
			)
		;
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__WRAPPED_ELEMENT, self::P__WRAPPED_ELEMENT_TYPE);
	}
	const _CLASS = __CLASS__;
	const FIELD_ATTRIBUTE_FEATURE = 'df_feature';
	const FIELD_CONFIG = 'field_config';
	const FORM_ELEMENT__ATTRIBUTE_CAN_USE_DEFAULT_VALUE = 'сan_use_default_value';
	const FORM_ELEMENT__ATTRIBUTE_CAN_USE_WEBSITE_VALUE = 'can_use_website_value';
	const FORM_ELEMENT__ATTRIBUTE_DEFAULT_VALUE = 'default_value';
	const FORM_ELEMENT__ATTRIBUTE_HIHT = 'hint';
	const FORM_ELEMENT__ATTRIBUTE_SCOPE = 'scope';
	const FORM_ELEMENT__ATTRIBUTE_SCOPE_LABEL = 'scope_label';
	const MULTIPLE = 'multiple';
	const P__WRAPPED_ELEMENT = 'wrappedElement';
	const P__WRAPPED_ELEMENT_TYPE = 'Varien_Data_Form_Element_Abstract';
	const T_USE_DEFAULT= 'Use Default';
	const T_USE_WEBSITE = 'Use Website';

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Admin_Model_Form_Element
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}