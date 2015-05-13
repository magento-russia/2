<?php
class Df_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping {
	/**
	 * Цель перекрытия —
	 * всегда показывать поле для указания города в витринном блоке
	 * для расчёта пользователем стоимости доставки
	 * до перехода на страницу оформления заказа (на странице корзины).
	 * @override
	 * @return bool
	 */
	public function getCityActive() {return true;}
}