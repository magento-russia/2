<?php
namespace Df\YandexMoney;
class Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'yandex-money';}
}