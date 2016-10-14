<?php
class Df_Reward_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions::_prepareForm()
		Mage::dispatchEvent('adminhtml_block_salesrule_actions_prepareform', array('form' => $form));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_block_salesrule_actions_prepareform(Varien_Event_Observer $o) {
		if (df_h()->reward()->isEnabled()) {
			/** @var Varien_Data_Form $form */
			$form = $o['form'];
			/** @var Varien_Data_Form_Element_Fieldset|null $fieldset */
			$fieldset = $form->getElement('action_fieldset');
			if ($fieldset) {
				$fieldset->addField('reward_points_delta','text', array(
					'name'  => 'reward_points_delta'
					,'label' => df_h()->reward()->__('Add Reward Points')
					,'title' => df_h()->reward()->__('Add Reward Points')
				), 'stop_rules_processing');
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Adminhtml_CustomerController::saveAction()
		Mage::dispatchEvent('adminhtml_customer_prepare_save', array(
			'customer'  => $customer,
			'request'   => $this->getRequest()
		));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_customer_prepare_save(Varien_Event_Observer $o) {
		if (df_h()->reward()->isEnabled()) {
			/* @var Df_Customer_Model_Customer $customer */
			$customer = $o['customer'];
			/* @var $request Mage_Core_Controller_Request_Http */
			$request = $o['request'];
			$data = $request->getPost('reward');
			$subscribeByDefault = df_h()->reward()->getNotificationConfig('subscribe_by_default');
			if ($customer->isObjectNew()) {
				$data['reward_update_notification']  = (int)$subscribeByDefault;
				$data['reward_warning_notification'] = (int)$subscribeByDefault;
			}
			$customer->setRewardUpdateNotification((isset($data['reward_update_notification']) ? 1 : 0));
			$customer->setRewardWarningNotification((isset($data['reward_warning_notification']) ? 1 : 0));
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_customer_save_after(Varien_Event_Observer $o) {
		if (df_h()->reward()->isEnabled()) {
			/* @var Df_Customer_Model_Customer $customer */
			$customer = $o['customer'];
			/* @var $request Mage_Core_Controller_Request_Http */
			$request = $o['request'];
			$data = $request->getPost('reward');
			if ($data) {
				if (!isset($data['store_id'])) {
					if ($customer->getStoreId() == 0) {
						$data['store_id'] = Mage::app()->getDefaultStoreView()->getWebsiteId();
					} else {
						$data['store_id'] = $customer->getStoreId();
					}
				}
				$reward = Df_Reward_Model_Reward::i()
					->setCustomer($customer)
					->setWebsiteId(rm_store($data['store_id'])->getWebsiteId())
					->loadByCustomer()
				;
				if (!empty($data['points_delta'])) {
					$reward->addData($data);
					$reward
						->setAction(Df_Reward_Model_Reward::REWARD_ACTION_ADMIN)
						->setActionEntity($customer)
						->updateRewardPoints();
				} else {
					$reward->save();
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Adminhtml_Sales_Order_CreateController::_processActionData()
			$eventData = array(
			 'order_create_model' => $this->_getOrderCreateModel(),
			 'request' => $this->getRequest()->getPost(),
		 );
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_sales_order_create_process_data(Varien_Event_Observer $o) {
		/**
		 * @var Mage_Adminhtml_Model_Sales_Order_Create $orderCreate
		 * @see Mage_Adminhtml_Sales_Order_CreateController::_getOrderCreateModel()
		 */
		$orderCreate = $o['order_create_model'];
		/** @var Mage_Sales_Model_Quote $quote */
		$quote = $orderCreate->getQuote();
		if (df_h()->reward()->isEnabledOnFront($quote->getStore()->getWebsiteId())) {
			/**
			 * @var array(string => string|mixed[]) $request
			 * Zend_Controller_Request_Http::getPost()
			 */
			$request = $o['request'];
			/** @var array(string => mixed)|null $payment */
			$payment = df_a($request, 'payment');
			if ($payment) {
				/** @var bool|null $usePoints */
				$usePoints = df_a($payment, 'use_reward_points');
				if (!is_null($usePoints)) {
					$this->_paymentDataImport($quote, $quote->getPayment(), !!$usePoints);
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Adminhtml_Sales_Order_CreditmemoController::_initCreditmemo()
		$args = array('creditmemo' => $creditmemo, 'request' => $this->getRequest());
		Mage::dispatchEvent('adminhtml_sales_order_creditmemo_register_before', $args);
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_sales_order_creditmemo_register_before(Varien_Event_Observer $o) {
		/** @var Mage_Core_Controller_Request_Http $request */
		$request = $request = $o['request'];
		/** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
		$creditmemo = $o['creditmemo'];
		$input = $request->getParam('creditmemo');
		if (isset($input['refund_reward_points']) && isset($input['refund_reward_points_enable'])) {
			$enable = $input['refund_reward_points_enable'];
			$balance = (int)$input['refund_reward_points'];
			$balance = min($creditmemo->getRewardPointsBalance(), $balance);
			if ($enable && $balance) {
				$creditmemo->setRewardPointsBalanceToRefund($balance);
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_block_abstract_to_html_after(Varien_Event_Observer $o) {
		try {
			/** @var Mage_Core_Block_Abstract $block */
			$block = $o['block'];
			if ('checkout.payment.methods' === $block->getNameInLayout()) {
				/** @var Varien_Object $transport */
				$transport = $o['transport'];
				$transport['html'] =
					Df_Reward_Block_Checkout_Payment::html('before')
					. $transport['html']
					. Df_Reward_Block_Checkout_Payment::html('after')
				;
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Df_Core_Model_Layout_Update::getFileLayoutUpdatesXml_Df()
	 * @see Mage_Core_Model_Layout_Update::getFileLayoutUpdatesXml()
		Mage::dispatchEvent('core_layout_update_updates_get_after', array('updates' => $updatesRoot));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_layout_update_updates_get_after(Varien_Event_Observer $o) {
		if (!df_h()->reward()->isEnabled()) {
			/** @var Mage_Core_Model_Config_Element $updates */
			$updates = $o['updates'];
			/** @see app/design/frontend/rm/default/layout/df/reward.xml */
			unset($updates->{'df_reward'});
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function customer_save_after(Varien_Event_Observer $o) {
		if (df_h()->reward()->isEnabledOnFront()) {
			/* @var Df_Customer_Model_Customer $customer */
			$customer = $o['customer'];
			if ($customer->getData('rm_object_new')) {
				try {
					$subscribeByDefault =
						df_h()->reward()->getNotificationConfig('subscribe_by_default', rm_website_id())
					;
					/** @var Df_Reward_Model_Reward $reward */
					$reward = Df_Reward_Model_Reward::i();
					$reward->setCustomer($customer);
					$reward->addData(array(
						Df_Reward_Model_Reward::P__ACTION_ENTITY => $customer
						,Df_Reward_Model_Reward::P__STORE => rm_store_id()
						,Df_Reward_Model_Reward::P__ACTION => Df_Reward_Model_Reward::REWARD_ACTION_REGISTER
					));
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
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function customer_save_before(Varien_Event_Observer $o) {
		try {
			/* @var Df_Customer_Model_Customer $customer */
			$customer = $o['customer'];
			if ($customer->isObjectNew()) {
				/**
				 * К сожалению, при событии customer_save_after от isObjectNew() толку мало,
				 * потому что метод Mage_Eav_Model_Entity_Abstract::_processSaveData()
				 * почему-то удаляет флаг isObjectNew() до наступления события customer_save_after
				 */
				$customer->setData('rm_object_new', true);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Customer_Model_Session::__construct()
		Mage::dispatchEvent('customer_session_init', array('customer_session'=>$this));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function customer_session_init(Varien_Event_Observer $o)
	{
		if (df_h()->reward()->isEnabledOnFront()) {
			/** @var Mage_Customer_Model_Session $session */
			$session = $o['customer_session'];
			$groupId = $session->getCustomer()->getGroupId();
			$websiteId = rm_website_id();
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
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function df_invitation_save_commit_after(Varien_Event_Observer $o) {
		/* @var Df_Invitation_Model_Invitation $invitation */
		$invitation = $o['invitation'];
		$websiteId = rm_store($invitation->getStoreId())->getWebsiteId();
		if (df_h()->reward()->isEnabledOnFront($websiteId)
			&& $invitation->getCustomerId()
			&& $invitation->getReferralId()
		) {
			Df_Reward_Model_Reward::i()
				->setCustomerId($invitation->getCustomerId())
				->setWebsiteId($websiteId)
				->setAction(Df_Reward_Model_Reward::REWARD_ACTION_INVITATION_CUSTOMER)
				->setActionEntity($invitation)
				->updateRewardPoints()
			;
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function newsletter_subscriber_save_commit_after(Varien_Event_Observer $o) {
		/* @var Mage_Newsletter_Model_Subscriber $subscriber */
		$subscriber = $o['subscriber'];
		// reward only new subscribtions
		if ($subscriber->isObjectNew() && $subscriber->getCustomerId()) {
			$websiteId = rm_store($subscriber->getStoreId())->getWebsiteId();
			if (df_h()->reward()->isEnabledOnFront($websiteId)) {
				/** @var Df_Reward_Model_Reward $reward */
				$reward = Df_Reward_Model_Reward::i();
				$reward
					->setCustomerId($subscriber->getCustomerId())
					->setStore($subscriber->getStoreId())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_NEWSLETTER)
					->setActionEntity($subscriber)
				;
				$reward->updateRewardPoints();
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Payment_Model_Method_Abstract::isAvailable()
			Mage::dispatchEvent('payment_method_is_active', array(
			'result' => $checkResult,
			'method_instance' => $this,
			'quote' => $quote,
		 ));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function payment_method_is_active(Varien_Event_Observer $o)
	{
		if (df_h()->reward()->isEnabledOnFront()) {
			/** @var Mage_Sales_Model_Quote|null|Df_Sales_Model_Quote $quote */
			$quote = $o['quote'];
			if ($quote && $quote->getId()) {
				/* @var Df_Reward_Model_Reward|null $reward */
				$reward = $quote->getRewardInstance();
				if ($reward && $reward->getId()) {
					$baseQuoteGrandTotal = $quote->getBaseGrandTotal() + $quote->getBaseRewardCurrencyAmount();
					if ($reward->isEnoughPointsToCoverAmount($baseQuoteGrandTotal)) {
						// disable all payment methods and enable only Zero Subtotal Checkout
						$result = $o['result'];
						/** @var Mage_Payment_Model_Method_Abstract $paymentMethod */
						$paymentMethod = $o['method_instance'];
						$result->isAvailable = 'free' === $paymentMethod->getCode();
					}
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function review_save_commit_after(Varien_Event_Observer $o) {
		/* @var Mage_Review_Model_Review|Df_Review_Model_Review $review */
		$review = $o['object'];
		$websiteId = rm_store($review->getStoreId())->getWebsiteId();
		if (
			df_h()->reward()->isEnabledOnFront($websiteId)
			&& $review->isApproved()
			&& $review->getCustomerId()
		) {
			Df_Reward_Model_Reward::i()
				->setCustomerId($review->getCustomerId())
				->setStore($review->getStoreId())
				->setAction(Df_Reward_Model_Reward::REWARD_ACTION_REVIEW)
				->setActionEntity($review)
				->updateRewardPoints()
			;
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_creditmemo_refund(Varien_Event_Observer $o) {
		/** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
		$creditmemo = $o['creditmemo'];
		/* @var Df_Sales_Model_Order $order */
		$order = $creditmemo->getOrder();
		$refundedAmount =
			(float)($order->getBaseRewardCurrencyAmountRefunded() + $creditmemo->getBaseRewardCurrencyAmount())
		;
		if ((float)$order->getBaseRewardCurrencyAmountInvoiced() == $refundedAmount) {
			$order->setForcedCanCreditmemo(false);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_creditmemo_save_after(Varien_Event_Observer $o) {
		/** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
		$creditmemo = $o['creditmemo'];
		if ($creditmemo->getBaseRewardCurrencyAmount()) {
			/** @var Df_Sales_Model_Order $order */
			$order = $creditmemo->getOrder();
			$order->setRewardPointsBalanceRefunded($order->getRewardPointsBalanceRefunded() + $creditmemo->getRewardPointsBalance());
			$order->setRewardCurrencyAmountRefunded($order->getRewardCurrencyAmountRefunded() + $creditmemo->getRewardCurrencyAmount());
			$order->setBaseRewardCurrencyAmountRefunded($order->getBaseRewardCurrencyAmountRefunded() + $creditmemo->getBaseRewardCurrencyAmount());
			$order->setRewardPointsBalanceToRefund($order->getRewardPointsBalanceToRefund() + $creditmemo->getRewardPointsBalanceToRefund());
			if ((int)$creditmemo->getRewardPointsBalanceToRefund() > 0) {
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
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_invoice_save_after(Varien_Event_Observer $o) {
		/** @var Mage_Sales_Model_Order_Invoice $invoice */
		$invoice = $o['invoice'];
		if ($invoice->getBaseRewardCurrencyAmount()) {
			$order = $invoice->getOrder();
			$order->setRewardCurrencyAmountInvoiced($order->getRewardCurrencyAmountInvoiced() + $invoice->getRewardCurrencyAmount());
			$order->setBaseRewardCurrencyAmountInvoiced($order->getBaseRewardCurrencyAmountInvoiced() + $invoice->getBaseRewardCurrencyAmount());
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_invoice_save_commit_after(Varien_Event_Observer $o) {
		/** @var Mage_Sales_Model_Order_Invoice $invoice */
		$invoice = $o['invoice'];
		/* @var $order Df_Sales_Model_Order */
		$order = $invoice->getOrder();
		if (df_h()->reward()->isEnabledOnFront($order->getStore()->getWebsiteId())) {
			if ($order->getCustomerId()
				&& !$order->canInvoice()
				&& $order->getRewardSalesrulePoints()
			) {
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
					$order->comment(df_h()->reward()->__(
						'Customer earned promotion extra %s.'
						, df_h()->reward()->formatReward($reward->getPointsDelta())
					));
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_load_after(Varien_Event_Observer $o) {
		/** @var Df_Sales_Model_Order $order */
		$order = $o['order'];
		if (
			!$order->canUnhold()
			&& !$order->isCanceled()
			&& $order->getState() !== Mage_Sales_Model_Order::STATE_CLOSED
			&&
					$order->getBaseRewardCurrencyAmountInvoiced()
				-
					$order->getBaseRewardCurrencyAmountRefunded()
				> 0
		) {
			$order->setForcedCanCreditmemo(true);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_place_after(Varien_Event_Observer $o)
	{
		if (df_h()->reward()->isEnabledOnFront()) {
			/** @var Df_Sales_Model_Order $order */
			$order = $o['order'];
			if ($order->getBaseRewardCurrencyAmount() > 0) {
				Df_Reward_Model_Reward::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId(rm_store($order->getStoreId())->getWebsiteId())
					->setPointsDelta(-$order->getRewardPointsBalance())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_ORDER)
					->setActionEntity($order)
					->updateRewardPoints()
				;
			}
			/** @var int[] $ruleIds */
			$ruleIds = rm_array_unique_fast(df_csv_parse_int($order->getAppliedRuleIds()));
			$data = Df_Reward_Model_Resource_Reward::s()->getRewardSalesrule($ruleIds);
			$pointsDelta = array_sum(rm_int(array_column($data, 'points_delta')));
			if ($pointsDelta) {
				$order->setRewardSalesrulePoints($pointsDelta);
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_place_before(Varien_Event_Observer $o) {
		if (df_h()->reward()->isEnabledOnFront()) {
			/** @var Df_Sales_Model_Order $order */
			$order = $o['order'];
			if ($order->getRewardPointsBalance() > 0) {
				$websiteId = rm_store($order->getStoreId())->getWebsiteId();
				/* @var Df_Reward_Model_Reward $reward */
				$reward = Df_Reward_Model_Reward::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId($websiteId)
					->loadByCustomer()
				;
				if (($order->getRewardPointsBalance() - $reward->getPointsBalance()) > 0) {
					/** @var Mage_Checkout_Model_Session $session */
					$session = Df_Checkout_Model_Type_Onepage::s()->getCheckout();
					$session->setUpdateSection('payment-method');
					$session->setGotoSection('payment');
					Mage::throwException(df_h()->reward()->__('Not enough Reward Points to complete this Order.'));
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_save_after(Varien_Event_Observer $o) {
		/** @var Df_Sales_Model_Order $order */
		$order = $o['order'];
		if (
			!$order->getCustomerIsGuest()
			&& df_h()->reward()->isEnabledOnFront($order->getStore()->getWebsiteId())
			&& $order->getCustomerId()
			&& ((float)$order->getBaseTotalPaid() > 0)
			&& (
				   $order->getBaseGrandTotal() - $order->getBaseSubtotalCanceled()
			   ) - $order->getBaseTotalPaid()
				< 0.0001
		) {
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
				$order->comment(df_h()->reward()->__(
					'Customer earned %s for the order.'
					, df_h()->reward()->formatReward($reward->getPointsDelta())
				));
			}
			// Also update inviter balance if possible
			$this->_invitationToOrder($o);
		}
	}

	/**
	 * Модуль при получении сообщения «sales_quote_collect_totals_before»
	 * обнуляет свои счётчики применимых к корзине правил с накопительными скидками.
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_quote_collect_totals_before(Varien_Event_Observer $o) {
		try {
			if (!df_is_admin() && df_h()->reward()->isEnabledOnFront()) {
				// обнуляем счётчики применимых к корзине правил с накопительными скидками
				df_h()->reward()->getSalesRuleApplications()->clear();
			}
			/** @var Mage_Sales_Model_Quote|Df_Sales_Model_Quote $quote */
			$quote = $o['quote'];
			$quote->setRewardPointsTotalReseted(false);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_quote_merge_after(Varien_Event_Observer $o) {
		/** @var Mage_Sales_Model_Quote|Df_Sales_Model_Quote $quote */
		$quote = $o['quote'];
		/** @var Mage_Sales_Model_Quote|Df_Sales_Model_Quote $source */
		$source = $o['source'];
		if ($source->getUseRewardPoints()) {
			$quote->setUseRewardPoints($source->getUseRewardPoints());
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Sales_Model_Quote_Payment::importData()
			Mage::dispatchEvent(
				$this->_eventPrefix . '_import_data_before',
				array(
					$this->_eventObject=>$this,
					'input'=>$data,
				)
			);
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_quote_payment_import_data_before(Varien_Event_Observer $o)
	{
		if (df_h()->reward()->isEnabledOnFront()) {
			/** @var Varien_Object $input */
			$input = $o['input'];
			/** @var Mage_Sales_Model_Quote_Payment $payment */
			$payment = $o['payment'];
			$this->_paymentDataImport($payment->getQuote(), $input, $input->getUseRewardPoints());
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function salesrule_rule_load_after(Varien_Event_Observer $o)
	{
		if (df_h()->reward()->isEnabled()) {
			/* @var Mage_SalesRule_Model_Rule $salesRule */
			$salesRule = $o['rule'];
			if ($salesRule->getId()) {
				$data = Df_Reward_Model_Resource_Reward::s()->getRewardSalesrule($salesRule->getId());
				if (isset($data['points_delta'])) {
					$salesRule->setRewardPointsDelta($data['points_delta']);
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function salesrule_rule_save_after(Varien_Event_Observer $o) {
		if (df_h()->reward()->isEnabled()) {
			/* @var Mage_SalesRule_Model_Rule $salesRule */
			$salesRule = $o['rule'];
			Df_Reward_Model_Resource_Reward::s()->saveRewardSalesrule(
				$salesRule->getId(), (int)$salesRule->getRewardPointsDelta()
			);
		}
	}

	/**
	 * Подсчитываем применимые к корзине правила с накопительными скидками
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function salesrule_validator_process(Varien_Event_Observer $o) {
		try {
			if (df_h()->reward()->isEnabledOnFront()) {
				// чтобы коллекция не ругалась на элемент без идентификатора
				$o['id'] = rm_uniqid();
				df_h()->reward()->getSalesRuleApplications()->addItem($o);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Cron_Model_Observer::_processJob()
	 * @return void
	 */
	public function scheduledPointsExpiration() {
		if (df_h()->reward()->isEnabled()) {
			foreach (Mage::app()->getWebsites() as $website) {
				if (!df_h()->reward()->isEnabledOnFront($website->getId())) {
					continue;
				}
				$expiryType = df_h()->reward()->getGeneralConfig('expiry_calculation', $website->getId());
				Df_Reward_Model_Resource_Reward_History::s()->expirePoints(
					$website->getId(), $expiryType, 100
				);
			}
		}
	}

	/**
	 * Send scheduled low balance warning notifications
	 * @return Df_Reward_Observer
	 */
	public function scheduledBalanceExpireNotification() {
		if (df_h()->reward()->isEnabled()) {
			foreach (Mage::app()->getWebsites() as $website) {
				if (!df_h()->reward()->isEnabledOnFront($website->getId())) {
					continue;
				}
				$inDays = (int)df_h()->reward()->getNotificationConfig('expiry_day_before');
				if (!$inDays) {
					continue;
				}
				$collection =
					Df_Reward_Model_Reward_History::c()
						->setExpiryConfig(df_h()->reward()->getExpiryConfig())
						->loadExpiredSoonPoints($website->getId(), true)
						->addCustomerInfo()
						->setPageSize(20) // limit queues for each website
						->setCurPage(1)
						->load()
				;
				foreach ($collection as $item) {
					Df_Reward_Model_Reward::s()->sendBalanceWarningNotification($item);
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function tag_save_commit_after(Varien_Event_Observer $o) {
		/* @var Mage_Tag_Model_Tag $tag */
		$tag = $o['object'];
		$websiteId = rm_store($tag->getFirstStoreId())->getWebsiteId();
		if (
			df_h()->reward()->isEnabledOnFront($websiteId)
			&& $tag->getApprovedStatus() == $tag->getStatus()
			&& $tag->getFirstCustomerId()
		) {
			Df_Reward_Model_Reward::i()
				->setCustomerId($tag->getFirstCustomerId())
				->setStore($tag->getFirstStoreId())
				->setAction(Df_Reward_Model_Reward::REWARD_ACTION_TAG)
				->setActionEntity($tag)
				->updateRewardPoints()
			;
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function website_delete_after(Varien_Event_Observer $o) {
		/** @var Mage_Core_Model_Website $input */
		$website = $o['website'];
		Df_Reward_Model_Reward::i()->prepareOrphanPoints($website->getId(), $website->getBaseCurrencyCode());
	}

	/**
	 * Update inviter points balance after referral's order completed
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Reward_Observer
	 */
	private function _invitationToOrder(Varien_Event_Observer $observer)
	{
		if (df_module_enabled(Df_Core_Module::INVITATION)) {
			/* @var Df_Sales_Model_Order $order */
			$order = $observer->getEvent()->getOrder();
			$invitation =
				Df_Invitation_Model_Invitation::i()->load($order->getCustomerId(), 'referral_id')
			;
			if ($invitation->getId() && $invitation->getCustomerId()) {
				Df_Reward_Model_Reward::i()
					->setActionEntity($invitation)
					->setCustomerId($invitation->getCustomerId())
					->setStore($order->getStoreId())
					->setAction(Df_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER)
					->updateRewardPoints();
			}
		}
	}

	/**
	 * Prepare and set to quote reward balance instance,
	 * set zero subtotal checkout payment if need
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 * @param Varien_Object $payment
	 * @param boolean $useRewardPoints
	 * @return void
	 */
	private function _paymentDataImport($quote, $payment, $useRewardPoints) {
		if ($quote && $quote->getCustomerId()) {
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
		}
	}
}