<?php
/**
 * 2015-02-17
 * Этот класс используется в файлах etc/system.xml».
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 */
class Df_Admin_Block_Field
	extends Mage_Adminhtml_Block_Abstract
	implements Varien_Data_Form_Element_Renderer_Interface {

	/**
	 * отключаем кэшировани
	 * @override
	 * @return int|null
	 */
	public function getCacheLifetime() {return Df_Core_Block_Template::CACHE_LIFETIME_DISABLE;}

	/** @return Df_Admin_Model_Form_Element */
	public function getElement() {return $this->_element;}

	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		return
			$this
				->setElement(Df_Admin_Model_Form_Element::i($element))
				->setTemplate('df/admin/field.phtml')
				->toHtml()
		;
	}

	/**
	 * @param Df_Admin_Model_Form_Element $element
	 * @return Df_Admin_Block_Field
	 */
	public function setElement(Df_Admin_Model_Form_Element $element) {
		$this->_element = $element;
		return $this;
	}

	/** @var  Df_Admin_Model_Form_Element */
	private $_element;
}