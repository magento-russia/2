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
class Df_Admin_Block_Field_Button_Action extends Df_Admin_Block_Field_Button {
	/**
	 * @override
	 * @see Df_Admin_Block_Field_Button::url()
	 * Кэшировать результат обычным образом нельзя!
	 * @used-by df/admin/field/button/action.phtml
	 * @return string
	 */
	protected function url() {
		/** @var string|null $area */
		$area = $this->param('rm_action_area');
		/** @var string|null $action */
		$action = $this->param('rm_action');
		/** @var Mage_Core_Model_Url $urlModel */
		$urlModel = Mage::getModel(('admin' === $area) ? 'adminhtml/url' : 'core/url');
		return $urlModel->getUrl($action, df_clean(array('store-view' => df_request('store'))));
	}
}