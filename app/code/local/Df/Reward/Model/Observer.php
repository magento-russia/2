<?php
/**
 * Reward observer
 */
class Df_Reward_Model_Observer
{
	/**
	 * Update reward points for customer, send notification
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function saveRewardPoints($observer)
	{
		if (!df_h()->reward()->isEnabled()) {
			return;
		}
		$request = $observer->getEvent()->getRequest();
		$customer = $observer->getEvent()->getCustomer();
		$data = $request->getPost('reward');
		if ($data) {
			if (!isset($data['store_id'])) {
				if ($customer->getStoreId() == 0) {
					$data['store_id'] = Mage::app()->getDefaultStoreView()->getWebsiteId();
				} else {
					$data['store_id'] = $customer->getStoreId();
				}
			}
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomer($customer)
					->setWebsiteId(Mage::app()->getStore($data['store_id'])->getWebsiteId())
					->loadByCustomer()
			;
			if (!empty($data['points_delta'])) {
				$reward->addData($data)
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_ADMIN)
					->setActionEntity($customer)
					->updateRewardPoints();
			} else {
				$reward->save();
			}
		}
		return $this;
	}

	/**
	 * Update reward notifications for customer
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function saveRewardNotifications($observer)
	{
		if (!df_h()->reward()->isEnabled()) {
			return;
		}

		$request = $observer->getEvent()->getRequest();
		$customer = $observer->getEvent()->getCustomer();
		$data = $request->getPost('reward');
		$subscribeByDefault = df_h()->reward()->getNotificationConfig('subscribe_by_default');
		if ($customer->isObjectNew()) {
			$data['reward_update_notification']  = (int)$subscribeByDefault;
			$data['reward_warning_notification'] = (int)$subscribeByDefault;
		}

		$customer->setRewardUpdateNotification((isset($data['reward_update_notification']) ? 1 : 0));
		$customer->setRewardWarningNotification((isset($data['reward_warning_notification']) ? 1 : 0));
		return $this;
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function customer_save_before(
		Varien_Event_Observer $observer
	) {
		try {
			/* @var $customer Mage_Customer_Model_Customer */
			$customer = $observer->getEvent()->getData('customer');
			df_assert($customer instanceof Mage_Customer_Model_Customer);
			if ($customer->isObjectNew()) {
				/**
				 * К сожалению, при событии customer_save_after от isObjectNew() толку мало,
				 * потому что метод Mage_Eav_Model_Entity_Abstract::_processSaveData()
				 * почему-то удаляет флаг isObjectNew() до наступления события customer_save_after
				 */
				$customer->setData('rm_object_new', true);
			}
		}

		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}

	}

	/**
	 * Update reward points after customer register
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function customerRegister($observer)
	{
		if (!df_h()->reward()->isEnabledOnFront()) {
			return $this;
		}
		/* @var $customer Mage_Customer_Model_Customer */
		$customer = $observer->getEvent()->getCustomer();
		df_assert($customer instanceof Mage_Customer_Model_Customer);
		if ($customer->getData('rm_object_new')) {
			try {
				$subscribeByDefault = df_h()->reward()
					->getNotificationConfig('subscribe_by_default', Mage::app()->getStore()->getWebsiteId());
				/** @var Df_Reward_Model_Reward $reward */
				$reward = Df_Reward_Model_Reward::i();
				$reward->setCustomer($customer);
				$reward
					->addData(
						array(
							Df_Reward_Model_Reward::P__ACTION_ENTITY => $customer
							,Df_Reward_Model_Reward::P__STORE => Mage::app()->getStore()->getId()
							,Df_Reward_Model_Reward::P__ACTION => Df_Reward_Model_Reward::REWARD_ACTION_REGISTER
						)
					)
				;
				$reward->updateRewardPoints();
				$customer->setRewardUpdateNotification((int)$subscribeByDefault)
					->setRewardWarningNotification((int)$subscribeByDefault);
				$customer->getResource()->saveAttribute($customer, 'reward_update_notification');
				$customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
			} catch (Exception $e) {
				//save exception if something were wrong during saving reward and allow to register customer
				df_handle_entry_point_exception($e, false);
			}
		}
		return $this;
	}

	/**
	 * Update points balance after review submit
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function reviewSubmit($observer)
	{
		/* @var $review Mage_Review_Model_Review */
		$review = $observer->getEvent()->getObject();
		$websiteId = Mage::app()->getStore($review->getStoreId())->getWebsiteId();
		if (!df_h()->reward()->isEnabledOnFront($websiteId)) {
			return $this;
		}
		if ($review->isApproved() && $review->getCustomerId()) {
			/* @var $reward Df_Reward_Model_Reward */
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomerId($review->getCustomerId())
					->setStore($review->getStoreId())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_REVIEW)
					->setActionEntity($review)
					->updateRewardPoints()
			;
		}
		return $this;
	}

	/**
	 * Update points balance after tag submit
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function tagSubmit($observer)
	{
		/* @var $tag Mage_Tag_Model_Tag */
		$tag = $observer->getEvent()->getObject();
		$websiteId = Mage::app()->getStore($tag->getFirstStoreId())->getWebsiteId();
		if (!df_h()->reward()->isEnabledOnFront($websiteId)) {
			return $this;
		}
		if (($tag->getApprovedStatus() == $tag->getStatus()) && $tag->getFirstCustomerId()) {
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomerId($tag->getFirstCustomerId())
					->setStore($tag->getFirstStoreId())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_TAG)
					->setActionEntity($tag)
					->updateRewardPoints()
			;
		}
		return $this;
	}

	/**
	 * Update points balance after first successful subscribtion
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function customerSubscribed($observer)
	{
		/* @var $subscriber Mage_Newsletter_Model_Subscriber */
		$subscriber = $observer->getEvent()->getSubscriber();
		// reward only new subscribtions
		if (!$subscriber->isObjectNew() || !$subscriber->getCustomerId()) {
			return $this;
		}
		$websiteId = Mage::app()->getStore($subscriber->getStoreId())->getWebsiteId();
		if (!df_h()->reward()->isEnabledOnFront($websiteId)) {
			return $this;
		}
		/** @var Df_Reward_Model_Reward $reward */
		$reward = Df_Reward_Model_Reward::i();
		$reward
			->setCustomerId($subscriber->getCustomerId())
			->setStore($subscriber->getStoreId())
			->setAction(Df_Reward_Model_Reward::REWARD_ACTION_NEWSLETTER)
			->setActionEntity($subscriber)
		;
		$reward->updateRewardPoints();
		return $this;
	}

	/**
	 * Update points balance after customer registered by invitation
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function invitationToCustomer($observer)
	{
		/* @var $invitation Df_Invitation_Model_Invitation */
		$invitation = $observer->getEvent()->getInvitation();
		$websiteId = Mage::app()->getStore($invitation->getStoreId())->getWebsiteId();
		if (!df_h()->reward()->isEnabledOnFront($websiteId)) {
			return $this;
		}

		if ($invitation->getCustomerId() && $invitation->getReferralId()) {
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomerId($invitation->getCustomerId())
					->setWebsiteId($websiteId)
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_INVITATION_CUSTOMER)
					->setActionEntity($invitation)
					->updateRewardPoints()
			;
		}
		return $this;
	}

	/**
	 * Update points balance after order becomes completed
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function orderCompleted($observer)
	{
		/* @var $order Mage_Sales_Model_Order */
		$order = $observer->getEvent()->getOrder();
		if ($order->getCustomerIsGuest()
			|| !df_h()->reward()->isEnabledOnFront($order->getStore()->getWebsiteId()))
		{
			return $this;
		}
		if ($order->getCustomerId() && ((float)$order->getBaseTotalPaid() > 0)
			&& (($order->getBaseGrandTotal() - $order->getBaseSubtotalCanceled()) - $order->getBaseTotalPaid()) < 0.0001) {
			/* @var Df_Reward_Model_Reward $reward */
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId($order->getStore()->getWebsiteId())
					->setActionEntity($order)
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_ORDER_EXTRA)
					->updateRewardPoints()
			;
			if ($reward->getPointsDelta()) {
				$order->addStatusHistoryComment(
					df_h()->reward()->__('Customer earned %s for the order.', df_h()->reward()->formatReward($reward->getPointsDelta()))
				)->save();
			}
			// Also update inviter balance if possible
			$this->_invitationToOrder($observer);
		}
		return $this;
	}

	/**
	 * Update inviter points balance after referral's order completed
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	protected function _invitationToOrder($observer)
	{
		if (df_module_enabled(Df_Core_Module::INVITATION)) {
			/* @var $order Mage_Sales_Model_Order */
			$order = $observer->getEvent()->getOrder();
			$invitation =
				Df_Invitation_Model_Invitation::i()->load($order->getCustomerId(), 'referral_id')
			;
			if (!$invitation->getId() || !$invitation->getCustomerId()) {
				return $this;
			}
			$reward =
				Df_Reward_Model_Reward::i()
					->setActionEntity($invitation)
					->setCustomerId($invitation->getCustomerId())
					->setStore($order->getStoreId())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER)
					->updateRewardPoints()
			;
		}
		return $this;
	}

	/**
	 * Set flag to reset reward points totals
	 *
	 * @param Varien_Event_Observer $observer
	 * @@return Df_Reward_Model_Observer
	 */
	public function quoteCollectTotalsBefore(Varien_Event_Observer $observer)
	{
		$quote = $observer->getEvent()->getQuote();
		$quote->setRewardPointsTotalReseted(false);
		return $this;
	}

	/**
	 * Set use reward points flag to new quote
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function quoteMergeAfter($observer)
	{
		$quote = $observer->getEvent()->getQuote();
		$source = $observer->getEvent()->getSource();
		if ($source->getUseRewardPoints()) {
			$quote->setUseRewardPoints($source->getUseRewardPoints());
		}
		return $this;
	}

	/**
	 * Payment data import in checkout process
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function paymentDataImport(Varien_Event_Observer $observer)
	{
		if (!df_h()->reward()->isEnabledOnFront()) {
			return $this;
		}
		$input = $observer->getEvent()->getInput();
		/* @var $quote Mage_Sales_Model_Quote */
		$quote = $observer->getEvent()->getPayment()->getQuote();
		$this->_paymentDataImport($quote, $input, $input->getUseRewardPoints());
		return $this;
	}

	/**
	 * Enable Zero Subtotal Checkout payment method
	 * if customer has enough points to cover grand total
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function preparePaymentMethod($observer)
	{
		if (!df_h()->reward()->isEnabledOnFront()) {
			return $this;
		}

		/** @var Mage_Sales_Model_Quote|null $quote */
		$quote = $observer->getEvent()->getQuote();
		if (is_null($quote) || !$quote->getId()) {
			return $this;
		}
		/* @var $reward Df_Reward_Model_Reward */
		$reward = $quote->getRewardInstance();
		if (!$reward || !$reward->getId()) {
			return $this;
		}
		$baseQuoteGrandTotal = $quote->getBaseGrandTotal()+$quote->getBaseRewardCurrencyAmount();
		if ($reward->isEnoughPointsToCoverAmount($baseQuoteGrandTotal)) {
			$paymentCode = $observer->getEvent()->getMethodInstance()->getCode();
			$result = $observer->getEvent()->getResult();
			if ('free' === $paymentCode) {
				$result->isAvailable = true;
			} else {
				$result->isAvailable = false;
			}
		}
		return $this;
	}

	/**
	 * Payment data import in admin order create process
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function processOrderCreationData(Varien_Event_Observer $observer)
	{
		/* @var $quote Mage_Sales_Model_Quote */
		$quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
		if (!df_h()->reward()->isEnabledOnFront($quote->getStore()->getWebsiteId())) {
			return $this;
		}
		$request = $observer->getEvent()->getRequest();
		if (isset($request['payment']) && isset($request['payment']['use_reward_points'])) {
			$this->_paymentDataImport($quote, $quote->getPayment(), $request['payment']['use_reward_points']);
		}
		return $this;
	}

	/**
	 * Prepare and set to quote reward balance instance, * set zero subtotal checkout payment if need
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 * @param Varien_Object $payment
	 * @param boolean $useRewardPoints
	 * @return Df_Reward_Model_Observer
	 */
	protected function _paymentDataImport($quote, $payment, $useRewardPoints)
	{
		if (!$quote || !$quote->getCustomerId()) {
			return $this;
		}
		$quote->setUseRewardPoints(!!$useRewardPoints);
		if ($quote->getUseRewardPoints()) {
			/* @var $reward Df_Reward_Model_Reward */
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomer($quote->getCustomer())
					->setWebsiteId($quote->getStore()->getWebsiteId())
					->loadByCustomer()
			;
			if ($reward->getId()) {
				$quote->setRewardInstance($reward);
				if (!$payment->getMethod()) {
					$payment->setMethod('free');
				}
			}
			else {
				$quote->setUseRewardPoints(false);
			}
		}
		return $this;
	}

	/**
	 * Validate order, check if enough reward points to place order
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function processBeforeOrderPlace(Varien_Event_Observer $observer)
	{
		if (!df_h()->reward()->isEnabledOnFront()) {
			return $this;
		}
		$order = $observer->getEvent()->getOrder();
		if ($order->getRewardPointsBalance() > 0) {
			$websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();
			/* @var $reward Df_Reward_Model_Reward */
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId($websiteId)
					->loadByCustomer()
			;
			if (($order->getRewardPointsBalance() - $reward->getPointsBalance()) > 0) {
				Mage::getSingleton('checkout/type_onepage')
					->getCheckout()
					->setUpdateSection('payment-method')
					->setGotoSection('payment');
				Mage::throwException(df_h()->reward()->__('Not enough Reward Points to complete this Order.'));
			}
		}
		return $this;
	}

	/**
	 * Reduce reward points if points was used during checkout
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function processOrderPlace(Varien_Event_Observer $observer)
	{
		if (!df_h()->reward()->isEnabledOnFront()) {
			return $this;
		}
		/* @var $order Mage_Sales_Model_Order */
		$order = $observer->getEvent()->getOrder();
		if ($order->getBaseRewardCurrencyAmount() > 0) {
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId(Mage::app()->getStore($order->getStoreId())->getWebsiteId())
					->setPointsDelta(-$order->getRewardPointsBalance())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_ORDER)
					->setActionEntity($order)
					->updateRewardPoints()
			;
		}
		$ruleIds = explode(',', $order->getAppliedRuleIds());
		$ruleIds = rm_array_unique_fast($ruleIds);
		$data = Mage::getResourceModel('df_reward/reward')
			->getRewardSalesrule($ruleIds);
		$pointsDelta = 0;
		foreach ($data as $rule) {
			$pointsDelta += (int)$rule['points_delta'];
		}
		if ($pointsDelta) {
			$order->setRewardSalesrulePoints($pointsDelta);
		}
		return $this;
	}

	/**
	 * Set forced can creditmemo flag if refunded amount less then invoiced amount of reward points
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function orderLoadAfter(Varien_Event_Observer $observer)
	{
		/* @var $order Mage_Sales_Model_Order */
		$order = $observer->getEvent()->getOrder();
		if ($order->canUnhold()) {
			return $this;
		}
		if ($order->isCanceled() ||
			$order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
			return $this;
		}
		if (($order->getBaseRewardCurrencyAmountInvoiced() - $order->getBaseRewardCurrencyAmountRefunded()) > 0) {
			$order->setForcedCanCreditmemo(true);
		}
		return $this;
	}

	/**
	 * Set invoiced reward amount to order
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function invoiceSaveAfter(Varien_Event_Observer $observer)
	{
		/* @var $invoice Mage_Sales_Model_Order_Invoice */
		$invoice = $observer->getEvent()->getInvoice();
		if ($invoice->getBaseRewardCurrencyAmount()) {
			$order = $invoice->getOrder();
			$order->setRewardCurrencyAmountInvoiced($order->getRewardCurrencyAmountInvoiced() + $invoice->getRewardCurrencyAmount());
			$order->setBaseRewardCurrencyAmountInvoiced($order->getBaseRewardCurrencyAmountInvoiced() + $invoice->getBaseRewardCurrencyAmount());
		}
		return $this;
	}

	/**
	 * Set reward points balance to refund before creditmemo register
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function setRewardPointsBalanceToRefund(Varien_Event_Observer $observer)
	{
		$input = $observer->getEvent()->getRequest()->getParam('creditmemo');
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if (isset($input['refund_reward_points']) && isset($input['refund_reward_points_enable'])) {
			$enable = $input['refund_reward_points_enable'];
			$balance = (int)$input['refund_reward_points'];
			$balance = min($creditmemo->getRewardPointsBalance(), $balance);
			if ($enable && $balance) {
				$creditmemo->setRewardPointsBalanceToRefund($balance);
			}
		}
		return $this;
	}

	/**
	 * Clear forced can creditmemo if whole reward amount was refunded
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function creditmemoRefund(Varien_Event_Observer $observer)
	{
		$creditmemo = $observer->getEvent()->getCreditmemo();
		/* @var $order Mage_Sales_Model_Order */
		$order = $observer->getEvent()->getCreditmemo()->getOrder();
		$refundedAmount = (float)($order->getBaseRewardCurrencyAmountRefunded() + $creditmemo->getBaseRewardCurrencyAmount());
		if ((float)$order->getBaseRewardCurrencyAmountInvoiced() == $refundedAmount) {
			$order->setForcedCanCreditmemo(false);
		}
		return $this;
	}

	/**
	 * Set refunded reward amount order and update reward points balance if need
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function creditmemoSaveAfter(Varien_Event_Observer $observer)
	{
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if ($creditmemo->getBaseRewardCurrencyAmount()) {
			$order = $creditmemo->getOrder();
			$order->setRewardPointsBalanceRefunded($order->getRewardPointsBalanceRefunded() + $creditmemo->getRewardPointsBalance());
			$order->setRewardCurrencyAmountRefunded($order->getRewardCurrencyAmountRefunded() + $creditmemo->getRewardCurrencyAmount());
			$order->setBaseRewardCurrencyAmountRefunded($order->getBaseRewardCurrencyAmountRefunded() + $creditmemo->getBaseRewardCurrencyAmount());
			$order->setRewardPointsBalanceToRefund($order->getRewardPointsBalanceToRefund() + $creditmemo->getRewardPointsBalanceToRefund());
			if ((int)$creditmemo->getRewardPointsBalanceToRefund() > 0) {
				$reward =
					Df_Reward_Model_Reward::i()
						->setCustomerId($order->getCustomerId())
						->setStore($order->getStoreId())
						->setPointsDelta((int)$creditmemo->getRewardPointsBalanceToRefund())
						->setAction(Df_Reward_Model_Reward::REWARD_ACTION_CREDITMEMO)
						->setActionEntity($order)
						->save()
				;
			}
		}
		return $this;
	}

	/**
	 * Disable entire RP layout
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function disableLayout(Varien_Event_Observer $observer)
	{
		if (!df_h()->reward()->isEnabled()) {
			unset($observer->getUpdates()->df_reward);
		}
		return $this;
	}

	/**
	 * Send scheduled low balance warning notifications
	 * @return Df_Reward_Model_Observer
	 */
	public function scheduledBalanceExpireNotification()
	{
		if (!df_h()->reward()->isEnabled()) {
			return $this;
		}
		foreach (Mage::app()->getWebsites() as $website) {
			if (!df_h()->reward()->isEnabledOnFront($website->getId())) {
				continue;
			}
			$inDays = (int)df_h()->reward()->getNotificationConfig('expiry_day_before');
			if (!$inDays) {
				continue;
			}
			$collection = Mage::getResourceModel('df_reward/reward_history_collection')
				->setExpiryConfig(df_h()->reward()->getExpiryConfig())
				->loadExpiredSoonPoints($website->getId(), true)
				->addCustomerInfo()
				->setPageSize(20) // limit queues for each website
				->setCurPage(1)
				->load();
			foreach ($collection as $item) {
				Df_Reward_Model_Reward::s()->sendBalanceWarningNotification($item);
			}
		}
		return $this;
	}

	/**
	 * Make points expired
	 * @return Df_Reward_Model_Observer
	 */
	public function scheduledPointsExpiration() {
		if (df_h()->reward()->isEnabled()) {
			foreach (Mage::app()->getWebsites() as $website) {
				if (!df_h()->reward()->isEnabledOnFront($website->getId())) {
					continue;
				}
				$expiryType = df_h()->reward()->getGeneralConfig('expiry_calculation', $website->getId());
				Mage::getResourceModel('df_reward/reward_history')
					->expirePoints($website->getId(), $expiryType, 100);
			}
		}
		return $this;
	}

	/**
	 * Prepare orphan points of customers after website was deleted
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function prepareCustomerOrphanPoints(Varien_Event_Observer $observer)
	{
		/* @var $website Mage_Core_Model_Website */
		$website = $observer->getEvent()->getWebsite();
		Df_Reward_Model_Reward::i()->prepareOrphanPoints(
			$website->getId(), $website->getBaseCurrencyCode()
		);
		return $this;
	}

	/**
	 * Prepare salesrule form. Add field to specify reward points delta
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function prepareSalesruleForm(Varien_Event_Observer $observer)
	{
		if (df_h()->reward()->isEnabled()) {
			$form = $observer->getEvent()->getForm();
			$fieldset = $form->getElement('action_fieldset');
			if (!is_null($fieldset)) {
				$fieldset
					->addField(
						'reward_points_delta'
						,'text'
						,array(
							'name'  => 'reward_points_delta'
							,'label' => df_h()->reward()->__('Add Reward Points')
							,'title' => df_h()->reward()->__('Add Reward Points')
						)
						,'stop_rules_processing'
					)
				;
			}
		}
		return $this;
	}

	/**
	 * Set reward points delta to salesrule model after it loaded
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function loadRewardSalesruleData(Varien_Event_Observer $observer)
	{
		if (df_h()->reward()->isEnabled()) {
			/* @var $salesRule Mage_SalesRule_Model_Rule */
			$salesRule = $observer->getEvent()->getRule();
			if ($salesRule->getId()) {
				$data =
					Mage::getResourceModel('df_reward/reward')
						->getRewardSalesrule($salesRule->getId())
				;
				if (isset($data['points_delta'])) {
					$salesRule->setRewardPointsDelta($data['points_delta']);
				}
			}
		}
		return $this;
	}

	/**
	 * Save reward points delta for salesrule
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function saveRewardSalesruleData(Varien_Event_Observer $observer)
	{
		if (!df_h()->reward()->isEnabled()) {
			return $this;
		}
		/* @var $salesRule Mage_SalesRule_Model_Rule */
		$salesRule = $observer->getEvent()->getRule();
		/** @var Df_Reward_Model_Resource_Reward $rewardResource */
		$rewardResource = Mage::getResourceModel('df_reward/reward');
		$rewardResource
			->saveRewardSalesrule(
				$salesRule->getId()
				,(int)$salesRule->getRewardPointsDelta()
			)
		;
		return $this;
	}

	/**
	 * Update customer reward points balance by points from applied sales rules
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function applyRewardSalesrulePoints(Varien_Event_Observer $observer)
	{
		/* @var $order Mage_Sales_Model_Order */
		$order = $observer->getEvent()->getInvoice()->getOrder();
		if (!df_h()->reward()->isEnabledOnFront($order->getStore()->getWebsiteId())) {
			return $this;
		}
		if ($order->getCustomerId() && !$order->canInvoice() && $order->getRewardSalesrulePoints()) {
			$reward =
				Df_Reward_Model_Reward::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId($order->getStore()->getWebsiteId())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_SALESRULE)
					->setActionEntity($order)
					->setPointsDelta($order->getRewardSalesrulePoints())
					->updateRewardPoints()
			;
			if ($reward->getPointsDelta()) {
				$order->addStatusHistoryComment(
					df_h()->reward()->__('Customer earned promotion extra %s.', df_h()->reward()->formatReward($reward->getPointsDelta()))
				)->save();
			}
		}
		return $this;
	}

	/**
	 * If not all rates found, we should disable reward points on frontend
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Model_Observer
	 */
	public function checkRates(Varien_Event_Observer $observer)
	{
		if (!df_h()->reward()->isEnabledOnFront()) {
			return $this;
		}

		$groupId	= $observer->getEvent()->getCustomerSession()->getCustomer()->getGroupId();
		$websiteId  = Mage::app()->getStore()->getWebsiteId();
		$rate = Df_Reward_Model_Reward_Rate::i();
		$hasRates =
				$rate->fetch(
					$groupId, $websiteId, Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
				)->getId()
			&&
				$rate->reset()->fetch(
					$groupId, $websiteId, Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
				)->getId()
		;
		df_h()->reward()->setHasRates($hasRates);
		return $this;
	}

	/** @return Df_Reward_Model_Observer */
	public static function i() {return new self;}
}