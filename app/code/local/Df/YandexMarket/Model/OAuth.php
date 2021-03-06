<?php
class Df_YandexMarket_Model_OAuth extends Df_Yandex_Model_OAuth {
	/**
	 * @override
	 * @param array(string => string) $response
	 * @return Df_YandexMarket_Model_OAuth
	 * @throws Exception
	 */
	protected function checkResponse(array $response) {
		/** @var string $errorType */
		$errorType = df_a($response, 'error');
		if ($errorType) {
			/** @var string $errorMessage */
			$errorMessage = df_a($response, 'error_description', $errorType);
			df_error(
				'При получении токена Партнёрского API Яндекс.Маркета произошёл сбой: «%s».'
				,$errorMessage
			);
		}
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getUriAsString() {return 'https://oauth.yandex.ru/token';}

	/**
	 * @param string $appId
	 * @param string $appPassword
	 * @param string $tokenTemporary
	 * @return Object
	 */
	public static function i($appId, $appPassword, $tokenTemporary) {
		df_param_string_not_empty($appId, 0);
		df_param_string_not_empty($appPassword, 1);
		df_param_string_not_empty($tokenTemporary, 2);
		return new self(array(
			self::P__APP_ID => $appId
			, self::P__APP_PASSWORD => $appPassword
			, self::P__TOKEN_TEMPOPARY => $tokenTemporary
		));
	}
}