<?php
/** @method Df_YandexMoney_Response_Authorize getResponse() */
class Df_YandexMoney_Exception_ActionRequired extends \Df\Payment\Exception\Response {
	/** @return string */
	public function getActionUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getResponse()->getActionUrl();
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @see \Df\Payment\Exception\Response::needFraming()
	 * @override
	 * @return bool
	 */
	public function needFraming() {return false;}

	/** @used-by Df_YandexMoney_Response_Authorize::getExceptionClass() */
	
}