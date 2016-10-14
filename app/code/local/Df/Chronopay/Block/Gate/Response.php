<?php
class Df_Chronopay_Block_Gate_Response extends Df_Core_Block_Template_NoCache {
	/** @return array(string => string) */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Chronopay_Model_Gate_Response $response */
			$response = $this[self::$P__RESPONSE];
			$this->{__METHOD__} = array(
				'ChronoPay error code' => $response->getCode()
				,'ChronoPay error message'  => $response->getMessage()
				,'Transaction ID'  => $response->getTransactionId()
				,'ChronoPay extended error code'  => $response->getExtendedCode()
				,'ChronoPay extended error message'  => $response->getExtendedMessage()
			);
		}
		return $this->{__METHOD__};
	}

	/** @var string */
	private static $P__RESPONSE = 'response';

	/**
	 * @used-by Df_Chronopay_Model_Gate::capture()
	 * @param Df_Chronopay_Model_Gate_Response $response
	 * @param string $template
	 * @return string
	 */
	public static function r(Df_Chronopay_Model_Gate_Response $response, $template) {
		return rm_render(__CLASS__, array(self::$P__RESPONSE => $response, 'template' => $template));
	}
}