<?php
class Df_Core_State {
	/** @return void */
	public function blocksGenerationStarted() {$this->_blocksGenerationStarted = true;}
	/** @return void */
	public function blocksHasBeenGenerated() {$this->_blocksHasBeenGenerated = true;}

	/**
	 * @used-by Df_Localization_Realtime_Dictionary::handleForBlock()
	 * @return Mage_Core_Block_Abstract|null
	 */
	public function block() {return df_last($this->_blockStack);}

	/**
	 * @used-by Df_Core_Observer::core_block_abstract_to_html_before()
	 * @param Mage_Core_Block_Abstract $value
	 * @return void
	 */
	public function blockSet($value) {$this->_blockStack[]= $value;}

	/**
	 * @used-by Df_Core_Observer::core_block_abstract_to_html_after()
	 * array_pop() pops and returns the last value of the array,
	 * shortening the array by one element.
	 * If array is empty (or is not an array), null will be returned.
	 * Will additionally produce a Warning when called on a non-array.
	 * @return void
	 */
	public function blockSetPrev() {array_pop($this->_blockStack);}

	/**
	 * @used-by rm_controller()
	 * @return Mage_Core_Controller_Varien_Action|null
	 */
	public function getController() {return $this->_controller;}

	/**
	 * @param bool $recalculate [optional]
	 * @return Df_Catalog_Model_Category
	 */
	public function getCurrentCategory($recalculate = false) {
		if (!isset($this->{__METHOD__}) || $recalculate) {
			/**
			 * Обратите внимание, что узнавать текущий раздел напрямую через реестр
			 * Mage::registry('current_category')
			 * неправильно, потому что товарный список может отображаться на главной странице
			 * и вообще на любой статейной странице через синтаксис
			 * {{block
					type="catalog/product_list"
					category_id="3"
					column_count="4"
					template="catalog/product/list.phtml"
				}}
			 * В этом случае текущий товарный раздел category_id сам собой в реестр не попадает
			 * (в обычной ситуации попадает через контроллер:
			 * @see Mage_Catalog_CategoryController::_initCatagory()
			 * ),
			 * и правильно узнавать текущий товарный раздел через
			 * @see Mage_Catalog_Model_Layer::getCurrentCategory(),
			 * и вот этот метод как раз и заносит значение в реестр.
			 * В этом случае текущий товарный раздел типо как отсутствует.
			 */
			$this->{__METHOD__} = df_mage()->catalog()->layerSingleton()->getCurrentCategory();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $recalculate [optional]
	 * @return int
	 */
	public function getCurrentCategoryId($recalculate = false) {
		return (int)$this->getCurrentCategory($recalculate)->getId();
	}

	/** @return string */
	public function getCurrentDesignPackage() {
		/** @var string $result */
		$result = rm_design_package()->getPackageName();
		if (!$result) {
			$result = Mage_Core_Model_Design_Package::DEFAULT_PACKAGE;
		}
		return $result;
	}

	/** @return string */
	public function getCurrentDesignTheme() {
		/** @var string $result */
		$result = rm_design_package()->getTheme('default');
		if (!$result) {
			$result = Mage_Core_Model_Design_Package::DEFAULT_THEME;
		}
		return $result;
	}

	/**
	 * 2015-11-04
	 * При нахождении в административном интерфейсе
	 * и при отсутствии в веб-адресе идентификатора магазина
	 * этот метод вернёт витрину по по-умолчанию,
	 * а не витрину «admin», как делает стандартный метод
	 * @see Mage_Core_Model_App::getStore() при вызове без параметров,
	 * Не знаю, правильно ли это, но раз уж так работает — решил пока не трогать.
	 *
	 * В Magento 2 же стандартный метод \Magento\Store\Model\StoreManager::getStore()
	 * при вызове без параметров возвращает именно витрину по умолчанию, а не витрину «admin»:
	 * https://github.com/magento/magento2/issues/2254
	 * «The call for \Magento\Store\Model\StoreManager::getStore() without parameters
	 * inside the backend returns the default frontend store, not the «admin» store,
	 * which is inconsistent with Magento 1.x behaviour and I think it will lead to developer mistakes.»
	 *
	 * @param bool $needThrow [optional]
	 * @return Df_Core_Model_StoreM|null
	 * @throws Df_Core_Exception|Exception
	 */
	public function getStoreProcessed($needThrow = true) {
		if (!isset($this->_storeProcessed)) {
			/** @var Df_Core_Model_StoreM|null $result */
			$result = null;
			if (Mage::app()->isSingleStoreMode()) {
				/**
				 * 2015-08-10
				 * Нельзя использовать здесь @see rm_store(),
				 * потому что @see rm_store() сам использует @see getStoreProcessed(), и получится зависание.
				 */
				$result = Mage::app()->getStore(true);
			}
			else {
				/**
				 * Если в системе присутствует больше одного магазина,
				 * то администратор должен указать обрабатываемый магазин
				 * параметром в запрашиваемом адресе одним из двух способов:
				 *
				 * 1) http://localhost.com:686/df-1c/cml2/index/?store-view=store_686
				 * 2) http://localhost.com:686/df-1c/cml2/index/store-view/store_686/
				 */
				/** @var string $storeCode */
				$storeCode = rm_request('store-view');
				if (is_null($storeCode)) {
					$storeCode = rm_preg_match(
						'#\/store\-view\/([^\/]+)\/#u', rm_ruri(), $needThrow = false
					);
				}
				if (!$storeCode) {
					if ($needThrow) {
						df_error(
							'Ваша система содержит несколько витрин,'
							. ' поэтому Вы должны указать системное имя обрабатываемой витрины'
							. ' в веб-адресе, добавив к веб-адресу окончание'
							. ' «/store-view/<системное имя витрины>/».'
						);
					}
				}
				else {
					df_assert_string_not_empty($storeCode);
					try {
						/**
						 * 2015-08-10
						 * Нельзя использовать здесь @see rm_store(),
						 * потому что @see rm_store() сам использует @see getStoreProcessed(), и получится зависание.
						 */
						$result = Mage::app()->getStore($storeCode);
					}
					catch (Mage_Core_Model_Store_Exception $e) {
						if ($needThrow) {
							df_error(
								'Витрина с системным именем «%s» отсутствует в Вашей системе.'
								, $storeCode
							);
						}
					}
				}
			}
			if (!$result) {
				df_assert(!$needThrow);
			}
			else {
				df_assert($result instanceof Df_Core_Model_StoreM);
				if (!$result->getWebsiteId()) {
					// Так бывает...
					$result = df_model('core/store')->load($result->getId());
					df_assert($result->getWebsiteId());
				}
			}
			$this->_storeProcessed = rm_n_set($result);
		}
		return rm_n_get($this->_storeProcessed);
	}

	/** @return bool */
	public function hasBlocksBeenGenerated() {return $this->_blocksHasBeenGenerated;}

	/** @return bool */
	public function hasBlocksGenerationBeenStarted() {return $this->_blocksGenerationStarted;}

	/** @return bool */
	public function hasCategory() {return !($this->getCurrentCategory()->isRoot());}

	/** @return bool */
	public function hasLayoutRenderingBeenStarted() {return $this->_layoutRenderingHasBeenStarted;}

	/** @return bool */
	public function isStoreInitialized() {
		/** @var bool $result */
		static $result = false;
		if (!$result) {
			try {
				rm_store();
				$result = true;
			}
			catch (Mage_Core_Model_Store_Exception $e) {}
		}
		return $result;
	}

	/** @return void */
	public function layoutRenderingHasBeenStarted() {$this->_layoutRenderingHasBeenStarted = true;}

	/**
	 * @used-by Df_Core_Observer::controller_action_predispatch()
	 * @param Mage_Core_Controller_Varien_Action $controller
	 * @return void
	 */
	public function setController(Mage_Core_Controller_Varien_Action $controller) {
		$this->_controller = $controller;
	}

	/**
	 * Этот метод предназначен для модульного тестирования,
	 * когда требуется указать текущий магазин вручную.
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return void
	 */
	public function setStoreProcessed($store = null) {$this->_storeProcessed = rm_store($store);}

	/** @var bool */
	private $_blocksGenerationStarted = false;
	/** @var bool */
	private $_blocksHasBeenGenerated = false;
	/**
	 * 2015-09-02
	 * Значение по умолчанию null можно не указывать.
	 * @var Mage_Core_Controller_Varien_Action|null
	 */
	private $_controller;
	/**
	 * @used-by block()
	 * @used-by blockSet()
	 * @used-by blockSetPrev()
	 * @var array(Mage_Core_Block_Abstract|null)
	 */
	private $_blockStack = array();
	/** @var bool */
	private $_layoutRenderingHasBeenStarted = false;
	/**
	 * 2015-09-02
	 * Значение по умолчанию null можно не указывать.
	 * @var Df_Core_Model_StoreM|null
	 */
	private $_storeProcessed;

	/** @return Df_Core_State */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}