<?php
class Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
	extends Df_Core_Model_Abstract {
	/** @return Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType */
	public function addFields() {
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что addField() возвращает не $fieldset, а созданное поле.
		 */
		$this->getFieldset()
			->addField(
				$this->getFieldId('enabled')
				,'select'
				,array(
					'label'  => df_h()->cms()->__($this->getEnabledLabel())
					,'name' => $this->getFieldId('enabled')
					,'container_id' => $this->getFieldContainerId('enabled')
					,'values'  => $this->getOptionsYesNo()
					,'tabindex' => $this->getTabIndex()
					,'class' => 'df-field'
				)
			)
		;
		$this->getFieldset()
			->addField(
				$this->getFieldId('position')
				,'select'
				,array(
					'label' => df_h()->cms()->__($this->getPositionLabel())
					,'name' => $this->getFieldId('position')
					,'container_id' => $this->getFieldContainerId('position')
					,'values' => $this->getOptionsPosition()
					,'tabindex' => 1 + $this->getTabIndex()
					,'class' => $this->getCssClassesAsStringForDependentFields()
				)
			)
		;
		$this->getFieldset()
			->addField(
				$this->getFieldId('vertical_ordering')
				,'select'
				,array(
					'label' => df_h()->cms()->__($this->getVerticalOrderingLabel())
					,'note' =>
						df_h()->cms()->__(
							'Считается сверху вниз, среди всех блоков в данном месте'
						)
					,'name' => $this->getFieldId('vertical_ordering')
					,'values' => $this->getOptionsVerticalOrdering()
					,'container_id' => $this->getFieldContainerId('vertical_ordering')
					,'tabindex'  => 2 + $this->getTabIndex()
					,'class' => $this->getCssClassesAsStringForDependentFields()
				)
			)
		;		
		return $this;
	}

	/** @return string */
	private function getCssClassesAsStringForDependentFields() {
		return 'df-field ' . $this->getCssClassForDependency();
	}

	/** @return string */
	private function getCssClassForDependency() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 'df-depends--' . $this->getFieldId('enabled');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getEnabledLabel() {return $this->cfg(self::P__ENABLED__LABEL);}

	/**
	 * @param string $fieldShortName
	 * @return string
	 */
	private function getFieldContainerId($fieldShortName) {
		df_param_string($fieldShortName, 0);
		return implode('__', array('field', 'contents_menu', $this->getPageTypeId(), $fieldShortName));
	}

	/**
	 * @param string $fieldShortName
	 * @return string
	 */
	private function getFieldId($fieldShortName) {
		df_param_string($fieldShortName, 0);
		return implode('__', array('contents_menu', $this->getPageTypeId(), $fieldShortName));
	}

	/** @return Varien_Data_Form_Element_Fieldset */
	private function getFieldset() {return $this->cfg(self::P__FIELDSET);}

	/** @return string[][] */
	private function getOptionsPosition() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cms_Model_Config_Source_ContentsMenu_Position::s()->toOptionArray();
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getOptionsVerticalOrdering() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Admin_Model_Config_Source_SelectNumberFromDropdown::i(15)->toOptionArray()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getOptionsYesNo() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_reverse(df_mage()->adminhtml()->yesNo()->toOptionArray(), true);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPageTypeId() {return $this->cfg(self::P__PAGE_TYPE_ID);}

	/** @return string */
	private function getPositionLabel() {return $this->cfg(self::P__POSITION__LABEL);}

	/** @return int */
	private function getTabIndex() {return $this->cfg(self::P__TAB_INDEX);}

	/** @return string */
	private function getVerticalOrderingLabel() {return $this->cfg(self::P__VERTICAL_ORDERING__LABEL);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ENABLED__LABEL, self::V_STRING_NE)
			->_prop(self::P__FIELDSET, 'Varien_Data_Form_Element_Fieldset')
			->_prop(self::P__PAGE_TYPE_ID, self::V_STRING_NE)
			->_prop(self::P__POSITION__LABEL, self::V_STRING_NE)
			->_prop(self::P__TAB_INDEX, self::V_INT)
			->_prop(self::P__VERTICAL_ORDERING__LABEL, self::V_STRING_NE)
		;
	}
	const _CLASS = __CLASS__;
	const P__ENABLED__LABEL = 'enabled__label';
	const P__FIELDSET = 'fieldset';
	const P__PAGE_TYPE_ID = 'page_type_id';
	const P__POSITION__LABEL = 'position__label';
	const P__TAB_INDEX = 'tab_index';
	const P__VERTICAL_ORDERING__LABEL = 'vertical_ordering__label';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}