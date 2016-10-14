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
			$this->{__METHOD__} = df_strings_are_equal_ci(
				Zend_Mime::TYPE_HTML, df_a($this->getContentTypesExpectedByClient(), 0)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Controller_Request_Http */
	public function getRequest() {return $this->getController()->getRequest();}

	/** @return Mage_Core_Controller_Varien_Action */
	public function getController() {return $this->getEventParam('controller_action');}

	/** @return string[] */
	private function getContentTypesExpectedByClient() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv_parse($this->getAcceptHeader());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAcceptHeader() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание, что названия заголовков могут быть записаны в запросе
			 * как прописными буквами, так и строчными,
			 * однако @uses Zend_Controller_Request_Http::getHeader()
			 * самостоятельно приводит их к нужному регистру
			 * http://stackoverflow.com/questions/5258977/are-http-headers-case-sensitive
			 */
			/** @var string|bool $result */
			$result = $this->getRequest()->getHeader('Accept');
			$this->{__METHOD__} = $result ? $result : '';
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'controller_action_postdispatch';}
}