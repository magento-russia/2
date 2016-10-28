<?php
/**
 * Платёжная система возвращает сюда покупателя вне зависимости от успешности оплаты заказа.
 * Наша задача — перенаправить покупателя:
 * на страницу checkout/onepage/success в случае успешной оплаты
 * на страницу checkout/onepage в случае неуспешной оплаты
 * @uses \Df\Psbank\Action\CustomerReturn
 */
class Df_Psbank_CustomerReturnController extends \Df\Core\Controller {}