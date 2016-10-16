<?php
/**
 * Базовый класс для настроечных граф, которые рисуют себя полностью самостоятельно,
 * перекрывая родительский метод @see Df_Adminhtml_Block_Config_Form_Field::render().
 *
 * @singleton
 * КЭШИРОВАНИЕ НАДО РЕАЛИЗОВЫВАТЬ КРАЙНЕ ОСТОРОЖНО!!!
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 *
 * Все классы, которые мы указываем в качестве «frontend_model» для интерфейсного поля,
 * в том числе и данный класс, используются как объекты-одиночки.
 * Конструируются «frontend_model» в методе
 * @used-by Mage_Adminhtml_Block_System_Config_Form::initFields():
	if ($element->frontend_model) {
		$fieldRenderer = Mage::getBlockSingleton((string)$element->frontend_model);
	} else {
		$fieldRenderer = $this->_defaultFieldRenderer;
	}
 * Обратите внимание, что для конструирования используется метод @uses Mage::getBlockSingleton()
 * Он-то как раз и обеспечивает одиночество объектов.
 *
 * Рисование полей происходит в методе
 * @see Mage_Adminhtml_Block_System_Config_Form_Field::render()
 * @see Df_Adminhtml_Block_Config_Form_Field::render()
		$html .= '<td class="value">';
		$html .= $this->_getElementHtml($element);
 */
abstract class Df_Admin_Block_Field_Custom extends Df_Adminhtml_Block_Config_Form_Field {
	/** @return string */
	abstract protected function getInstanceClass();

	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return df_abstract($this);}

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
		$this->_instance = Df_Admin_Config_Form_FieldInstance::create(
			$this, $element, $this->getInstanceClass()
		);
		return $this->_decorateRowHtml($element, $this->_toHtml());
	}

	/** @return Varien_Data_Form_Element_Abstract */
	protected function getElement() {return $this->getInstance()->getElement();}

	/**
	 * Таким нестандартным способом мы реализуем кэширование для данного класса.
	 * Стандартное кэширование здесь применять нельзя, потому что
	 * система использует объекты данного класса как одиночки.
	 * @return Df_Admin_Config_Form_FieldInstance
	 */
	protected function getInstance() {return $this->_instance;}

	/** @var Df_Admin_Config_Form_FieldInstance  */
	private $_instance;
	/** @used-by Df_Admin_Config_Form_FieldInstance::_construct() */

}