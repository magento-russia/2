<?php
/**
 * @method Df_Admin_Config_Form_FieldInstance_Info_Urls getInstance()
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
class Df_Admin_Block_Field_Info_Urls extends Df_Admin_Block_Field_Custom {
	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/admin/field/info/urls.phtml';}

	/**
	 * @override
	 * @return string
	 */
	protected function getInstanceClass() {return Df_Admin_Config_Form_FieldInstance_Info_Urls::_C;}

	/** @return array(string => string) */
	protected function getUrls() {return $this->getInstance()->getUrls();}
}