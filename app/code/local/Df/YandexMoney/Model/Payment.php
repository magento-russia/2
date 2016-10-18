<?php
class Df_YandexMoney_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'yandex-money';}
}