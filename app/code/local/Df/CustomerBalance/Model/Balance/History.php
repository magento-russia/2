<?php
/**
 * @method Df_CustomerBalance_Model_Resource_Balance_History getResource()
 */
class Df_CustomerBalance_Model_Balance_History extends Df_Core_Model {
	/** @return string[] */
	public function getActionNamesArray() {
		return
			array(
				self::ACTION_CREATED => df_h()->customer()->balance()->__('Created')
				,self::ACTION_UPDATED => df_h()->customer()->balance()->__('Updated')
				,self::ACTION_USED => df_h()->customer()->balance()->__('Used')
				,self::ACTION_REFUNDED => df_h()->customer()->balance()->__('Refunded')
			)
		;
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Balance_History
	 */
	protected function _afterSave() {
		parent::_afterSave();
		// attempt to send email
		$this->setIsCustomerNotified(false);
		if ($this->getBalanceModel()->getNotifyByEmail()) {
			$storeId = $this->getBalanceModel()->getStoreId();
			/** @var Df_Core_Model_Email_Template $email */
			$email = Df_Core_Model_Email_Template::i();
			$email
				->setDesignConfig(
					array('store' => $storeId)
				)
			;
			$customer = $this->getBalanceModel()->getCustomer();
			$email
				->sendTransactional(
					Mage::getStoreConfig('df_customer/balance/email_template', $storeId)
					,Mage::getStoreConfig('df_customer/balance/email_identity', $storeId)
					,$customer->getEmail()
					,$customer->getName()
					,array(
						'balance' =>
							Mage::app()->getWebsite($this->getBalanceModel()->getWebsiteId())
								->getBaseCurrency()->format(
									$this->getBalanceModel()->getAmount()
									,array()
									,false
								)
						,'name'	=> $customer->getName()
					)
				)
			;
			if ($email->getSentSuccess()) {
				$this->getResource()->markAsSent($this->getId());
				$this->setIsCustomerNotified(true);
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Balance_History
	 */
	protected function _beforeSave() {
		$balance = $this->getBalanceModel();
		if (!$balance || !$balance->getId()) {
			Mage::throwException(
				df_h()->customer()->balance()->__(
					'Balance history cannot be saved without existing balance.'
				)
			);
		}
		$this->addData(
			array(
				'balance_id' => $balance->getId()
				,'updated_at' => time()
				,'balance_amount' => $balance->getAmount()
				,'balance_delta'  => $balance->getAmountDelta()
			)
		);
		switch (rm_int($balance->getHistoryAction())) {
			case self::ACTION_CREATED:
				// break intentionally omitted
			case self::ACTION_UPDATED:
				if (!$balance->getUpdatedActionAdditionalInfo()) {
					/** @var Mage_Admin_Model_User $user */
					$user = df_mage()->admin()->session()->getUser();
					if ($user) {
						if ($user->getUsername()) {
							if (!trim($balance->getComment())){
								$this
									->setAdditionalInfo(
										df_h()->customer()->balance()->__(
											'By admin: %s.'
											,$user->getUsername()
										)
									)
								;
							}
							else{
								$this
									->setAdditionalInfo(
										df_h()->customer()->balance()->__(
											'By admin: %1$s. (%2$s)'
											,$user->getUsername()
											,$balance->getComment()
										)
									)
								;
							}
						}
					}
				}
				else {
					$this->setAdditionalInfo($balance->getUpdatedActionAdditionalInfo());
				}
				break;
			case self::ACTION_USED:
				$this->_checkBalanceModelOrder($balance);
				$this
					->setAdditionalInfo(
						df_h()->customer()->balance()->__(
							'Order #%s'
							,$balance->getOrder()->getIncrementId()
						)
					)
				;
				break;
			case self::ACTION_REFUNDED:
				$this->_checkBalanceModelOrder($balance);
				if (
						!$balance->getCreditMemo()
					||
						!$balance->getCreditMemo()->getIncrementId()
				) {
					Mage::throwException(
						df_h()->customer()->balance()
							->__('There is no creditmemo set to balance model.')
					);
				}
				$this
					->setAdditionalInfo(
						df_h()->customer()->balance()->__(
							'Order #%s, creditmemo #%s'
							,$balance->getOrder()->getIncrementId()
							,$balance->getCreditMemo()->getIncrementId()
						)
					)
				;
				break;
			default:
				Mage::throwException(
					df_h()->customer()->balance()->__(
						'Unknown balance history action code'
					)
				);
		}
		$this->setAction(rm_int($balance->getHistoryAction()));
		return parent::_beforeSave();
	}

	/**
	 * @param Df_CustomerBalance_Model_Balance $model
	 * @return void
	 */
	private function _checkBalanceModelOrder(Df_CustomerBalance_Model_Balance $model) {
		if (!$model->getOrder() || !$model->getOrder()->getIncrementId()) {
			Mage::throwException(
				df_h()->customer()->balance()->__('There is no order set to balance model.')
			);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_CustomerBalance_Model_Resource_Balance_History::mf());
	}

	const _CLASS = __CLASS__;
	const ACTION_UPDATED  = 1;
	const ACTION_CREATED  = 2;
	const ACTION_USED	 = 3;
	const ACTION_REFUNDED = 4;
	const P__ID = 'history_id';

	/** @return Df_CustomerBalance_Model_Resource_Balance_History_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_CustomerBalance_Model_Balance_History
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_CustomerBalance_Model_Balance_History
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_CustomerBalance_Model_Resource_Balance_History_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_CustomerBalance_Model_Balance_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}