<?php
/**
 * @method string getEmail()
 * @method Df_Customer_Model_Resource_Customer getResource()
 * @method Df_Customer_Model_Customer setEmail($value)
 * @method Df_Customer_Model_Customer setGroupId($value)
 * @method Df_Customer_Model_Customer setPassword($value)
 * @method Df_Customer_Model_Customer setWebsiteId($value)
 */
class Df_Customer_Model_Customer extends Mage_Customer_Model_Customer {
	/**
	 * Этот метод должен быть публичен,
	 * потому что он почему-то публичен в родительском классе.
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(Df_Customer_Model_Resource_Customer::mf());
	}

	/** @return Zend_Date|null */
	public function getDateOfBirth() {
		/** @var string|null $dateAsString */
		$dateAsString = $this->_getData('dob');
		return !$dateAsString ? null : df()->date()->fromDb($dateAsString, $throw = false);
	}

	/**
	 * @param string|null $format [optional]
	 * @return string
	 */
	public function getDateOfBirthAsString($format = null) {
		return $this->getDateOfBirth() ? df_dts($this->getDateOfBirth(), $format) : '';
	}

	/**
	 * Обратите внимание, что пол храниться в данном объекте не в виде строки Male/Female,
	 * а в виде целого числа.
	 * @return int
	 */
	public function getGender() {return rm_int($this->getData('gender'));}

	/** @return string|null */
	public function getGenderAsString() {
		return df_a($this->getResource()->getMapFromGenderIdToGenderName(), $this->getGender());
	}

	/** @return string */
	public function getInn() {return df_nts($this->_getData('taxvat'));}

	/** @return string|null */
	public function getNameFirst() {return $this->_getData(self::P__NAME_FIRST);}

	/** @return string|null */
	public function getNameLast() {return $this->_getData(self::P__NAME_LAST);}

	/** @return string|null */
	public function getNameMiddle() {return $this->_getData(self::P__NAME_MIDDLE);}

	/**
	 * @param string|Zend_Date|null $value
	 * @return Df_Customer_Model_Customer
	 */
	public function setDob($value) {
		/**
		 * Обратите внимание, что $value может быть равно NULL.
		 * @link http://magento-forum.ru/topic/4198/
		 */
		if ($value instanceof Zend_Date) {
			$value = df_dts($value, 'y-MM-dd HH-mm-ss');
		}
		$this->setData(self::P__DOB, $value);
		return $this;
	}

	/**
	 * В качестве параметра можно передавать как код пола, так и строки «Male» / «Female»
	 * @param int|string|null $value
	 * @return Df_Customer_Model_Customer
	 */
	public function setGender($value) {
		/**
		 * $value может быть равно null
		 * @link http://magento-forum.ru/topic/4220/
		 */
		if (is_numeric($value) || is_null($value)) {
			$value = rm_int($value);
		}
		else {
			df_param_string($value, 0);
			$value = df_a($this->getResource()->getMapFromGenderNameToGenderId(), $value);
			df_assert_integer($value);
		}
		/**
		 * Оказывается, система может передавать в метод setGender значение '0'.
		 * @link http://magento-forum.ru/topic/4243/
		 */
		if (0 == $value) {
			$this->unsetData(self::P__GENDER);
		}
		else {
			$this->setData(self::P__GENDER, $value);
		}
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Customer
	 */
	public function setNameFirst($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__NAME_FIRST, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Customer
	 */
	public function setNameLast($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__NAME_LAST, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Customer
	 */
	public function setNameMiddle($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__NAME_MIDDLE, $value);
		return $this;
	}

	/**
	 * @override
	 * @return bool|array
	 */
	public function validate() {
		if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
			$this->setEmail($this->getFakedEmail());
		}
		if (!Zend_Validate::is(trim($this->getNameFirst()) , 'NotEmpty')) {
			$this->setNameFirst('Аноним');
		}
		if (!Zend_Validate::is(trim($this->getNameLast()) , 'NotEmpty')) {
			$this->setNameLast('Анонимов');
		}
		/** @var bool|array $result */
		$result = parent::validate();
		return $result;
	}

	/** @return string */
	private function getFakedEmail() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $incrementId */
			$incrementId = $this->getDataUsingMethod('increment_id');
			$this->{__METHOD__} = implode('@', array(
				$incrementId ? $incrementId : time()
				,Mage::app()->getStore()->getConfig(self::XML_PATH_DEFAULT_EMAIL_DOMAIN)
			));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const GENDER__FEMALE = 'Female';
	const GENDER__MALE = 'Male';
	const P__CREATED_IN = 'created_in';
	const P__DOB = 'dob';
	const P__EMAIL = 'email';
	const P__GENDER = 'gender';
	const P__GROUP_ID = 'group_id';
	const P__NAME_FIRST = 'firstname';
	const P__NAME_LAST = 'lastname';
	const P__NAME_MIDDLE = 'middlename';
	const P__PASSWORD = 'password';
	const P__WEBSITE_ID = 'website_id';

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Customer_Model_Customer
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Customer_Model_Customer
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Customer_Model_Resource_Customer_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
}