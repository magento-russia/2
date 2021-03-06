<?php
/**
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 */
class Df_YandexMarket_Block_Api_GetConfirmationCode extends Df_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/yandex_market/api/get_confirmation_code.phtml';}

	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		/**
		 * Раньше тут стоял код:
		 * $originalData = $element->getDataUsingMethod('original_data');
		 * $caption = df_a($originalData, 'button_label');
		 * Однако в Magento CE 1.4 поле «original_data» отсутствует.
		 */
		/** @var Mage_Core_Model_Config_Element $fieldConfig */
		$fieldConfig = $element->getData('field_config');
		/** @var string $caption */
		$caption = (string)$fieldConfig->{'button_label'};
		/** @var string $url */
		$url =
			rm_sprintf(
				'https://oauth.yandex.ru/authorize?response_type=code&client_id=%s'
				,df_cfg()->yandexMarket()->api()->getApplicationId()
			)
		;
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