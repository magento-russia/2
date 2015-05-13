<?php
/**
 * Базовый класс для настроечных граф, которые рисуют себя полностью самостоятельно,
 * перекрывая родительский метод @see Df_Adminhtml_Block_System_Config_Form_Field::render().
 *
 * Обратите внимание, что система использует потмков данного класса как одиночек!
 * @see Mage_Adminhtml_Block_System_Config_Form::initFields():
	if ($element->frontend_model) {
		$fieldRenderer = Mage::getBlockSingleton((string)$element->frontend_model);
	} else {
		$fieldRenderer = $this->_defaultFieldRenderer;
	}
 * ПОЭТОМУ КЭШИРОВАНИЕ НАДО РЕАЛИЗОВЫВАТЬ КРАЙНЕ ОСТОРОЖНО!!!
 */
abstract class Df_Admin_Block_System_Config_Form_Field_Custom
	extends Df_Adminhtml_Block_System_Config_Form_Field {
	/** @return string */
	abstract protected function getInstanceClass();

	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return df_abstract(__METHOD__);}

	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		/**
		 * Таким нестандартным способом мы реализуем кэширование для данного класса.
		 * Стандартное кэширование здесь применять нельзя, потому что
		 * система использует объекты данного класса как одиночки.
		 */
		$this->_instance =
			Df_Admin_Model_Config_Form_FieldInstance::create(
				$this, $element, $this->getInstanceClass()
			)
		;
		return $this->_decorateRowHtml($element, $this->_toHtml());
	}

	/** @return Varien_Data_Form_Element_Abstract */
	protected function getElement() {return $this->getInstance()->getElement();}

	/**
	 * Таким нестандартным способом мы реализуем кэширование для данного класса.
	 * Стандартное кэширование здесь применять нельзя, потому что
	 * система использует объекты данного класса как одиночки.
	 * @return Df_Admin_Model_Config_Form_FieldInstance
	 */
	protected function getInstance() {return $this->_instance;}

	/** @var Df_Admin_Model_Config_Form_FieldInstance  */
	private $_instance;
	const _CLASS = __CLASS__;
}