<?php
class Df_PromoGift_Model_Resource_Indexer extends Df_Core_Model_Resource {
	/**
	 * Перестраивает таблицу подарков полностью
	 * @used-by Mage_Index_Model_Indexer_Abstract::reindexAll()
	 * @return void
	 */
	public function reindexAll() {
		try {
			// cначала полностью очищаем таблицу от старых данных
			$this->deleteAllGifts();
			// затем записываем в таблицу новые данные
			$this->createGiftsForAllWebsites();
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}

	/**
	 * @used-by Df_PromoGift_Model_Indexer::_processEvent()
	 * @param Df_Catalog_Model_Product $product
	 * @return void
	 */
	public function reindexProduct(Df_Catalog_Model_Product $product) {
		$this->deleteGiftsForProduct($product);
		$this->createGiftsForProduct($product);
	}

	/**
	 * @used-by Df_PromoGift_Model_Indexer::_processEvent()
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return void
	 */
	public function reindexRule(Mage_SalesRule_Model_Rule $rule) {
		$this->deleteGiftsForRule($rule);
		$this->createGiftsForRule($rule);
	}

	/**
	 * @used-by Df_PromoGift_Model_Indexer::_processEvent()
	 * @param Mage_Core_Model_Website $website
	 * @return void
	 */
	public function reindexWebsite(Mage_Core_Model_Website $website) {
		$this->deleteGiftsForWebsite($website);
		$this->createGiftsForWebsite($website);
	}

	/**
	 * @used-by reindexAll()
	 * @return void
	 */
	private function createGiftsForAllWebsites() {
		foreach ($this->getWebsites() as $website) {
			/** @var Mage_Core_Model_Website $website */
			$this->createGiftsForWebsite($website);
		}
	}

	/**
	 * @used-by reindexProduct()
	 * @param Df_Catalog_Model_Product $product
	 * @return void
	 */
	private function createGiftsForProduct(Df_Catalog_Model_Product $product) {
		if ($product->isInStock()) {
			foreach ($this->getWebsites() as $website) {
				/** @var Mage_Core_Model_Website $website */
				// Нам нужно отсеять сайты, к которым товар не относится
				if (in_array($website->getId(), $product->getWebsiteIds())) {
					/** @var Df_PromoGift_Model_Resource_Rule_Collection $rules */
					$rules = $this->getPromoGiftingRulesByWebsite($website);
					foreach ($rules as $rule) {
						/** @var Df_PromoGift_Model_Rule $rule */
						// Распаковываем содержимое свойств conditions и actions,
						// которое в БД хранится в запакованном (serialized) виде
						$rule->afterLoad();
						if ($this->isEligibleForRule($product, $rule)) {
							$this->makeIndexEntry($website, $rule, $product);
						}
					}
				}
			}
		}
	}

	/**
	 * @used-by reindexRule()
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return void
	 */
	private function createGiftsForRule(Mage_SalesRule_Model_Rule $rule) {
		// @todo: С фильтрами код будет компактнее.
		// Отбраковываем отключенные правила
		if ($rule->getIsActive()) {
			// Отбраковываем истёкшие правила
			$isValid = true;
			/** @var Zend_Db_Expr $toDateAsDbExpr */
			$toDateAsDbExpr = $rule->getToDate();
			/** @var string $toDateAsString */
			$toDateAsString = (string)$toDateAsDbExpr;
			if (!df_h()->zf()->db()->isNull($toDateAsString)) {
				/** @var Zend_Date|null $toDate */
				$toDate = df()->date()->fromDb($toDateAsString, $throw = false);
				$isValid = !is_null($toDate) && !df_is_date_expired($toDate);
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
						if ($this->isEligibleForRule($product, $rule)) {
							$this->makeIndexEntry($website, $rule, $product);
						}
					}
				}
			}
		}
	}

	/**
	 * @used-by createGiftsForAllWebsites()
	 * @used-by reindexWebsite()
	 * @param Mage_Core_Model_Website $website
	 * @return void
	 */
	private function createGiftsForWebsite(Mage_Core_Model_Website $website) {
		/**
		 * @todo: С фильтрами код будет компактнее.
		 */
		/** @var Df_Varien_Data_Collection $products */
		$products = $this->getProductsByWebsite($website);
		$rules = $this->getPromoGiftingRulesByWebsite($website);
		/** @var Df_PromoGift_Model_Resource_Rule_Collection $rules */
		foreach ($rules as $rule) {
			/** @var Df_PromoGift_Model_Rule $rule */
			/**
			 * Распаковываем содержимое свойств conditions и actions,
			 * которое в БД хранится в запакованном (serialized) виде
			 */
			$rule->afterLoad();
			foreach ($products as $product) {
				/** @var Df_Catalog_Model_Product $product */
				if ($this->isEligibleForRule($product, $rule)) {
					$this->makeIndexEntry($website, $rule, $product);
				}
			}
		}
	}

	/**
	 * @used-by reindexAll()
	 * @return void
	 */
	private function deleteAllGifts() {
		df_table_truncate($this->getMainTable(), $this->_getWriteAdapter());
	}

	/**
	 * @used-by reindexProduct()
	 * @param Mage_Catalog_Model_Product $product
	 * @return void
	 */
	private function deleteGiftsForProduct(Mage_Catalog_Model_Product $product) {
		df_table_delete($this->getMainTable(), 'product_id', $product->getId());
	}

	/**
	 * @used-by reindexRule()
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return void
	 */
	private function deleteGiftsForRule(Mage_SalesRule_Model_Rule $rule) {
		df_table_delete($this->getMainTable(), 'rule_id', $rule->getId());
	}

	/**
	 * @used-by reindexWebsite()
	 * @param Mage_Core_Model_Website $website
	 * @return void
	 */
	private function deleteGiftsForWebsite(Mage_Core_Model_Website $website) {
		df_table_delete($this->getMainTable(), 'website_id', $website->getId());
	}

	/**
	 * @param Mage_Core_Model_Website $website
	 * @return Df_Varien_Data_Collection
	 */
	private function getProductsByWebsite(Mage_Core_Model_Website $website) {
		/** @var Df_Catalog_Model_Resource_Product_Collection $result */
		$result = Df_Catalog_Model_Product::c();
		$result->addAttributeToSelect('*');
		// Товары привязаны к сайту (website).
		// Так что фильтр по store на уровне SQL всё равно транслируется в фильтр по website.
		$result->addWebsiteFilter($website->getId());
		// Мы не отбраковываем невидимые товары,
		// потому что администратор может захотеть, чтобы товары-подарки были скрыты при просмотре витрины
		// и отображались только при выполнении условий акции и только в специальном блоке для подарков,
		// а не в общем каталоге.
		// @todo Откроет ли Magento карточку товара, если товар скрыт?
		// отбраковываем отключенные товары
		$result->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		// Фильтр по наличию на складе
		// С типом аргумента всё в порядке
		df_mage()->catalogInventory()->stockSingleton()->addInStockFilterToCollection($result);
		return $result;
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
		// Отбраковываем правила, не относящиеся к обрабатываемому сайту $website
		return Df_PromoGift_Model_Rule::c()->addWebsiteFilter(array($website->getId()));
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @return Df_Sales_Model_Quote_Item
	 */
	private function getQuoteItemMockForProduct(Mage_Catalog_Model_Product $product) {
		/** @var Df_Sales_Model_Quote_Item $result */
		$result = Df_Sales_Model_Quote_Item::i();
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
		/** @var Df_Sales_Model_Quote $quote */
		$quote = Df_Sales_Model_Quote::i(array('is_super_mode' => true));
  		$result->setQuote($quote);
		$result->setQty(1);
		return $result;
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return bool
	 */
	private function isEligibleForRule(Mage_Catalog_Model_Product $product, Mage_SalesRule_Model_Rule $rule) {
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
		$gift = Df_PromoGift_Model_Gift::i(array(
			Df_PromoGift_Model_Gift::P__PRODUCT => $product
			,Df_PromoGift_Model_Gift::P__RULE => $rule
			,Df_PromoGift_Model_Gift::P__WEBSITE => $website
		));
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
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_init(Df_PromoGift_Model_Resource_Gift::TABLE, Df_PromoGift_Model_Gift::P__ID);
	}
	/** @return Df_PromoGift_Model_Resource_Indexer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}