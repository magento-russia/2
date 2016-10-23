<?php
class Df_YandexMoney_OAuth extends Df_Yandex_OAuth {
	/**
	 * @override
	 * @param array(string => string) $response
	 * @return Df_YandexMarket_OAuth
	 * @throws Exception
	 */
	protected function checkResponse(array $response) {
		/** @var string $errorType */
		$errorType = dfa($response, 'error');
		if ($errorType) {
			/** @var string $errorMessage */
			$errorMessage = dfa(array(
				'invalid_grant' =>
					'В выдаче access_token отказано.'
					. ' Временный токен не выдавался Яндекс.Деньгами, либо просрочен,'
					. ' либо по этому временному токену уже выдан access_token'
					. ' (повторный запрос токена авторизации с тем же временным токеном).'
				,'invalid_request' =>
					'Обязательные параметры запроса отсутствуют'
					. ' или имеют некорректные или недопустимые значения.'
				,'unauthorized_client' =>
					'Неверное значение параметра client_id или client_secret,'
					. ' либо приложение не имеет права запрашивать авторизацию'
					. ' (например, его client_id заблокирован Яндекс.Деньгами).'
			), $errorType, $errorType);
			df_error('При получении токена Яндекс.Денег произошёл сбой: «%s».', $errorMessage);
		}
		return $this;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getAdditionalParams() {
		return array('redirect_uri' => $this->cfg(self::P__CUSTOMER_RETURN_URL));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getUriAsString() {return 'https://sp-money.yandex.ru/oauth/token';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CUSTOMER_RETURN_URL, DF_V_STRING_NE);
	}
	const P__CUSTOMER_RETURN_URL = 'param__customer_return_url';
	/**
	 * @param string $appId
	 * @param string $appPassword
	 * @param string $tokenTemporary
	 * @param string $customerReturnUrl
	 * @return Object
	 */
	public static function i($appId, $appPassword, $tokenTemporary, $customerReturnUrl) {
		df_param_string_not_empty($appId, 0);
		df_param_string_not_empty($appPassword, 1);
		df_param_string_not_empty($tokenTemporary, 2);
		df_param_string_not_empty($customerReturnUrl, 3);
		return new self(array(
			self::P__APP_ID => $appId
			, self::P__APP_PASSWORD => $appPassword
			, self::P__TOKEN_TEMPOPARY => $tokenTemporary
			, self::P__CUSTOMER_RETURN_URL => $customerReturnUrl
		));
	}
}