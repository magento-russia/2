<?php
/**
 * @method int|null getCustomerId()
 * @method string|null getEmail()
 * @method int|null getGroupId()
 * @method int|null getReferralId()
 */
class Df_Invitation_Model_Invitation extends Df_Core_Model {
	/**
	 * @param int|string $websiteId
	 * @param int $referralId
	 * @return Df_Invitation_Model_Invitation
	 */
	public function accept($websiteId, $referralId) {
		$this->makeSureCanBeAccepted($websiteId);
		$this
			->setReferralId($referralId)
			->setStatus(self::STATUS_ACCEPTED)
			->setSignupDate($this->getResource()->formatDate(time()))
			->save()
		;
		$inviterId = $this->getCustomerId();
		if ($inviterId) {
			$this->getResource()->trackReferral($inviterId, $referralId);
		}
		return $this;
	}

	/**
	 * @param int $websiteId
	 * @return bool
	 */
	public function canBeAccepted($websiteId = null) {
		try {
			$this->makeSureCanBeAccepted($websiteId);
			return true;
		}
		catch (Mage_Core_Exception $e) {
			// intentionally jammed
		}
		return false;
	}

	/** @return bool */
	public function canBeCanceled() {
		return
				!!$this->getId()
			&&
				!in_array($this->getStatus(), array(self::STATUS_CANCELED, self::STATUS_ACCEPTED))
		;
	}

	/**
	 * @throws Mage_Core_Exception|Exception
	 * @return bool
	 */
	public function canBeSent() {
		try {
			$this->makeSureCanBeSent();
			return true;
		}
		catch (Mage_Core_Exception $e) {
			if ($e->getCode() && $e->getCode() === self::ERROR_INVALID_DATA) {
				df_error($e);
			}
		}
		return false;
	}

	/** @return Df_Invitation_Model_Invitation */
	public function cancel() {
		if ($this->canBeCanceled()) {
			$this->setStatus(self::STATUS_CANCELED)->save();
		}
		return $this;
	}

	/** @return bool */
	public function canMessageBeUpdated() {
		return $this->getId() && (self::STATUS_NEW === $this->getStatus());
	}

	/** @return string */
	public function getInvitationCode() {
		if (!$this->getId()) {
			Mage::throwException(df_h()->invitation()->__('Impossible to generate encrypted code.'));
		}
		return $this->getId() . ':' . $this->getProtectionCode();
	}

	/** @return Df_Customer_Model_Customer|null */
	public function getInviter() {
		/** @var Df_Customer_Model_Customer|null $result */
		$result = $this->getCustomer();
		return $result && $result->getId() ? $result : null;
	}

	/**
	 * @override
	 * @return Df_Invitation_Model_Resource_Invitation_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/** @return int */
	public function getStoreId() {
		return $this->hasData('store_id') ? $this->_getData('store_id') : df_store_id();
	}

	/**
	 * @param string $code
	 * @return Df_Invitation_Model_Invitation
	 * @throws Mage_Core_Exception
	 */
	public function loadByInvitationCode($code) {
		$code = explode(':', $code, 2);
		if (count($code) != 2) {
			Mage::throwException(df_h()->invitation()->__('Invalid invitation code.'));
		}
		list($id, $protectionCode) = $code;
		$this->load($id);
		if (!$this->getId() || $this->getProtectionCode() != $protectionCode) {
			Mage::throwException(df_h()->invitation()->__('Invalid invitation code.'));
		}
		return $this;
	}

