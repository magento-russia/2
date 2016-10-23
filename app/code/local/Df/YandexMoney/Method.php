<?php
class Df_YandexMoney_Method extends Df_Payment_Method_WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'yandex-money';}
}