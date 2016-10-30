<?php
/** @method Df_PromoGift_Model_Resource_Indexer getResource() */
class Df_PromoGift_Model_Indexer extends Mage_Index_Model_Indexer_Abstract {
	/**
	 * Карта событий, которые будет обрабатывать данный класс.
	 * Данный свойство перекрывает одноимённый свойство родителя.
	 * Родитель использует данный свойство в методе @see Mage_Index_Model_Indexer_Abstract::matchEvent()
	 * Пока мы скопировали данную карту из класса @see Mage_Catalog_Model_Product_Indexer_Flat
	 * @var array(string => mixed)
	 */
	protected $_matchedEntities =
		array(
			Df_SalesRule_Model_Rule::ENTITY =>
				array(
					// Это сообщение приходит после сохранения ценового правила для корзины.
					Mage_Index_Model_Event::TYPE_SAVE
				)
			,Mage_Catalog_Model_Product::ENTITY =>
				array(
					/**
					 * Это сообщение приходит после сохранения товара.
					 * В стандартном коде некоторые индексаторы проверяют у товара
					 * характеристику «is_massupdate», и, если её значение равно true,
					 * то ничего не делают до наступления
					 * Mage_Catalog_Model_Convert_Adapter_Product::finish()
					 * (
					 * 		Mage_Catalog_Model_Convert_Adapter_Product::ENTITY
					 * 		,
					 * 		Mage_Index_Model_Event::TYPE_SAVE
					 * ).
					 * Обратите внимание, что событие удаления товаров
					 * (Mage_Index_Model_Event::TYPE_DELETE)
					 * нам обрабатывать не нужно, потому что соответствующие удалённым товарам
					 * записи в таблице подарков автоматически удаляются из базы данных
					 * благодаря триггеру ON DELETE CASCADE.
					 */
					Mage_Index_Model_Event::TYPE_SAVE
					/**
					 * Вызывается после массовых операций с товарами.
					 * Например, после массовых обновлений свойств товара
					 * в методе Mage_Catalog_Model_Product_Action::updateAttributes()
					 */
					,Mage_Index_Model_Event::TYPE_MASS_ACTION
				)
			,Mage_Core_Model_Website::ENTITY =>
				array(
					/**
					 * Вызывается после сохранения данных сайта.
					 *
					 * Обратите внимание, что событие удаления сайтов
					 * (Mage_Index_Model_Event::TYPE_DELETE)
					 * нам обрабатывать не нужно, потому что соответствующие удалённым сайтам
					 * записи в таблице подарков автоматически удаляются из базы данных
					 * благодаря триггеру ON DELETE CASCADE.
					 */
					Mage_Index_Model_Event::TYPE_SAVE
				)
			/**
			 * Отлавливаем события сохранения и удаления ценовых правил
			 */
			/**
			 * Вызывается в методе Mage_Catalog_Model_Convert_Adapter_Product::finish()
			 * после импорта всех товаров.
			 *
			 * В стандартном коде некоторые индексаторы проверяют у товара характеристику «is_massupdate»
			 * и если её значение равно true, * то ничего не делают до наступления Mage_Catalog_Model_Convert_Adapter_Product::finish()
			 * (Mage_Catalog_Model_Convert_Adapter_Product::ENTITY, Mage_Index_Model_Event::TYPE_SAVE).
			 */
			,Mage_Catalog_Model_Convert_Adapter_Product::ENTITY =>
				array(
					Mage_Index_Model_Event::TYPE_SAVE
				)
			)
	;

	/**
	 * Get Indexer name
	 * @return string
	 */
	public function getName() {return 'Промо-подарки';}

	/**
	 * @override
	 * @return string
	 */
	public function getDescription() {return 'хранит товары-подарки для каждой промо-акции';}

	/**
	 * @override
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	protected function _getResource() {return Df_PromoGift_Model_Resource_Indexer::s();}

	/**
	 * Register indexer required data inside event object
	 *
	 * Система сначала вызывает для объекта класса Mage_Index_Model_Indexer_Abstract
	 * метод _registerEvent(), а затем метод _processEvent().
	 *
	 * Смысл такого двуступенчатого вызова:
	 * Стандартные наследники Mage_Index_Model_Indexer_Abstract на этапе _registerEvent
	 * на этапе _registerEvent зачастую изменяют объект-событие: например, добавляя в него свои данные.
	 *
	 * Хотя, мне до сих пор непонятно, почему бы всё это не делать в _processEvent.
	 *
	 * Один из возможных источников:
	 *
	 * Mage_CatalogInventory_Model_Stock_Item::afterCommitCallback():
	 *
	 * [code]
			Mage::getSingleton('index/indexer')->processEntityAction(
				$this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
			);
	 * [/code]
	 *
	 * Последовательность вызовов до:
	 *
	 * Mage_Index_Model_Indexer::processEntityAction()
	 * Mage_Index_Model_Indexer::logEvent()
	 * Mage_Index_Model_Indexer::registerEvent()
	 *
	 *
	 * Последовательность вызовов в результате:
	 *
	 * Mage_Index_Model_Indexer_Abstract::register()
	 * Df_PromoGift_Model_Indexer::_registerEvent()
	 *
	 * @param  Mage_Index_Model_Event $event
	 * @return void
	 */
	protected function _registerEvent(Mage_Index_Model_Event $event) {
	}

