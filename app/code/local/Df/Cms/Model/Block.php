<?php
/**
 * @method Df_Cms_Model_Resource_Block getResource()
 */
class Df_Cms_Model_Block extends Mage_Cms_Model_Block {
	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Block_Collection
	 */
	public function getResourceCollection() {return self::c();}

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
	 * @return Df_Cms_Model_Resource_Block
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Cms_Model_Resource_Block::s();}

	/**
	 * @used-by Df_Cms_Model_Resource_Block_Collection::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Block::getEntityClass()
	 * @used-by Df_Localization_Onetime_Processor_Cms_Block::_construct()
	 */

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
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}