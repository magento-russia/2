<?php
/**
 * Цель перекрытия:
 *
 * некоторые сторонние оформительские темы не поддерживают режим денормализации,
 * поэтому желательно иметь возможность отключать режим денормализации для конкретных коллекций.
 */
class Df_Reports_Model_Resource_Product_Collection 
	extends Mage_Reports_Model_Resource_Product_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Resource_Abstract|array(string => mixed) $resource
	 */
	public function __construct($resource = null) {
		if (is_array($resource)) {
			$this->_rmData = $resource;
			$resource = null;
		}
		parent::__construct($resource);
	}

	/**
	 * Цель перекрытия:
	 *
	 * некоторые сторонние оформительские темы не поддерживают режим денормализации,
	 * поэтому желательно иметь возможность отключать режим денормализации для конкретных коллекций.
	 *
	 * @override
	 * @return bool
	 */
	public function isEnabledFlat() {
		/** @var bool $isFlatDisabled */
		$isFlatDisabled = (true === $this->getRmData(self::P__DISABLE_FLAT));
		return !$isFlatDisabled && parent::isEnabledFlat();
	}

	/**
	 * @param string|null $paramName [optional]
	 * @return mixed
	 */
	private function getRmData($paramName = null) {
		return is_null($paramName) ?  $this->_rmData : dfa($this->_rmData, $paramName);
	}

	const P__DISABLE_FLAT = 'disable_flat';

	/** @var array(string => mixed) */
	private $_rmData = [];
}