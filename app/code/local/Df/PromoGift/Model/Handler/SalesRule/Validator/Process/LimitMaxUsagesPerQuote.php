<?php
class Df_PromoGift_Model_Handler_SalesRule_Validator_Process_LimitMaxUsagesPerQuote
	extends Df_PromoGift_Model_Handler_SalesRule_Validator_Process_Abstract {
	/**
	 * Вообще-то мы могли бы допустить использование параметра «Max Usages per Cart»
	 * не только для правил с подарками (на которые скидка 100%), * но и для других правил с процентной скидкой.
	 *
	 * Обработка других правил с процентной скидкой не добавляет нам дополнительных затрат:
	 * программный код обработки един для всех правил с процентной скидкой.
	 *
	 * Однако, область действия данного модуля — только товары в подарок, * поэтому не пользуемся возможностью расширения сферы данной обработки на другие типы правил, * и обрабатываем только правила с товарами в подарок.
	 * @return void
	 */
	protected function handlePromoGiftingRule() {
		/**
		 * Сколько раз правило можно использовать для корзины?
		 *
		 * Обратите внимание, что количество использований правила для элементов конкретной строки заказа
		 * уже ограничено системной настройкой «Maximum Qty Discount is Applied To».
		 *
		 * Другими словами, параметр $maxUsagesPerQuote ограничивает
		 * количество СТРОК ЗАКАЗА которые содержат товары подарки.
		 *
		 * Ещё можно сказать, что параметр $maxUsagesPerQuote ограничивает
		 * количество АРТИКУЛОВ ТОВАРОВ в заказе, которые могут быть подарками.
		 *
		 * Параметр «Maximum Qty Discount is Applied To» ограничивает
		 * количество бесплатных единиц товара одного артикула
		 *
		 * @var int $maxUsagesPerQuote
		 */
		$maxUsagesPerQuote =
			df_nat0($this->getRule()->getData(Df_PromoGift_Model_Rule::P__MAX_USAGES_PER_QUOTE))
		;
		/**
		 * Дальше в коде мы можем «отнять» подарок.
		 * Эта переменная учитывает, был ли подарок отнят.
		 */
		/** @var bool $isGift */
		$isGift = true;
		// Значение «0» означает, что правило можно использовать неограниченное количество раз.
		if (0 < $maxUsagesPerQuote) {
			// Сколько раз правило уже использовали для корзины
			/** @var int $timesUsed */
			$timesUsed = $this->getRule()->getTimesUsed();
			if (is_null($timesUsed)) {
				$timesUsed = 0;
			}
			// Учитываем данное использование и обновляем счётчик
			$timesUsed++;
			$this->getRule()->setTimesUsed($timesUsed);
			if ($timesUsed > $maxUsagesPerQuote) {
				// Превысили лимит использования, делаем скидку недействительной
				$this->getResult()->addData(array(
					Df_SalesRule_Model_Rule::P__DISCOUNT_AMOUNT => 0
					,Df_SalesRule_Model_Rule::P__BASE_DISCOUNT_AMOUNT => 0
				));
				$this->getCurrentQuoteItem()->unsetData('discount_percent');
				$isGift = false;
			}
		}
		if ($isGift) {
			/**
			 * Чтобы правильно управлять показом блока подарков на экране,
			 * нам надо вести учёт находящихся в корзине подаров и правил,
			 * по подарки предоставлены покупателю.
			 * Обратите внимание, что наши счётчики обнуляются по событию
			 * «sales_quote_collect_totals_before»:
			 * это событие вызывается прямо перед началом работы
			 * стандартного калькулятора ценовых правил.
			 */
			$counter = df_h()->promoGift()->getCustomerRuleCounter();
			/** @var Df_PromoGift_Model_Customer_Rule_Counter $counter */
			/**
			 * Нам нужно знать идентификатор подарочной строки заказа,
			 * чтобы выделить её в корзине.
			 * Поэтому, если строка заказа ещё не сохранялась, то сохраняем её сейчас.
			 */
			if (is_null($this->getCurrentQuoteItem()->getId())) {
				/**
				 * Иногда у клиентов происходит странный сбой:
				 *	2012-07-20T12:38:46+04:00 INFO (6):
					URL:			http://www.ultra-avto.ru/checkout/cart/add/uenc/aHR0cDovL3d3dy51bHRyYS1hdnRvLnJ1L3ZpZGVvcmVnaXN0cmF0b3JpL3BhcmtjaXR5LWR2ci1oZC01MjAuaHRtbA,,/product/55/
					Magento:		1.47.7 (1.7.0.0)
					***********************************
					SQLSTATE[23000]: Integrity constraint violation:
				 	1452 Cannot add or update a child row:
				 	a foreign key constraint fails
				 	(`magento`.`sales_flat_quote_item`, 	CONSTRAINT `FK_SALES_FLAT_QUOTE_ITEM_QUOTE_ID_SALES_FLAT_QUOTE_ENTITY_ID`
				 	FOREIGN KEY (`quote_id`) REFERENCES `sales_flat_quote` (`entity_id`)
				 	ON DELETE CASC)
					***********************************
				 *
				 *  Ограничение, о котором говорит система, выглядит так:
				 * 	CONSTRAINT `FK_SALES_FLAT_QUOTE_ITEM_QUOTE_ID_SALES_FLAT_QUOTE_ENTITY_ID`
				 * 		FOREIGN KEY (`quote_id`)
				 * 		REFERENCES `sales_flat_quote` (`entity_id`)
				 * 		ON DELETE CASCADE
				 * 		ON UPDATE CASCADE
				 *
				 *  Видимо, нельзя сохранять quote item при несохранённом quote.
				 */
				if (is_null($this->getQuote()->getId())) {
					$this->getQuote()->save();
				}
				if (is_null($this->getCurrentQuoteItem()->getId())) {
					$this->getCurrentQuoteItem()->save();
				}
			}
			df_assert(!is_null($this->getCurrentQuoteItem()->getId()));
			$counter->count($this->getRule()->getId(), $this->getCurrentQuoteItem()->getId());
		}
	}

	/** @used-by Df_PromoGift_Observer::salesrule_validator_process() */
	const _C = __CLASS__;
}