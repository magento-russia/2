<?php
class Df_YandexMoney_Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'yandex-money';}
}