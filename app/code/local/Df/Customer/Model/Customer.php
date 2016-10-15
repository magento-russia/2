<?php
/**
 * @method array getCustomerBalanceData()
 * @method string|null getEmail()
 * @method string|null getFirstname()
 * @method string|null getIncrementId()
 * @method string|null getLastname()
 * @method string|null getMiddlename()
 * @method Df_Customer_Model_Resource_Customer getResource()
 * @method bool|null getRewardUpdateNotification()
 * @method bool|null getRewardWarningNotification()
 * @method int|null getWebsiteId()
 * @method Df_Customer_Model_Customer setCustomerBalanceData(array $value)
 * @method Df_Customer_Model_Customer setEmail(string $value)
 * @method Df_Customer_Model_Customer setFirstname(string $value)
 * @method Df_Customer_Model_Customer setGroupId(int $value)
 * @method Df_Customer_Model_Customer setLastname(string $value)
 * @method Df_Customer_Model_Customer setMiddlename(string $value)
 * @method Df_Customer_Model_Customer setPassword(string $value)
 * @method Df_Customer_Model_Customer setRewardUpdateNotification(bool $value)
 * @method Df_Customer_Model_Customer setRewardWarningNotification(bool $value)
 * @method Df_Customer_Model_Customer setWebsiteId(int $value)
 */
class Df_Customer_Model_Customer extends Mage_Customer_Model_Customer {
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
	public function getGender() {return df_int($this->getData('gender'));}

	/** @return string|null */
	public function getGenderAsString() {
		return dfa($this->getResource()->getMapFromGenderIdToGenderName(), $this->getGender());
	}

	/** @return string */
	public function getInn() {return df_nts($this->_getData('taxvat'));}

	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Customer_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @param string|Zend_Date|null $value
	 * @return Df_Customer_Model_Customer
	 */
	public function setDob($value) {
		/**
		 * Обратите внимание, что $value может быть равно NULL.
		 * http://magento-forum.ru/topic/4198/
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
		 * http://magento-forum.ru/topic/4220/
		 */
		if (is_numeric($value) || is_null($value)) {
			$value = df_int($value);
		}
		else {
			df_param_string($value, 0);
			$value = dfa($this->getResource()->getMapFromGenderNameToGenderId(), $value);
			df_assert_integer($value);
		}
		/**
		 * Оказывается, система может передавать в метод setGender значение '0'.
		 * http://magento-forum.ru/topic/4243/
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
	 * @override
	 * @return bool|array
	 */
	public function validate() {
		if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
			$this->setEmail($this->getFakedEmail());
		}
		if (!Zend_Validate::is(trim($this->getFirstname()) , 'NotEmpty')) {
			$this->setFirstname('Аноним');
		}
		if (!Zend_Validate::is(trim($this->getLastname()) , 'NotEmpty')) {
			$this->setLastname('Анонимов');
		}
		/** @var bool|array $result */
		$result = parent::validate();
		return $result;
	}

	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Customer
	 */
	protected function _getResource() {return Df_Customer_Model_Resource_Customer::s();}

	/** @return string */
	private function getFakedEmail() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $incrementId */
			$incrementId = $this->getIncrementId();
			$this->{__METHOD__} = implode('@', array(
				$incrementId ? $incrementId : time()
				,df_store()->getConfig(self::XML_PATH_DEFAULT_EMAIL_DOMAIN)
			));
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_Customer_Model_Resource_Customer_Collection::_construct() */
	const _C = __CLASS__;
	const GENDER__FEMALE = 'Female';
	const GENDER__MALE = 'Male';
	const P__CREATED_IN = 'created_in';
	const P__DOB = 'dob';
	const P__EMAIL = 'email';
	const P__FIRSTNAME = 'firstname';
	const P__GENDER = 'gender';
	const P__GROUP_ID = 'group_id';
	const P__LASTNAME = 'lastname';
	const P__MIDDLENAME = 'middlename';
	const P__PASSWORD = 'password';
	const P__WEBSITE_ID = 'website_id';

	/** @return Df_Customer_Model_Resource_Customer_Collection */
	public static function c() {return new Df_Customer_Model_Resource_Customer_Collection;}
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
}