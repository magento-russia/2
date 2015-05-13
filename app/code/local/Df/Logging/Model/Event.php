<?php
/**
 * @method Df_Logging_Model_Resource_Event getResource()
 */
class Df_Logging_Model_Event extends Df_Core_Model_Abstract {
	/** @return bool */
	public function hasChanges() {
		return $this->getId() && !!$this->getResource()->getEventChangeIds($this->getId());
	}

	/**
	 * @override
	 * @return Df_Logging_Model_Event
	 */
	protected function _beforeSave() {
		if (!$this->getId()) {
			$this->setStatus($this->getIsSuccess() ? self::RESULT_SUCCESS : self::RESULT_FAILURE);
			if (!$this->getUser() && $id = $this->getUserId()) {
				$this->setUser(df_model('admin/user')->load($id)->getUserName());
			}
			if (!$this->hasTime()) {
				$this->setTime(time());
			}
		}
		return parent::_beforeSave();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Logging_Model_Resource_Event::mf());
	}
	const _CLASS = __CLASS__;
	const P__ID = 'log_id';
	const RESULT_SUCCESS = 'success';
	const RESULT_FAILURE = 'failure';

	/** @return Df_Logging_Model_Resource_Event_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Logging_Model_Event
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Logging_Model_Event
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Logging_Model_Resource_Event_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Logging_Model_Event */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}