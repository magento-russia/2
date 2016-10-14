<?php
/**
 * @method Df_Rating_Model_Rating setRatingCodes(array $value)
 * @method Df_Rating_Model_Rating setStores(array $value)
 */
class Df_Rating_Model_Rating extends Mage_Rating_Model_Rating {
	/**
	 * В Magento CE 1.4.0.1 класс Mage_Rating_Model_Rating
	 * по-уродски перекрывает конструктор:
	 * @see Mage_Rating_Model_Rating::__construct()
	 * Возвращаем конструктор из @see Varien_Object::__construct()
	 * @override
	 * @return Df_Rating_Model_Rating
	 */
	public function __construct() {
		$args = func_get_args();
		if (empty($args[0])) {
			$args[0] = array();
		}
		$this->_data = $args[0];
		$this->_construct();
	}

	/**
	 * @param int $code
	 * @return int
	 */
	public function getOptionIdByCode($code) {
		df_param_integer($code, 0);
		df_param_between($code, 0, 1, 5);
		return df_nat(dfa($this->getMapFromCodeToOptionId(), $code));
	}

	/** @return string|null */
	public function getRatingCode() {
		/** @var string|null $result */
		$result = $this->_getData(self::P__RATING_CODE);
		if (!is_null($result)) {
			df_result_string_not_empty($result);
		}
		return $result;
	}

	/**
	 * @override
	 * @return Df_Rating_Model_Resource_Rating_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @param int $value|null
	 * @return Df_Rating_Model_Rating
	 */
	public function setCustomerId($value) {
		if (!is_null($value)) {
			df_param_integer($value, 0);
			df_param_between($value, 0, 1);
		}
		$this->setData(self::P__CUSTOMER_ID, $value);
		return $this;
	}

	/**
	 * @param int $value
	 * @return Df_Rating_Model_Rating
	 */
	public function setEntityId($value) {
		df_param_integer($value, 0);
		$this->setData(self::P__ENTITY_ID, $value);
		return $this;
	}

	/**
	 * @param int $value
	 * @return Df_Rating_Model_Rating
	 */
	public function setPosition($value) {
		df_param_integer($value, 0);
		$this->setData(self::P__POSITION, $value);
		return $this;
	}

	/**
	 * @param string $value
	 * @return Df_Rating_Model_Rating
	 */
	public function setRatingCode($value) {
		df_param_string($value, 0);
		$this->setData(self::P__RATING_CODE, $value);
		return $this;
	}

	/**
	 * @param int $value|null
	 * @return Df_Rating_Model_Rating
	 */
	public function setRatingId($value) {
		if (!is_null($value)) {
			df_param_integer($value, 0);
			df_param_between($value, 0, 1);
		}
		$this->setData(self::P__RATING_ID, $value);
		return $this;
	}

	/**
	 * @param int $value|null
	 * @return Df_Rating_Model_Rating
	 */
	public function setReviewId($value) {
		if (!is_null($value)) {
			df_param_integer($value, 0);
			df_param_between($value, 0, 1);
		}
		$this->setData(self::P__REVIEW_ID, $value);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Rating_Model_Resource_Rating
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Rating_Model_Resource_Rating::s();}

	/** @return array(int => int) */
	private function getMapFromCodeToOptionId() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => int) $result  */
			$result = array();
			foreach ($this->getOptions() as $option) {
				/** @var Mage_Rating_Model_Rating_Option $option */
				$result[df_nat0($option->getCode())] = df_nat0($option->getId());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Rating::getEntityClass()
	 * @used-by Df_Rating_Model_Resource_Rating_Collection::_construct()
	 */
	const _C = __CLASS__;
	const P__CUSTOMER_ID = 'customer_id';
	const P__ENTITY_ID = 'entity_id';
	const P__POSITION = 'position';
	const P__RATING_CODE = 'rating_code';
	const P__RATING_ID = 'rating_id';
	const P__REVIEW_ID = 'review_id';

	/** @return Df_Rating_Model_Resource_Rating_Collection */
	public static function c() {return new Df_Rating_Model_Resource_Rating_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Rating_Model_Rating
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Rating_Model_Rating */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}