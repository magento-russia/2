<?php
/**
 * @method Df_Cms_Model_Resource_Block getResource()
 */
class Df_Cms_Model_Block extends Mage_Cms_Model_Block {
	/** @return void */
	public function loadStoresInfo() {
		if ($this->getId() && !$this->getData(self::P__STORES)) {
			/** @var int[] $stores */
			$stores = $this->getResource()->lookupStoreIds($this->getId());
			/** @var bool $hasDataChanges */
			$hasDataChanges = $this->hasDataChanges();
			$this->addData(array(
				self::P__STORES => $stores
				,self::P__STORE_ID => $stores
			));
			$this->setDataChanges($hasDataChanges);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Cms_Model_Resource_Block::mf());
	}
	const _CLASS = __CLASS__;
	const P__IDENTIFIER = 'identifier';
	const P__STORE_ID = 'store_id';
	const P__STORES = 'stores';

	/**
	 * @param bool $loadStoresInfo [optional]
	 * @return Df_Cms_Model_Resource_Block_Collection
	 */
	public static function c($loadStoresInfo = false) {
		return Df_Cms_Model_Resource_Block_Collection::i($loadStoresInfo);
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Block
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Cms_Model_Block
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @param string $identifier
	 * @return Df_Cms_Model_Block
	 */
	public static function loadByIdentifier($identifier) {
		df_param_string_not_empty($identifier, 0);
		return df_load(__CLASS__, $identifier, self::P__IDENTIFIER, $throwOnError = false);
	}
	/**
	 * @see Df_Cms_Model_Resource_Block_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Cms_Model_Block */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}