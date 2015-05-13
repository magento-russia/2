<?php
/**
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 */
class Df_Admin_Block_System_Config_Form_Field_Button_Action
	extends Df_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/admin/system/config/form/field/button/action.phtml';}

	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		/** @var array(string => mixed) $originalData */
		$originalData = $element->getDataUsingMethod('original_data');
		/** @var string $area */
		$area = df_a($originalData, 'rm_area');
		/** @var string $action */
		$action = df_a($originalData, 'rm_action');
		/** @var string $caption */
		$caption = df_a($originalData, 'rm_label');
		/** @var Mage_Core_Model_Url $urlModel */
		$urlModel = Mage::getModel(('admin' === $area) ? 'adminhtml/url' : 'core/url');
		/** @var string $url */
		$url = $urlModel->getUrl($action, df_clean(array('store-view' => df_request('store'))));
		$this->addData(array(
			self::P__CAPTION => $caption
			,self::P__HTML_ID => $element->getHtmlId()
			,self::P__URL => $url
		));
		return $this->_toHtml();
	}

	const _CLASS = __CLASS__;
	const P__CAPTION = 'caption';
	const P__HTML_ID = 'html_id';
	const P__URL = 'url';
}