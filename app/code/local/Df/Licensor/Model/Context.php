<?php
class Df_Licensor_Model_Context extends Df_Core_Model_Abstract {
	/** @return string */
	public function getScope() {
		if (!isset($this->_scope)) {
			$this->_scope =
				$this->getStore()
				? self::SCOPE_STORE
				: ($this->getWebsite() ? self::SCOPE_WEBSITE : self::SCOPE_DEFAULT)
			;
		}
		return $this->_scope;
	}
	/** @var int */
	private $_scope;

	/**
	 * Возвращает редактируемый в данный момент магазин
	 * @return Mage_Core_Model_Store
	 */
	public function getStore() {
		if (!isset($this->_store)) {
			$this->_store =
				null === df_request(self::REQUEST_STORE)
				? null
				// getStore способна находить магазин не только по идентификатору,
				// но и по коду (например: «french»)
				: Mage::app()->getStore(df_request(self::REQUEST_STORE));
			;
		}
		return $this->_store;
	}
	/** @var  Mage_Core_Model_Store */
	private $_store;

	/**
	 * Перечисляет все магазины, на которые действуют текущие настройки
	 * @return Df_Licensor_Model_Collection_Store
	 */
	public function getStores() {
		if (!$this->_stores) {
			$this->_stores = Df_Licensor_Model_Collection_Store::i();
			if (self::SCOPE_STORE === $this->getScope()) {
				$this->_stores->addItem(Df_Licensor_Model_Store::i($this->getStore()));
			}
			else {
				/** @var array|Mage_Core_Model_Resource_Store_Collection|Mage_Core_Model_Mysql4_Store_Collection $collection */
				$collection =
					(self::SCOPE_WEBSITE === $this->getScope())
					? $this->getWebsite()->getStores()
					: Df_Core_Model_Store::c($loadDefault = true)
				;
				foreach ($collection as $store) {
					/** @var Mage_Core_Model_Store $store */
					$this->_stores->addItem(Df_Licensor_Model_Store::i($store));
				}
			}
		}
		return $this->_stores;
	}
	/** @var Df_Licensor_Model_Collection_Store */
	private $_stores;

	/**
	 * Возвращает редактируемый в данный момент сайт
	 * @return Mage_Core_Model_Website|null
	 */
	public function getWebsite() {
		if (!isset($this->_website)) {
			$this->_website =
				null === df_request(self::REQUEST_WEBSITE)
				? null
				// getWebsite способна находить магазин не только по идентификатору,
				// но и по коду (например: «base»)
				: Mage::app()->getWebsite(df_request(self::REQUEST_WEBSITE))
			;
		}
		return $this->_website;
	}
	/** @var  Mage_Core_Model_Website */
	private $_website;

	const _CLASS = __CLASS__;
	const REQUEST_STORE = 'store';
	const REQUEST_WEBSITE = 'website';
	const SCOPE_DEFAULT = 'default';
	const SCOPE_STORE = 'store';
	const SCOPE_WEBSITE = 'website';

	/** @return Df_Licensor_Model_Context */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}