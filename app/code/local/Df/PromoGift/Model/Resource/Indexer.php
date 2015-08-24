<?php
class Df_PromoGift_Model_Resource_Indexer extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Перестраивает таблицу подарков полностью
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	public function reindexAll() {
		try {
			$this
				/**
				 * Сначала полностью очищаем таблицу от старых данных
				 */
				->deleteAllGifts()
				/**
				 * Затем записываем в таблицу новые данные
				 */
				->createGiftsForAllWebsites()
			;
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $this;
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	public function reindexProduct(Df_Catalog_Model_Product $product) {
		$this
			->deleteGiftsForProduct($product)
			->createGiftsForProduct($product)
		;
		return $this;
	}

	/**
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	public function reindexRule(Mage_SalesRule_Model_Rule $rule) {
		$this
			->deleteGiftsForRule($rule)
			->createGiftsForRule($rule)
		;
		return $this;
	}

	/**
	 * @param Mage_Core_Model_Website $website
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	public function reindexWebsite(Mage_Core_Model_Website $website) {
		$this
			->deleteGiftsForWebsite($website)
			->createGiftsForWebsite($website)
		;
		return $this;
	}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
		parent::_afterLoad($object);
		$this->restoreObjectToNormalMode($object);
		return $this;
	}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		parent::_afterSave($object);
		$this->restoreObjectToNormalMode($object);
		return $this;
	}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object) {
		parent::_beforeSave($object);
		$this->switchObjectToDbMode($object);
		return $this;
	}

	/** @return Df_PromoGift_Model_Resource_Indexer */
	private function createGiftsForAllWebsites() {
		foreach ($this->getWebsites() as $website) {
			/** @var Mage_Core_Model_Website $website */
			$this->createGiftsForWebsite($website);
		}
		return $this;
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function createGiftsForProduct(Df_Catalog_Model_Product $product) {
		/**
		 * Надо понимать, что не любой присланный сюда товар подходит в качестве подарка
		 * Нам надо подвергнуть присланный сюда товар тем же проверкам,
		 * что содержатся в методе Df_PromoGift_Model_Resource_Indexer::getProductsByWebsite()
		 */
		if (Mage_Catalog_Model_Product_Status::STATUS_ENABLED === $product->getStatus()) {
			$eligible= true;
			/** @var bool $eligible */

			if (Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE === $product->getTypeId()) {
				if (
					is_null(
						$this->getDependentProducts()->getItemById(
							$product->getId()
						)
					)
				) {
					/**
					 * Отбраковываем товар, если он является частью другого и не видим самостоятельно
					 */
					$eligible = false;
				}
			}
			if ($eligible) {
				$stockItem = df_model('cataloginventory/stock_item');
				/** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */

				$stockItem->loadByProduct($product);
				if ($stockItem->getManageStock()) {
					if (!$stockItem->getIsInStock()) {
						$eligible = false;
					}
					else {
						if ($stockItem->getQty() < $stockItem->getMinQty()) {
							$eligible = false;
						}
					}
				}
			}
			if ($eligible) {
				foreach ($this->getWebsites() as $website) {
					/** @var Mage_Core_Model_Website $website */
					/**
					 * Нам нужно отсеять сайты, к которым товар не относится
					 */
					if (in_array($website->getId(), $product->getWebsiteIds())) {
						$rules = $this->getPromoGiftingRulesByWebsite($website);
						/** @var Mage_SalesRule_Model_Mysql4_Rule_Collection $rules */
						foreach ($rules as $rule) {
							/** @var Mage_SalesRule_Model_Rule $rule */
							/**
							 * Распаковываем содержимое свойств conditions и actions,
							 * которое в БД хранится в запакованном (serialized) виде
							 */
							$rule->afterLoad();
							if ($this->isProductEligibleForRule($product, $rule)) {
								$this->makeIndexEntry($website, $rule, $product);
							}
						}
					}
				}
			}
		}
		return $this;
	}

	/**
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function createGiftsForRule(Mage_SalesRule_Model_Rule $rule) {
		/**
		 * @todo: С фильтрами код будет компактнее.
		 */
		/**
		 * Отбраковываем отключенные правила
		 */
		if ($rule[Df_SalesRule_Const::DB__SALESRULE__IS_ACTIVE]) {
			/**
			 * Отбраковываем истёкшие правила
			 */
			$isValid = true;
			/** @var Zend_Db_Expr $toDateAsDbExpr */
			$toDateAsDbExpr = $rule[Df_SalesRule_Const::DB__SALESRULE__TO_DATE];
			/** @var string $toDateAsString */
			$toDateAsString = (string)$toDateAsDbExpr;
			if (!df_h()->zf()->db()->isNull($toDateAsString)) {
				/** @var Zend_Date|null $toDate */
				$toDate = df()->date()->fromDb($toDateAsString, $throw = false);
				$isValid = !is_null($toDate) && df()->date()->isInFuture($toDate);
			}
			if ($isValid) {
				foreach ($this->getWebsites() as $website) {
					/** @var Mage_Core_Model_Website $website */
					/** @var Df_Varien_Data_Collection $products */
					$products = $this->getProductsByWebsite($website);
					/**
					 * Распаковываем содержимое свойств conditions и actions,
					 * которое в БД хранится в запакованном (serialized) виде
					 */
					$rule->afterLoad();
					foreach ($products as $product) {
						/** @var Df_Catalog_Model_Product $product */
						if ($this->isProductEligibleForRule($product, $rule)) {
							$this->makeIndexEntry($website, $rule, $product);
						}
					}
				}
			}
		}
		return $this;
	}

	/**
	 * @param Mage_Core_Model_Website $website
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function createGiftsForWebsite(Mage_Core_Model_Website $website) {
		/**
		 * @todo: С фильтрами код будет компактнее.
		 */
		/** @var Df_Varien_Data_Collection $products */
		$products = $this->getProductsByWebsite($website);
		$rules = $this->getPromoGiftingRulesByWebsite($website);
		/** @var Mage_SalesRule_Model_Mysql4_Rule_Collection $rules */
		foreach ($rules as $rule) {
			/** @var Mage_SalesRule_Model_Rule $rule */
			/**
			 * Распаковываем содержимое свойств conditions и actions,
			 * которое в БД хранится в запакованном (serialized) виде
			 */
			$rule->afterLoad();
			foreach ($products as $product) {
				/** @var Df_Catalog_Model_Product $product */
				if ($this->isProductEligibleForRule($product, $rule)) {
					$this->makeIndexEntry($website, $rule, $product);
				}
			}
		}
		return $this;
	}

	/** @return Df_PromoGift_Model_Resource_Indexer */
	private function deleteAllGifts() {
		rm_table_truncate($this->getMainTable(), $this->_getWriteAdapter());
		return $this;
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function deleteGiftsForProduct(Mage_Catalog_Model_Product $product) {
		$this->_getWriteAdapter()->delete(
			$this->getMainTable(), array('? = product_id' => $product->getId())
		);
		return $this;
	}

	/**
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function deleteGiftsForRule(Mage_SalesRule_Model_Rule $rule) {
		$this->_getWriteAdapter()->delete(
			$this->getMainTable(), array('? = rule_id' => $rule->getId())
		);
		return $this;
	}

	/**
	 * @param Mage_Core_Model_Website $website
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function deleteGiftsForWebsite(Mage_Core_Model_Website $website) {
		$this->_getWriteAdapter()->delete(
			$this->getMainTable(), array('? = website_id' => $website->getId())
		);
		return $this;
	}

	/** @return Df_Varien_Data_Collection */
	private function getDependentProducts() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Product_Collection $products */
			$products = Df_Catalog_Model_Product::c();
			$products->addAttributeToSelect('*');
			// Нам надо отбраковать товары,
			// которые являются составными элементами настраиваемого товара.
			$this->{__METHOD__} =
				Df_Catalog_Model_Filter_Product_Collection_DependentProductRemover::i()
					->getRejectingFilter()->filter($products)
			;
		}
		return $this->{__METHOD__};
	}
	/**
	 * @param Mage_Core_Model_Website $website
	 * @return Df_Varien_Data_Collection
	 */
	private function getProductsByWebsite(Mage_Core_Model_Website $website) {
		/** @var Df_Catalog_Model_Resource_Product_Collection $result */
		$result = Df_Catalog_Model_Product::c();
		$result->addAttributeToSelect("*");
		/**
		 * Товары привязаны к сайту (website).
		 * Так что фильтр по store на уровне SQL всё равно транслируется в фильтр по website
		 */
		$result->addWebsiteFilter($website->getId());
		/**
		 * Мы не отбраковываем невидимые товары,
		 * потому что администратор может захотеть, чтобы товары-подарки были скрыты при просмотре витрины
		 * и отображались только при выполнении условий акции и только в специальном блоке для подарков,
		 * а не в общем каталоге.
		 * @todo Откроет ли Magento карточку товара, если товар скрыт?
		 */
		/**
		 * Отбраковываем отключенные товары
		 */
		$result->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		/**
		 * А вот фильтр по наличию на складе нам бы не помешал!
		 * Или он и так по умолчанию включен?
		 */
		/**
		 * С типом аргумента всё в порядке
		 */
		df_mage()->catalogInventory()->stockSingleton()->addInStockFilterToCollection($result);
		/**
		 * Нам надо отбраковать товары,
		 * которые являются составными элементами настраиваемого товара.
		 */
		return Df_Catalog_Model_Filter_Product_Collection_DependentProductRemover::i()->filter($result);
	}

	/**
	 * Возвращает список подлежащих индексации правил.
	 *
	 * Обратите внимание, что не все эти правила
	 * являются действующими сейчас или будут действующими на момент покупки,
	 * потому что мы намеренно не отбраковали правила,
	 * недействующие сейчас, но запланированные на будущее
	 * @param Mage_Core_Model_Website $website
	 * @return Df_PromoGift_Model_Resource_Rule_Collection
	 */
	private function getPromoGiftingRulesByWebsite(Mage_Core_Model_Website $website) {
		/** @var Df_PromoGift_Model_Resource_Rule_Collection $result */
		$result = Df_PromoGift_Model_Resource_Rule_Collection::i();
		/**
		 * Отбраковываем правила, не относящиеся к обрабатываемому сайту $website
		 */
		$result->addWebsiteFilter(array($website->getId()));
		return $result;
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @return Mage_Core_Model_Abstract|Mage_Sales_Model_Quote_Item
	 */
	private function getQuoteItemMockForProduct(Mage_Catalog_Model_Product $product) {
		/** @var Mage_Sales_Model_Quote_Item $result */
		$result = df_model(Df_Sales_Const::QUOTE_ITEM_CLASS_MF);
		$result->setProduct($product);
		/**
		 * При вызове Mage_Sales_Model_Quote_Item::setQty()
		 * срабатывает сигнал sales_quote_item_qty_set_after,
		 * который обрабатывается методом Mage_CatalogInventory_Model_Observer::checkQuoteItemQty().
		 *
		 * Метод Mage_CatalogInventory_Model_Observer::checkQuoteItemQty()
		 * в Magento 1.5.0.1 в нашей ситуации ничего не делает,
		 * потому что он прерывает выполнение, получив null
		 * при вызове Mage_Sales_Model_Quote_Item::getQuote().
		 *
		 * Однако в Magento 1.4.0.1 Mage_CatalogInventory_Model_Observer::checkQuoteItemQty()
		 * не проверяет существование свойства quote, а сразу вызывает метод
		 * $quoteItem->getQuote()->getIsSuperMode(),
		 * что приводит к фатальной ошибке при отсутствии quote.
		 * Поэтому идём на хитрость,
		 * подсовывая методу Mage_CatalogInventory_Model_Observer::checkQuoteItemQty()
		 * пустой объект quote c установленным в true значением is_super_mode
		 */
		/** @var Mage_Sales_Model_Quote $quote */
		$quote = df_model(Df_Sales_Const::QUOTE_CLASS_MF, array('is_super_mode' => true));
  		$result->setQuote($quote);
		$result->setQty(1);
		return $result;
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return bool
	 */
	private function isProductEligibleForRule(
		Mage_Catalog_Model_Product $product, Mage_SalesRule_Model_Rule $rule
	) {
		/** @var Mage_SalesRule_Model_Rule_Condition_Product_Combine $actions */
		$actions = $rule->getActions();
		return $actions->validate($this->getQuoteItemMockForProduct($product));
	}

	/**
	 * @param Mage_Core_Model_Website $website
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function makeIndexEntry(
		Mage_Core_Model_Website $website
		,Mage_SalesRule_Model_Rule $rule
		,Df_Catalog_Model_Product $product
	) {
		/** @var Df_PromoGift_Model_Gift $gift */
		$gift =
			Df_PromoGift_Model_Gift::i(
				array(
					Df_PromoGift_Model_Gift::P__PRODUCT => $product
					,Df_PromoGift_Model_Gift::P__RULE => $rule
					,Df_PromoGift_Model_Gift::P__WEBSITE => $website
				)
			)
		;
		$gift->setDataChanges(true);
		$gift->save();
		return $this;
	}

	/** @return Mage_Core_Model_Website[] */
	private function getWebsites() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $result */
			$this->{__METHOD__} =
				Mage::app()->getWebsites(
					false	// включать ли сайт с идентификатором 0 (административный)
					,false	// использовать ли коды сайтов в качестве ключей массива
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function restoreObjectToNormalMode(Mage_Core_Model_Abstract $object) {
		$fields = $this->_getWriteAdapter()->describeTable($this->getMainTable());
		/** @var string[] $serializableFields */
		$serializableFields =
			!isset($this->_serializableFields)
			? array()
			: array_keys($this->_serializableFields)
		;
		df_assert_array($serializableFields);
		foreach (array_keys($fields) as $field) {
			if (in_array($field, $serializableFields)) {
				continue;
			}
			/** @var string $method */
			$method = 'set' . uc_words ($field, '');
			if (
				/**
				 * К сожалению, нельзя здесь для проверки публичности метода
				 * использовать is_callable,
				 * потому что наличие Varien_Object::__call
				 * приводит к тому, что is_callable всегда возвращает true.
				 */
				method_exists($object, $method)
			) {
				$object->setDataUsingMethod($field, $object->getData($field));
			}
		}
		return $this;
	}

	/**
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_PromoGift_Model_Resource_Indexer
	 */
	private function switchObjectToDbMode(Mage_Core_Model_Abstract $object) {
		/** @var mixed[] $fields */
		$fields =
			$this->_getWriteAdapter()->describeTable(
				$this->getMainTable()
			)
		;
		df_assert_array($fields);
		/** @var string[] $serializableFields */
		$serializableFields =
			!isset($this->_serializableFields)
			? array()
			: array_keys($this->_serializableFields)
		;
		df_assert_array($serializableFields);
		foreach (array_keys($fields) as $field) {
			/** @var string $field */
			if (in_array($field, $serializableFields)) {
				continue;
			}
			/** @var string $method */
			$method = 'get' . uc_words ($field, '');
			if (
				/**
				 * К сожалению, нельзя здесь для проверки публичности метода
				 * использовать is_callable,
				 * потому что наличие Varien_Object::__call
				 * приводит к тому, что is_callable всегда возвращает true.
				 */
				method_exists($object, $method)
			) {
				$object->setData($field, $object->getDataUsingMethod($field));
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(Df_PromoGift_Model_Resource_Gift::MAIN_TABLE, Df_PromoGift_Model_Gift::P__ID);
	}
	const _CLASS = __CLASS__;
	/**
	 * @see Df_PromoGift_Model_Indexer::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_PromoGift_Model_Resource_Indexer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}