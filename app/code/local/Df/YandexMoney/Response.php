<?php
/**
 * http://api.yandex.ru/money/doc/dg/reference/request-payment.xml
 * http://api.yandex.ru/money/doc/dg/reference/process-payment.xml
 */
abstract class Df_YandexMoney_Response extends Df_Payment_Response {
	/** @return array(string => string) */
	abstract protected function getErrorMap();

	/**
	 * «Адрес, на который необходимо отправить пользователя для разблокировки счета.
	 * Поле присутствует в случае ошибки account_blocked.»
	 * @return string|null
	 */
	public function getAccountUnblockUri() {return $this->cfg('account_unblock_uri');}

	/**
	 * «Текущий баланс счета пользователя.
	 * Присутствует при выполнении следующих условий:
	 * 		метод выполнен успешно;
	 * 		токен авторизации обладает правом account-info.»
	 * @return Df_Core_Model_Money|null
	 */
	public function getCustomerBalance() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $resultAsString */
			$resultAsString = $this->cfg('balance');
			$this->{__METHOD__} = df_n_set(!$resultAsString ? null : df_money(df_float($resultAsString)));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getErrorCode() {return $this->cfg('error');}

	/**
	 * «Код результата выполнения операции.»
	 * @return string|null
	 */
	public function getStatusCode() {return $this->cfg('status');}

	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {
		return
			$this->isSuccessful()
			? ''
			: dfa($this->getErrorMap(), $this->getErrorCode(), $this->getErrorCode())
		;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function isSuccessful() {return 'success' === $this->getStatusCode();}
}