	/**
	 * @param int|string $websiteId
	 * @throws Mage_Core_Exception
	 */
	public function makeSureCanBeAccepted($websiteId = null) {
		$messageInvalid = df_h()->invitation()->__('This invitation is not valid.');
		if (!$this->getId()) {
			throw new Mage_Core_Exception($messageInvalid, self::ERROR_STATUS);
		}
		if (
			!in_array(
				$this->getStatus()
				,array(
					self::STATUS_NEW
					,self::STATUS_SENT
				)
			)
		) {
			throw new Mage_Core_Exception($messageInvalid, self::ERROR_STATUS);
		}
		$websiteId = is_null($websiteId) ? df_website_id() : $websiteId;
		if ($websiteId != df_store($this->getStoreId())->getWebsiteId()) {
			throw new Mage_Core_Exception($messageInvalid, self::ERROR_STATUS);
		}
	}

	/**
	 * @throws Mage_Core_Exception
	 */
	public function makeSureCanBeSent() {
		if (!$this->getId()) {
			throw
				new Mage_Core_Exception(
					df_h()->invitation()->__('Invitation has no ID.')
					,self::ERROR_INVALID_DATA
				)
			;
		}
		if ($this->getStatus() !== self::STATUS_NEW) {
			throw new Mage_Core_Exception(
				df_h()->invitation()->__(
					'Invitation with status "%s" cannot be sent.'
					,$this->getStatus()
				)
				,self::ERROR_STATUS
			);
		}
		if (!$this->getEmail() || !Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
			throw new Mage_Core_Exception(
				df_h()->invitation()->__(
					'Invalid or empty invitation email.'
				)
				,self::ERROR_INVALID_DATA
			);
		}
		$this->makeSureCustomerNotExists();
	}

	/**
	 * @param string $email
	 * @param string $websiteId
	 * @throws Mage_Core_Exception
	 * @return void
	 */
	public function makeSureCustomerNotExists($email = null, $websiteId = null) {
		$websiteId = !is_null($websiteId) ? $websiteId : df_store($this->getStoreId())->getWebsiteId();
		if (!$websiteId) {
			throw new Mage_Core_Exception(
				df_h()->invitation()->__('Unable to determine proper website.')
				, self::ERROR_INVALID_DATA
			);
		}
		if (null === $email) {
			$email = $this->getEmail();
		}
		if (!$email) {
			throw new Mage_Core_Exception(
				df_h()->invitation()->__('Email is not specified.')
				,self::ERROR_INVALID_DATA
			);
		}
		// lookup customer by specified email/website id
		if (
				!isset(self::$_customerExistsLookup[$email])
			||
				!isset(self::$_customerExistsLookup[$email][$websiteId])
		) {
			/** @var Df_Customer_Model_Customer $customer */
			$customer = Df_Customer_Model_Customer::i();
			$customer->setWebsiteId($websiteId);
			$customer->loadByEmail($email);
			self::$_customerExistsLookup[$email][$websiteId] =
				$customer->getId()
				? $customer->getId()
				: false
			;
		}
		if (false === self::$_customerExistsLookup[$email][$websiteId]) {
			return;
		}
		throw new Mage_Core_Exception(
			df_h()->invitation()->__(
				'Customer with email "%s" already exists.'
				,$email
			)
			,self::ERROR_CUSTOMER_EXISTS
		);
	}

	/** @return bool */
	public function sendInvitationEmail() {
		/** @var bool $result */
		$result = false;
		$this->makeSureCanBeSent();
		$store = df_store($this->getStoreId());
		/** @var Df_Core_Model_Email_Template $mail */
		$mail = Df_Core_Model_Email_Template::i();
		$mail->setDesignConfig(array(
			'area'=>Df_Core_Const_Design_Area::FRONTEND, 'store' => $this->getStoreId()
		));
		$mail
			->sendTransactional(
				$store->getConfig(self::XML_PATH_EMAIL_TEMPLATE)
				,$store->getConfig(self::XML_PATH_EMAIL_IDENTITY)
				,$this->getEmail()
				,null
				,array(
					'url' => df_h()->invitation()->getInvitationUrl($this)
					,'allow_message' =>
							df_is_admin()
						||
							df_h()->invitation()->config()->isInvitationMessageAllowed()
					,'message' => $this->getMessage()
					,'store' => $store
					,'store_name' => $store->getGroup()->getName()
					// @deprecated after 1.4.0.0-beta1
					,'inviter_name' =>
							$this->getInviter()
							? $this->getInviter()->getName()
							: null
				)
			)
		;
		if ($mail->getSentSuccess()) {
			$this->setStatus(self::STATUS_SENT)->setUpdateDate(true)->save();
			$result = true;
		}
		return $result;
	}

