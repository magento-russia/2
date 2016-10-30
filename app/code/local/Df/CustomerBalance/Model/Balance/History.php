<?php
/**
 * @method Df_CustomerBalance_Model_Resource_Balance_History getResource()
 * @method $this setBalanceModel(Df_CustomerBalance_Model_Balance $value)
 * @method $this setUpdatedAt(string $value)
 */
class Df_CustomerBalance_Model_Balance_History extends Df_Core_Model {
	/** @return array(int => string) */
	public function getActionNamesArray() {
		/** @var Df_CustomerBalance_Helper_Data $helper */
		$helper = Df_CustomerBalance_Helper_Data::s();
		return array(
			self::ACTION_CREATED => $helper->__('Created')
			,self::ACTION_UPDATED => $helper->__('Updated')
			,self::ACTION_USED => $helper->__('Used')
			,self::ACTION_REFUNDED => $helper->__('Refunded')
		);
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Resource_Balance_History_Collection
	 */
	public function getResourceCollection() {return self::c();}

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
			$email->sendTransactional(
				df_cfg('df_customer/balance/email_template', $storeId)
				,df_cfg('df_customer/balance/email_identity', $storeId)
				,$customer->getEmail()
				,$customer->getName()
				,array(
					'balance' =>
						df_website($this->getBalanceModel()->getWebsiteId())->getBaseCurrency()->format(
							$this->getBalanceModel()->getAmount(), array(), false
						)
					,'name'	=> $customer->getName()
				)
			);
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
				Df_CustomerBalance_Helper_Data::s()->__(
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
		switch (df_int($balance->getHistoryAction())) {
			case self::ACTION_CREATED:
				// break intentionally omitted
			case self::ACTION_UPDATED:
				if (!$balance->getUpdatedActionAdditionalInfo()) {
					/** @var string|null $name */
					$name = df_admin_name(false);
					if ($name) {
						/** @var Df_CustomerBalance_Helper_Data $t */
						$t = Df_CustomerBalance_Helper_Data::s();
						$this->setAdditionalInfo($t->__(
							!trim($balance->getComment())
							? $t->__('By admin: %s.', $name)
							: $t->__('By admin: %1$s. (%2$s)', $name, $balance->getComment())
						));
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
						Df_CustomerBalance_Helper_Data::s()->__(
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
						Df_CustomerBalance_Helper_Data::s()
							->__('There is no creditmemo set to balance model.')
					);
				}
				$this
					->setAdditionalInfo(
						Df_CustomerBalance_Helper_Data::s()->__(
							'Order #%s, creditmemo #%s'
							,$balance->getOrder()->getIncrementId()
							,$balance->getCreditMemo()->getIncrementId()
						)
					)
				;
				break;
			default:
				Mage::throwException(
					Df_CustomerBalance_Helper_Data::s()->__(
						'Unknown balance history action code'
					)
				);
		}
		$this->setAction(df_int($balance->getHistoryAction()));
		return parent::_beforeSave();
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Resource_Balance_History
	 */
	protected function _getResource() {return Df_CustomerBalance_Model_Resource_Balance_History::s();}

	/**
	 * @param Df_CustomerBalance_Model_Balance $model
	 * @return void
	 */
	private function _checkBalanceModelOrder(Df_CustomerBalance_Model_Balance $model) {
		if (!$model->getOrder() || !$model->getOrder()->getIncrementId()) {
			Mage::throwException(
				Df_CustomerBalance_Helper_Data::s()->__('There is no order set to balance model.')
			);
		}
	}

	/** @used-by Df_CustomerBalance_Model_Resource_Balance_History_Collection::_construct() */

	const ACTION_UPDATED = 1;
	const ACTION_CREATED = 2;
	const ACTION_USED = 3;
	const ACTION_REFUNDED = 4;
	const P__ID = 'history_id';

	/** @return Df_CustomerBalance_Model_Resource_Balance_History_Collection */
	public static function c() {
		return new Df_CustomerBalance_Model_Resource_Balance_History_Collection;
	}
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
	/** @return Df_CustomerBalance_Model_Balance_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}