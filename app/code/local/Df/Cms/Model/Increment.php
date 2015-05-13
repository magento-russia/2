<?php
/**
 * @method Df_Cms_Model_Resource_Increment getResource()
 */
class Df_Cms_Model_Increment extends Df_Core_Model_Abstract {
	/**
	 * Generate new increment id for passed type, node and level.
	 * @param int $type
	 * @param int $node
	 * @param int $level
	 * @return string
	 */
	public function getNewIncrementId($type, $node, $level) {
		$this->loadByTypeNodeLevel($type, $node, $level);
		// if no counter for such combination we need to create new
		if (!$this->getId()) {
			$this->setType($type)
				->setNode($node)
				->setLevel($level);
		}

		$newIncrementId = $this->_getNextId();
		$this->setLastId($newIncrementId)->save();
		return $newIncrementId;
	}

	/**
	 * Load increment counter by passed node and level
	 * @param int $type
	 * @param int $node
	 * @param int $level
	 * @return Df_Cms_Model_Increment
	 */
	public function loadByTypeNodeLevel($type, $node, $level) {
		$this->getResource()->loadByTypeNodeLevel($this, $type, $node, $level);
		return $this;
	}

	/**
	 * Get incremented value of counter.
	 * @return mixed
	 */
	protected function _getNextId() {
		$incrementId = $this->getLastId();
		if ($incrementId) {
			$incrementId++;
		} else {
			$incrementId = 1;
		}
		return $incrementId;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Cms_Model_Resource_Increment::mf());
	}
	const _CLASS = __CLASS__;
	/*
	 * Increment levels
	 */
	const LEVEL_VERSION = 0;
	const LEVEL_REVISION = 1;
	const P__ID = 'increment_id';
	/*
	 * Increment types
	 */
	const TYPE_PAGE = 0;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Increment
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Cms_Model_Increment
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}