	/** @return string[] */
	public function validate() {
		$errors = [];
		if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
			$errors[]= df_h()->invitation()->__("Invalid invitation email.");
		}
		if (!empty($errors)) {
			return $errors;
		}
		return true;
	}

	/**
	 * @override
	 * @return Df_Invitation_Model_Invitation
	 */
	protected function _afterSave() {
		Df_Invitation_Model_Invitation_History::i()
			->setInvitationId($this->getId())->setStatus($this->getStatus())
			->save()
		;
		$parent = parent::_afterSave();
		if ($this->getStatus() === self::STATUS_NEW) {
			$this->setOrigData();
		}
		return $parent;
	}

	/**
	 * @override
	 * @throws Mage_Core_Exception
	 * @return Df_Invitation_Model_Invitation
	 */
	protected function _beforeSave() {
		if (!$this->getId()) {
			// set initial data for new one
			$this
				->addData(
					array(
						'protection_code' => df_mage()->coreHelper()->uniqHash()
						,'status' => self::STATUS_NEW
						,'date' => $this->getResource()->formatDate(time())
						,'store_id' => $this->getStoreId()
					)
				)
			;
			$inviter = $this->getInviter();
			if ($inviter) {
				$this->setCustomerId($inviter->getId());
			}
			if (df_h()->invitation()->config()->getUseInviterGroup()) {
				if ($inviter) {
					$this->setGroupId($inviter->getGroupId());
				}
				if (!$this->hasGroupId()) {
					throw new Mage_Core_Exception(
						df_h()->invitation()->__('No customer group id specified.')
						,self::ERROR_INVALID_DATA
					);
				}
			}
			else {
				$this->unsetData('group_id');
			}
			if (0 === df_nat0($this->getStoreId())) {
				throw new Mage_Core_Exception(
					df_h()->invitation()->__('Wrong store specified.')
					,self::ERROR_INVALID_DATA
				);
			}
			$this->makeSureCustomerNotExists();
		}
		else {
			if ($this->dataHasChangedFor('message') && !$this->canMessageBeUpdated()) {
				throw new Mage_Core_Exception(df_h()->invitation()->__(
					'Message cannot be updated.', self::ERROR_STATUS
				));
			}
		}
		parent::_beforeSave();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Invitation_Model_Resource_Invitation
	 */
	protected function _getResource() {return Df_Invitation_Model_Resource_Invitation::s();}

	/** @var array[] */
	private static $_customerExistsLookup = [];
	/** @var string */
	protected $_eventObject = 'invitation';
	/** @var string */
	protected $_eventPrefix = 'df_invitation';

	/** @used-by Df_Invitation_Model_Resource_Invitation_Collection::_construct() */

	const ERROR_CUSTOMER_EXISTS = 3;
	const ERROR_INVALID_DATA = 2;
	const ERROR_STATUS = 1;
	const P__ID = 'invitation_id';
	const STATUS_ACCEPTED = 'accepted';
	const STATUS_CANCELED = 'canceled';
	const STATUS_NEW = 'new';
	const STATUS_SENT = 'sent';
	const XML_PATH_EMAIL_IDENTITY = 'df_invitation/email/identity';
	const XML_PATH_EMAIL_TEMPLATE = 'df_invitation/email/template';

	/** @return Df_Invitation_Model_Resource_Invitation_Collection */
	public static function c() {return new Df_Invitation_Model_Resource_Invitation_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Invitation_Model_Invitation
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Invitation_Model_Invitation
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}