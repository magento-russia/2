<?php
/**
 * @method Df_Logging_Model_Resource_Event_Changes getResource()
 */
class Df_Logging_Model_Event_Changes extends Df_Core_Model_Abstract {
	/**
	 * Set skip fields and clear model data
	 * @param mixed[] $skipFields
	 */
	public function cleanupData($skipFields) {
		if ($skipFields && is_array($skipFields)) {
			$this->_skipFields = $skipFields;
		}
		$this->setOriginalData($this->_cleanupData($this->getOriginalData()));
		$this->setResultData($this->_cleanupData($this->getResultData()));
	}

	/**
	 * Getter for source id of event changes
	 * Used to save compatibility with older versions
	 * @deprecated after 1.6.0.0
	 * @return string
	 */
	public function getModelId() {
		return $this->getSourceId();
	}

	/**
	 * Getter for source name of event changes
	 * Used to save compatibility with older versions
	 * @deprecated after 1.6.0.0
	 * @return string
	 */
	public function getModelName() {
		return $this->getSourceName();
	}

	/**
	 * Define if current model has difference between original and result data
	 * @return bool
	 */
	public function hasDifference() {return !!$this->_calculateDifference();}

	/**
	 * Setter for source id of event changes
	 * Used to save compatibility with older versions
	 * @deprecated after 1.6.0.0
	 * @param string $modelId
	 */
	public function setModelId($modelId) {
		$this->setSourceId($modelId);
	}

	/**
	 * Setter for source name of event changes
	 * Used to save compatibility with older versions
	 * @deprecated after 1.6.0.0
	 * @param string $modelName
	 */
	public function setModelName($modelName) {
		$this->setSourceName($modelName);
	}

	/**
	 * @override
	 * @return Df_Logging_Model_Event
	 */
	protected function _beforeSave() {
		$this->_calculateDifference();
		$this->setOriginalData(serialize($this->getOriginalData()));
		$this->setResultData(serialize($this->getResultData()));
		return parent::_beforeSave();
	}

	/**
	 * Calculate difference between original and result data and return that difference
	 * @return null|array|int
	 */
	protected function _calculateDifference() {
		if (is_null($this->_difference)) {
			$updatedParams = $newParams = $sameParams = $difference = array();
			$newOriginalData = $origData = $this->getOriginalData();
			$newResultData = $resultData = $this->getResultData();
			if (!is_array($origData)) {
				$origData = array();
			}
			if (!is_array($resultData)) {
				$resultData = array();
			}

			if (!$origData && $resultData) {
				$newOriginalData = array('__was_created' => true);
				$difference = $resultData;
			}
			else if ($origData && !$resultData) {
				$newResultData = array('__was_deleted' => true);
				$difference = $origData;
			}
			else if ($origData && $resultData) {
				$newParams  = array_diff_key($resultData, $origData);
				$sameParams = array_intersect_key($origData, $resultData);
				foreach ($sameParams as $key => $value) {
					if ($origData[$key] != $resultData[$key]){
						$updatedParams[$key] = $resultData[$key];
					}
				}
				$newOriginalData = array_intersect_key($origData, $updatedParams);
				$difference = $newResultData = array_merge($updatedParams, $newParams);
				if ($difference && !$updatedParams) {
					$newOriginalData = array('__no_changes' => true);
				}
			}

			$this->setOriginalData($newOriginalData);
			$this->setResultData($newResultData);
			$this->_difference = $difference;
		}
		return $this->_difference;
	}

	/**
	 * Clear model data from objects, arrays and fields that should be skipped
	 * @param array $data
	 * @return array
	 */
	protected function _cleanupData($data) {
		if (!$data && !is_array($data)) {
			return array();
		}
		$skipFields = $this->_skipFields;
		if (!$skipFields || !is_array($skipFields)) {
			$skipFields = array();
		}
		$clearedData = array();
		foreach ($data as $key => $value) {
			if (!in_array($key, $this->_globalSkipFields) && !in_array($key, $skipFields) && !is_array($value) && !is_object($value)) {
				$clearedData[$key] = $value;
			}
		}
		return $clearedData;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Logging_Model_Resource_Event_Changes::mf());
		$this->_globalSkipFields =
			df_clean(
				df_parse_csv(
					(string)Mage::getConfig()->getNode(self::XML_PATH_SKIP_GLOBAL_FIELDS)
				)
			)
		;
	}

	/**
	 * Set of fields that should not be logged for all models
	 * @var array
	 */
	protected $_globalSkipFields = array();
	/**
	 * Set of fields that should not be logged per expected model
	 * @var array
	 */
	protected $_skipFields = array();
	/**
	 * Store difference between original data and result data of model
	 * @var array
	 */
	protected $_difference = null;

	const _CLASS = __CLASS__;
	const P__ID = 'id';
	const XML_PATH_SKIP_GLOBAL_FIELDS = 'adminhtml/df/logging/skip_fields';

	/** @return Df_Logging_Model_Resource_Event_Changes_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Logging_Model_Event_Changes
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Logging_Model_Event_Changes
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Logging_Model_Resource_Event_Changes_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Logging_Model_Event_Changes */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}