	/**
	 * Process event based on event state data
	 *
	 * Чтобы система вызвала данный метод, событие $event должно отвечать карте $_matchedEntities.
	 *
	 * Обратите внимание, что когда администратор выполняет непосредственную переиндексацию
	 * в разделе System → Index Management — мы сюда не попадаем,
	 * потому что система вызовет у нас вместо данного метода метод
	 * Mage_Index_Model_Indexer_Abstract::reindexAll(),
	 * который по умолчанию работает как $this->_getResource()->reindexAll()
	 *
	 * @param  Mage_Index_Model_Event $event
	 * @return void
	 */
	protected function _processEvent(Mage_Index_Model_Event $event) {
		/**
		 * При сохранении товара мы попадаем сюда дважды, из методов:
		 * Mage_CatalogInventory_Model_Stock_Item::afterCommitCallback()
		 * Mage_Catalog_Model_Product::afterCommitCallback()
		 */
		try {
			if (
				df_module_enabled(Df_Core_Module::PROMO_GIFT)
				&& df_cfgr()->promotion()->gifts()->getEnabled()
			) {
				/** @var string $entityType */
				$entityType = $event->getEntity();
				/** @var mixed $entity */
				$entity = $event['data_object'];
				/** @var string $eventType */
				$eventType = $event->getType();
				if (Mage_Core_Model_Website::ENTITY === $entityType) {
					if (Mage_Index_Model_Event::TYPE_SAVE === $eventType) {
						/**
						 * Был создан новый сайт, или же изменились параметры созданного ранее.
						 * Нам надо обновить подарки, относящиеся к данному сайту.
						 */
						 df_assert($entity instanceof Mage_Core_Model_Website);
						/** @var Mage_Core_Model_Website $entity */
						 $this->getResource()->reindexWebsite($entity);
					}
				}
				else if (Df_SalesRule_Model_Rule::ENTITY === $entityType) {
					if (Mage_Index_Model_Event::TYPE_SAVE === $eventType) {
						/**
						 * Было создано новое ценовое правило, или же изменились параметры созданного ранее.
						 * Нам надо обновить подарки, относящиеся к данному ценовому правилу.
						 */
						df_assert($entity instanceof Mage_SalesRule_Model_Rule);
						/** @var Mage_SalesRule_Model_Rule $entity */
						$this->getResource()->reindexRule($entity);
					}
				}
				else if (Mage_Catalog_Model_Product::ENTITY === $entityType) {
					if (Mage_Index_Model_Event::TYPE_SAVE === $eventType) {
						/**
						 * Был создан новый товар, или же изменились параметры созданного ранее.
						 * Нам надо обновить подарки, относящиеся к данному товару.
						 */
						/**
						 * Обратите внимание, что класс товара — всегда Df_Catalog_Model_Product,
						 * а не Mage_Catalog_Model_Product,
						 * потому что Российская сборка перекрывает класс Mage_Catalog_Model_Product
						 * классом Df_Catalog_Model_Product
						 */
						df_assert($entity instanceof Df_Catalog_Model_Product);
						/** @var Df_Catalog_Model_Product $entity */
						$this->getResource()->reindexProduct($entity);
					}
					else if (Mage_Index_Model_Event::TYPE_MASS_ACTION === $eventType) {
						/** @var Df_Catalog_Model_Resource_Product_Collection $collection */
						$collection = Df_Catalog_Model_Resource_Product_Collection::i();
						$collection->addAttributeToSelect("*");
						if (!is_null($entity)) {
							df_assert($entity instanceof Varien_Object);
							/** @var Varien_Object $entity */
							// $productIds — это идентификаторы обновлённых товаров
							$productIds = $entity['product_ids'];
							/** @var array $productIds */
							df_assert(is_array($productIds));
							$collection->addAttributeToFilter('entity_id', array(
								Df_Varien_Const::IN => $productIds
							));
						}
						foreach ($collection as $product) {
							/** @var Df_Catalog_Model_Product $product */
							$this->getResource()->reindexProduct($product);
						}
					}
				}
				else if (Mage_Catalog_Model_Convert_Adapter_Product::ENTITY === $entityType) {
					if (Mage_Index_Model_Event::TYPE_SAVE === $eventType) {
						/**
						 * Администратор только что импортировал в магазин много товаров.
						 * Мы не знаем, что это за товары (в данном сообщении объект $entity пуст)
						 * Поэтому перестраиваем справочник подарков с нуля.
						 */
						$this->reindexAll();
					}
				}
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}
}