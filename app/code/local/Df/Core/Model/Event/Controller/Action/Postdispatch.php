<?php
/**
 * Cообщение:		«controller_action_postdispatch»
 * 					«controller_action_postdispatch_controller_action»
 * Источник:		Mage_Core_Controller_Varien_Action::postDispatch()
 * [code]
		Mage::dispatchEvent(
			'controller_action_postdispatch_'.$this->getFullActionName(),array('controller_action'=>$this)
		);
		Mage::dispatchEvent(
			'controller_action_postdispatch_'.$this->getRequest()->getRouteName(),array('controller_action'=>$this)
		);
		Mage::dispatchEvent('controller_action_postdispatch', array('controller_action'=>$this));
 * [/code]
 *
 * Назначение:		Позволяет выполнить дополнительную обработку запроса
 * 					после обработы запроса контроллером
 */
class Df_Core_Model_Event_Controller_Action_Postdispatch extends Df_Core_Model_Event {
	/** @return bool */
	public function isClientExpectedHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_strings_are_equal_ci(
					Zend_Mime::TYPE_HTML, df_a($this->getContentTypesExpectedByClient(), 0)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Controller_Request_Http */
	public function getRequest() {return $this->getController()->getRequest();}

	/** @return Mage_Core_Controller_Varien_Action */
	public function getController() {return $this->getEventParam(self::EVENT_PARAM__CONTROLLER_ACTION);}

	/** @return string[] */
	private function getContentTypesExpectedByClient() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_parse_csv($this->getAcceptHeader());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAcceptHeader() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|bool $result */
			$result = $this->getRequest()->getHeader(self::HEADER__ACCEPT);
			$this->{__METHOD__} = $result ? $result : '';
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	const _CLASS = __CLASS__;
	const EXPECTED_EVENT_PREFIX = 'controller_action_postdispatch';
	const EVENT_PARAM__CONTROLLER_ACTION = 'controller_action';
	/**
	 * Обратите внимание, что названия заголовков могут быть записаны в запросе
	 * как прописными буквами, так и строчными,
	 * однако Zend_Controller_Request_Http::getHeader() самостоятельно приводит их к нужному регистру
	 * @link http://stackoverflow.com/questions/5258977/are-http-headers-case-sensitive
	 */
	const HEADER__ACCEPT = 'Accept';
}