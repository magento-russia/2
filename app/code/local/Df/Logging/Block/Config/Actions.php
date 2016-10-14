<?php
/**
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
class Df_Logging_Block_Config_Actions extends Df_Adminhtml_Block_Config_Form_Field {
	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/logging/actions.phtml';}

	/** @return array(string => string) */
	public function getLabels() {return Df_Logging_Model_Config::s()->getLabels();}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function getIsChecked($key) {return Df_Logging_Model_Config::s()->isActive($key, true);}

	/**
	 * Render element html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$this->setNamePrefix($element->getName())->setHtmlId($element->getHtmlId());
		return $this->_toHtml();
	}
}