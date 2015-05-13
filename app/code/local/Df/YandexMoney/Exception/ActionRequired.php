<?php
/**
 * @method Df_YandexMoney_Model_Response_Authorize getResponse()
 */
class Df_YandexMoney_Exception_ActionRequired extends Df_Payment_Exception_Response {
	/** @return string */
	public function getActionUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getResponse()->getActionUrl();
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @see Df_Payment_Exception_Response::needFraming()
	 * @override
	 * @return bool
	 */
	public function needFraming() {return false;}
}