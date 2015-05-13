<?php
/**
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 */
class Df_Admin_Block_System_Config_Form_Field
	extends Mage_Adminhtml_Block_Abstract
	implements Varien_Data_Form_Element_Renderer_Interface {

	/**
	 * @override
	 * @return int|null
	 */
	public function getCacheLifetime() {
		// отключаем кэширование
		return Df_Core_Block_Template::CACHE_LIFETIME_DISABLE;
	}

	/** @return Df_Admin_Model_Form_Element */
	public function getElement() {return $this->_element;}

	/** @return string */
	public function getFeatureState() {return $this->getElement()->getFeatureInfo()->getState();}

	/** @return string */
	public function getFeatureStateText() {
		return $this->getElement()->getFeatureInfo()->getStateText();
	}

	/** @return string */
	public function getFeatureTitle() {return $this->getElement()->getFeatureInfo()->getTitle();}

	/** @return bool */
	public function isFeatureDisabledForAllStoresInCurrentScope() {
		return $this->getElement()->getFeatureInfo()->isDisabledForAllStoresInCurrentScope();
	}

	/** @return bool */
	public function isFeatureSpecified() {
		/**
		 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
		 * для вывода каждого поля!
		 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
		 * Поэтому в объектах данного класса нельзя кешировать информацию,
		 * которая индивидуальна для поля конкретного поля!
		 */
		return !!$this->getElement()->getFeatureCode();
	}

	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		return
			$this
				->setElement($this->wrap($element))
				->setTemplate('df/admin/system/config/form/field.phtml')
				->toHtml()
		;
	}

	/**
	 * @param Df_Admin_Model_Form_Element $element
	 * @return Df_Admin_Block_System_Config_Form_Field
	 */
	public function setElement(Df_Admin_Model_Form_Element $element) {
		$this->_element = $element;
		return $this;
	}
	/** @var  Df_Admin_Model_Form_Element */
	private $_element;

	/**
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return Df_Admin_Model_Form_Element
	 */
	private function wrap(Varien_Data_Form_Element_Abstract $element) {
		return
			Df_Admin_Model_Form_Element::i(
				array(
					Df_Admin_Model_Form_Element::P__WRAPPED_ELEMENT => $element
				)
			)
		;
	}
}