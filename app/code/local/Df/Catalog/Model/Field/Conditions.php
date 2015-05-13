<?php
class Df_Catalog_Model_Field_Conditions extends Df_Core_Model_Abstract {
	/** @return string */
	public function getHtml() {return $this->createForm()->toHtml();}

	/**
	 * @param Varien_Data_Form_Element_Fieldset $fieldset
	 * @return Varien_Data_Form_Element_Abstract
	 */
	private function createFieldConditions(Varien_Data_Form_Element_Fieldset $fieldset) {
		/** @var Varien_Data_Form_Element_Abstract $result */
		$result = $fieldset->addField('conditions', 'text', array(
			'name' => 'conditions'
			,'label' => Mage::helper('catalogrule')->__('Conditions')
			,'title' => Mage::helper('catalogrule')->__('Conditions')
			,'required' => true
		));
		/** @var Mage_Rule_Block_Conditions $blockRuleConditions */
		$blockRuleConditions = Mage::getBlockSingleton('rule/conditions');
		$result->setData('rule', $this->getRule());
		$result->setRenderer($blockRuleConditions);
		return $result;
	}

	/**
	 * @param Varien_Data_Form $form
	 * @return Varien_Data_Form_Element_Fieldset
	 */
	private function createFieldset(Varien_Data_Form $form) {
		/** @var Varien_Data_Form_Element_Fieldset $result */
		$result = $form->addFieldset('conditions_fieldset', array());
		$result->setRenderer($this->createRendererFieldset());
		$this->createFieldConditions($result);
		return $result;
	}

	/** @return Varien_Data_Form */
	private function createForm() {
		/** @var Varien_Data_Form $result */
		$result = new Varien_Data_Form();
		$result->setData('html_id_prefix', 'rule_');
		$this->createFieldset($result);
		return $result;
	}

	/** @return Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset */
	private function createRendererFieldset() {
		/** @var Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset $result */
		$result = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset');
		$result->setTemplate('df/catalog/conditions.phtml');
		$result->setData(
			'new_child_url'
			,$this->getBlock()->getUrl(
				'*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'
			)
		);
		return $result;
	}

	/** @return Mage_Core_Block_Abstract */
	private function getBlock() {return $this->cfg(self::$P__BLOCK);}

	/** @return Varien_Data_Form_Element_Abstract */
	private function getElement() {return $this->cfg(self::$P__ELEMENT);}

	/** @return Mage_CatalogRule_Model_Rule */
	private function getRule() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_CatalogRule_Model_Rule $result */
			$result = df_model('catalogrule/rule');
			if ($this->getRuleId()) {
				$result->load($this->getRuleId());
			}
			$result->getConditions()->setJsFormObject('rule_conditions_fieldset');
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getRuleId() {return rm_nat0($this->getElement()->getData('value'));}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__BLOCK, 'Mage_Core_Block_Abstract')
			->_prop(self::$P__ELEMENT, 'Varien_Data_Form_Element_Abstract')
		;
	}
	const _CLASS = __CLASS__;
	/** @var string */
	private static $P__BLOCK = 'block';
	/** @var string */
	private static $P__ELEMENT = 'element';
	/**
	 * @static
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @param Mage_Core_Block_Abstract $block
	 * @return Df_Catalog_Model_Field_Conditions
	 */
	public static function i(
		Varien_Data_Form_Element_Abstract $element, Mage_Core_Block_Abstract $block
	) {
		return new self(array(self::$P__ELEMENT => $element, self::$P__BLOCK => $block));
	}
}