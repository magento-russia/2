<?php
/**
 * Может показаться, что перед процессом применением промо-правил
 * разумно удалить добавленные ранее товары-подарки.
 * Это позволит в процессе добавления товаров-подарков не проверять,
 * были ли товары-подарки добавлены ранее.
 *
 * Однако, такой способ имеет существенный недостаток:
 * Пусть товар-подарок — настраиваемый (например, туфли, для которых покупатель должен выбрать размер).
 * Тогда, удалив товар-подарок перед пересчётом промо-правил — мы утратим информацию о выборе покупателя.
 */
/**
 * Вопрос: как сохранить информацию о предыдущих применениях промо-правил?
 *
 * Похоже, что в системе присутствует встроенный способ:
 * поле «applied_rule_ids» таблицы «sales_flat_quote».
 *
 * Однако значение соответствующего свойства в процессе создания страницы корзины
 * перетирается 4 раза при 2 товарах в корзине.
 *
 *
 *
 * Другой способ: можно посмотреть работу стандартного типа правил
 * «Buy X get Y free (discount amount is Y)».
 *
 * Однако этот способ на самом деле не добавляет товары в корзину покупателя
 * (см. комментарии к методу Mage_SalesRule_Model_Validator::process())
 *
 * Можно, например, хранить в сессии.
 * Но лучше всего — вместе с объектом Mage_Sales_Model_Quote.
 *
 * Я так понимаю, надо для объекта Mage_Sales_Model_Quote_Item
 * учитывать характеристику «получено в подарок».
 *
 * Объекты класса Mage_Sales_Model_Quote_Item хранят свои данные в таблице «sales_flat_quote_item».
 *
 *
 * Кстати, в таблице «sales_flat_quote_item» присутствует поле «additional_data».
 * Видимо, мы можем воспользоваться этим полем для хранения характеристики «получено в подарок».
 *
 * Однако, я не вижу, чтобы стандартный код как-либо использовал поле «additional_data».
 * Поэтому и нам его исользовать не стоит: ведь мы не знаем правил его использования.
 *
 * Надёжнее и безопаснее добавить в таблицу «sales_flat_quote_item» своё поле.
 * Но как нам записывать туда данные и извлекать их оттуда?
 *
 *
 * С другой стороны, можно воспользоваться таблицей «sales_flat_quote_item_option».
 * Система характеризует эту таблицу как «Additional options for quote item».
 * Эта таблица хранит объекты класса Mage_Sales_Model_Quote_Item_Option.
 * Как нам работать с этой таблицей?
 *
 * Как вообще работать с объектами Mage_Sales_Model_Quote_Item_Option?
 */
/**
 * Вопрос: нужно ли нам сохранять информацию о применении предыдущих промо-правил в БД,
 * или достаточно сессии?
 *
 * Думаю, что нужно.
 * Если мы не сохраняем в БД информацию о применении предыдущих промо-правил,
 * то, мы утратим эту информацию в следующих ситуациях:
 *
 * [*]	при восстановлении «сохранённой корзины»
 * 		(когда покупатель авторизуется и видит свою собранную ранее корзину)
 * [*]  при редактировании заказа в админке
 */
/**
 * Вопрос: нужно ли для сохранения в БД информации о применении предыдущих промо-правил
 * заводить новое поле в БД, или же у класса Mage_Sales_Model_Quote имеются встроенные средства
 * для сохранения новых структур данных в БД?
 */
/**
 * Объекты Mage_Sales_Model_Quote сохраняют свои данные в таблице «sales_flat_quote».
 */
class Df_PromoGift_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function sales_quote_collect_totals_before() {
		try {
			if (self::enabled()) {
				// обнуляем счётчики находящихся в корзине покупателя товаров-подарков
				df_h()->promoGift()->getCustomerRuleCounter()->reset();
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function salesrule_validator_process(Varien_Event_Observer $o) {
		try {
			if (self::enabled()) {
				df_handle_event(
					Df_PromoGift_Model_Handler_SalesRule_Validator_Process_LimitMaxUsagesPerQuote::class
					,Df_SalesRule_Model_Event_Validator_Process::class
					,$o
				);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_block_salesrule_actions_prepareform(Varien_Event_Observer $o) {
		try {
			if (self::enabled()) {
				df_handle_event(
					Df_PromoGift_Model_Handler_Adminhtml_Block_Actions_PrepareForm_AddInputMaxUsagesPerQuote::class
					,Df_Adminhtml_Model_Event_Block_SalesRule_Actions_PrepareForm::class
					,$o
				);
			}
		}

		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function salesrule_rule_save_after(Varien_Event_Observer $o) {
		try {
			if (self::enabled()) {
				/**
				 * Оповещаем индексаторов о событии изменения ценового правила.
				 * Такое оповещение ожидает наш индексатор Df_PromoGift_Model_Indexer
				 */
				/**
				 * Ядро использует только один объект класса Mage_Index_Model_Indexer,
				 * в том числе и для вызова processEntityAction.
				 * Значит, и нам безопасно использовать тот же объект-одиночку.
				 */
				df_mage()->index()->indexer()->processEntityAction(
					$o['rule']
					,Df_SalesRule_Model_Rule::ENTITY
					,Mage_Index_Model_Event::TYPE_SAVE
				);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/** @return bool */
	private static function enabled() {
		/** @var bool $result */
		static $r; return !is_null($r) ? $r : $r = df_cfg()->promotion()->gifts()->getEnabled();
	}
}