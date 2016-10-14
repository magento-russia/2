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
abstract class Df_Admin_Block_Field_Button extends Df_Adminhtml_Block_Config_Form_Field {
	/**
	 * @see Df_Admin_Block_Field_Button_Action::url()
	 * @see Df_YandexMarket_Block_Api_GetConfirmationCode::url()
	 * Кэшировать результат обычным образом нельзя!
	 * @used-by df/admin/field/button.phtml
	 * 2015-04-16
	 * Нельзя называть этот метод getUrl(),
	 * потому что такой метод уже присутствует в базовом классе:
	 * @see Mage_Core_Block_Abstract::getUrl()
	 * @return string
	 */
	abstract protected function url();

	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/admin/field/button.phtml';}

	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$this->_element = $element;
		return $this->_toHtml();
	}

	/**
	 * @used-by df/admin/field/button/action.phtml
	 * @return string
	 */
	protected function getCaption() {return rm_e($this->param('rm_button_label'));}

	/**
	 * @used-by df/admin/field/button/action.phtml
	 * @return string
	 */
	protected function getHtmlId() {return $this->_element->getHtmlId();}

	/**
	 * @used-by getCaption()
	 * @used-by Df_Admin_Block_Field_Button_Action::getUrl()
	 * @used-by Df_YandexMarket_Block_Api_GetConfirmationCode::getUrl()
	 * @param string $name
	 * @return string|null
	 */
	protected function param($name) {return rm_leaf_child($this->getFieldConfig(), $name);}

	/**
	 * Раньше код был таким:
	 * $originalData = $element->getDataUsingMethod('original_data');
	 * $caption = dfa($originalData, 'button_label');
	 * Однако в Magento CE 1.4 поле «original_data» отсутствует.
	 * @return Mage_Core_Model_Config_Element
	 */
	private function getFieldConfig() {return $this->_element->getData('field_config');}

	/** @var Varien_Data_Form_Element_Abstract */
	private $_element;
}