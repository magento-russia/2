<?php
/**
 * Cообщение:		«core_block_abstract_to_html_after»
 * Источник:		Mage_Core_Block_Abstract::toHtml()
 * [code]
		if (self::$_transportObject === null) {
			self::$_transportObject = new Varien_Object;
		}
		self::$_transportObject->setHtml($html);
		Mage::dispatchEvent('core_block_abstract_to_html_after',array('block' => $this, 'transport' => self::$_transportObject));
		$html = self::$_transportObject->getHtml();
 * [/code]
 *
 * Назначение:		Позволяет выполнить дополнительную настройку блока
 * 					после его создания
 */
class Df_Core_Model_Event_CoreBlockAbstract_ToHtml_After
	extends Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Abstract {
	/**
	 * @param string $html
	 * @return Df_Core_Model_Event_CoreBlockAbstract_ToHtml_After
	 */
	public function setHtml($html) {
		df_param_string($html, 0);
		$this->getTransport()->setData(self::TRANSPORT_PARAM__HTML, $html);
		return $this;
	}

	/** @return string */
	public function getHtml() {return df_nts($this->getTransport()->getData(self::TRANSPORT_PARAM__HTML));}

	/** @return Varien_Object */
	public function getTransport() {return $this->getEventParam(self::EVENT_PARAM__TRANSPORT);}

	/** @return string */
	protected function getExpectedEventSuffix() {return self::EXPECTED_EVENT_SUFFIX;}

	const _CLASS = __CLASS__;

	const EXPECTED_EVENT_SUFFIX = '_html_after';
	const EVENT_PARAM__TRANSPORT = 'transport';
	const TRANSPORT_PARAM__HTML = 'html';
}