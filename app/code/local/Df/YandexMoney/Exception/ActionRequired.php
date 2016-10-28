<?php
namespace Df\YandexMoney\Exception;
/** @method \Df\YandexMoney\Response\Authorize getResponse() */
class ActionRequired extends \Df\Payment\Exception\Response {
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
}