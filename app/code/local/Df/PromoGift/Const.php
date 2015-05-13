<?php
/**
 * Константы модуля.
 * Обратите внимание, что в программном коде Российской сборки
 * должны отсутствовать неявные константные текстовые строки среди выражений PHP.
 *
 * Все константные текстовые строки должны быть явно объявлены
 * как именованные константы оператором const
 * либо в описании того класса класса, где они применяются,
 * либо в данном классе, если они применяются сразу в нескольких классах.
 *
 */
interface Df_PromoGift_Const {
	const GIFT_COLLECTION_CLASS_MF = 'df_promo_gift/gift_collection';
	/**
	 * Модуль добавляет данное поле в базу данных
	 *
	 * Таблица:				«salesrule»
	 * Содержание поля:		наибольшее количество разных товаров, к которым правило применимо
	 * 						в рамках одного заказа
	 */
	const DB__SALES_RULE__MAX_USAGES_PER_QUOTE = 'df_max_usages_per_quote';
	/**
	 * Эта таблица является заранее расчитанным справочником товаров-подарков.
	 */
	const DB__PROMO_GIFT = 'df_promo_gift';
	/************************************************************
	 * Колонки таблицы «df_promo_gift»
	 */
	const DB__PROMO_GIFT__PRODUCT_ID = 'product_id';	// идентификатор товара
	const DB__PROMO_GIFT__RULE_ID = 'rule_id';			// идентификатор ценового правила
	const DB__PROMO_GIFT__WEBSITE_ID = 'website_id';	// идентификатор сайта
	/***********************************************************/
	/***********************************************************
	 * Различные заголовки и подписи
	 */
	/**
	 * Имя индексатора товаров-подарков
	 * (отображается в административной части на странице «System» → «Index Management»)
	 */
	const T_PROMO_GIFTS = 'Promo Gifts';
	/**
	 * Подпись для индексатора в административной части на странице
	 * «System» → «Index Management»
	 */
	const T_INDEXER_DESCRIPTION = 'Calculates free products for each promo gifting rule';
	/***********************************************************/
}