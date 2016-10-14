<?php
class Df_CustomerBalance_Observer  {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_customer_prepare_save(Varien_Event_Observer $o) {
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			return;
		}
		/* @var Df_Customer_Model_Customer $customer */
		$customer = $o['customer'];
		/* @var $request Mage_Core_Controller_Request_Http */
		$request = $o['request'];
		/** @var array(string => string|int) $data */
		$data = $request->getPost('customerbalance');
		if ($data) {
			/** @used-by adminhtml_customer_save_after() */
			$customer->setCustomerBalanceData($data);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_customer_save_after(Varien_Event_Observer $o) {
		if (Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			/* @var Df_Customer_Model_Customer $customer */
			$customer = $o['customer'];
			/**
			 * @see adminhtml_customer_prepare_save()
			 * @var array(string => string|int) $data
			 */
			$data = $customer->getCustomerBalanceData();
			if ($data && !empty($data['amount_delta'])) {
				/** @var Df_CustomerBalance_Model_Balance $balance */
				$balance = Df_CustomerBalance_Model_Balance::i();
				$balance->setCustomer($customer);
				$balance->setWebsiteId(df_a($data, 'website_id', $customer->getWebsiteId()));
				$balance->setAmountDelta($data['amount_delta']);
				$balance->setComment($data['comment']);
				if (isset($data['notify_by_email']) && isset($data['store_id'])) {
					$balance->setNotifyByEmail(true, $data['store_id']);
				}
				$balance->save();
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
		if (Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			/**
			 * @var Mage_Adminhtml_Model_Sales_Order_Create $orderCreate
			 * @see Mage_Adminhtml_Sales_Order_CreateController::_getOrderCreateModel()
			 */
			$orderCreate = $o['order_create_model'];
			/** @var Mage_Sales_Model_Quote $quote */
			$quote = $orderCreate->getQuote();
			/**
			 * @var array(string => string|mixed[]) $request
			 * Zend_Controller_Request_Http::getPost()
			 */
			$request = $o['request'];
			/** @var array(string => mixed)|null $payment */
			$payment = df_a($request, 'payment');
			if ($payment) {
				/** @var bool|null $useBalance */
				$useBalance = df_a($payment, 'use_customer_balance');
				if (!is_null($useBalance)) {
					$this->importPaymentData($quote, $quote->getPayment(), !!$useBalance);
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
		if (isset($input['refund_customerbalance_return']) && isset($input['refund_customerbalance_return_enable'])) {
			$enable = $input['refund_customerbalance_return_enable'];
			$amount = $input['refund_customerbalance_return'];
			if ($enable && is_numeric($amount)) {
				$amount = max(0, min($creditmemo->getBaseCustomerBalanceReturnMax(), $amount));
				if ($amount) {
					$amount = $creditmemo->getStore()->roundPrice($amount);
					$creditmemo->setBaseCustomerBalanceTotalRefunded($amount);
					$amount = $creditmemo->getStore()->roundPrice(
						$amount*$creditmemo->getOrder()->getStoreToOrderRate()
					);
					$creditmemo->setCustomerBalanceTotalRefunded($amount);
					//setting flag to make actual refund to customer balance after creditmemo save
					$creditmemo->setCustomerBalanceRefundFlag(true);
					$creditmemo->setPaymentRefundDisallowed(true);
				}
			}
		}
		if (isset($input['refund_customerbalance']) && $input['refund_customerbalance']) {
			$creditmemo->setRefundCustomerBalance(true);
		}
		if (isset($input['refund_real_customerbalance']) && $input['refund_real_customerbalance']) {
			$creditmemo->setRefundRealCustomerBalance(true);
			$creditmemo->setPaymentRefundDisallowed(true);
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
					Df_CustomerBalance_Block_Checkout_Payment::html('before')
					. $transport['html']
					. Df_CustomerBalance_Block_Checkout_Payment::html('after')
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
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			/** @var Mage_Core_Model_Config_Element $updates */
			$updates = $o['updates'];
			/** @see app/design/frontend/rm/default/layout/df/customerbalance.xml */
			unset($updates->{'df_customerbalance'});
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
	public function payment_method_is_active(Varien_Event_Observer $o) {
		if (Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			/** @var Mage_Sales_Model_Quote|null|Df_Sales_Model_Quote $quote */
			$quote = $o['quote'];
			if ($quote) {
				/** @var Df_CustomerBalance_Model_Balance|null $balance */
				$balance = $quote->getCustomerBalanceInstance();
				if ($balance && $balance->isFullAmountCovered($quote)) {
					// disable all payment methods and enable only Zero Subtotal Checkout
					$result = $o['result'];
					/** @var Mage_Payment_Model_Method_Abstract $paymentMethod */
					$paymentMethod = $o['method_instance'];
					$result->isAvailable = 'free' === $paymentMethod->getCode();
				}
			}
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
		$order = $creditmemo->getOrder();
		if ($creditmemo->getRefundRealCustomerBalance() && $creditmemo->getBaseGrandTotal()) {
			$baseAmount = $creditmemo->getBaseGrandTotal();
			$amount = $creditmemo->getGrandTotal();
			$creditmemo->setBaseCustomerBalanceTotalRefunded($creditmemo->getBaseCustomerBalanceTotalRefunded() + $baseAmount);
			$creditmemo->setCustomerBalanceTotalRefunded($creditmemo->getCustomerBalanceTotalRefunded() + $amount);
		}
		if ($creditmemo->getBaseCustomerBalanceAmount()) {
			if ($creditmemo->getRefundCustomerBalance()) {
				$baseAmount = $creditmemo->getBaseCustomerBalanceAmount();
				$amount = $creditmemo->getCustomerBalanceAmount();
				$creditmemo->setBaseCustomerBalanceTotalRefunded($creditmemo->getBaseCustomerBalanceTotalRefunded() + $baseAmount);
				$creditmemo->setCustomerBalanceTotalRefunded($creditmemo->getCustomerBalanceTotalRefunded() + $amount);
			}

			$order->setBaseCustomerBalanceRefunded($order->getBaseCustomerBalanceRefunded() + $creditmemo->getBaseCustomerBalanceAmount());
			$order->setCustomerBalanceRefunded($order->getCustomerBalanceRefunded() + $creditmemo->getCustomerBalanceAmount());
			// we need to update flag after credit memo was refunded and order's properties changed
			if ($order->getCustomerBalanceInvoiced() == $order->getCustomerBalanceRefunded()) {
				$order->setForcedCanCreditmemo(false);
			}
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
		//doing actual refund to customer balance if user have submitted refund form
		if ($creditmemo->getCustomerBalanceRefundFlag() && $creditmemo->getBaseCustomerBalanceTotalRefunded()) {
			/** @var Df_Sales_Model_Order $order */
			$order = $creditmemo->getOrder();
			$order->setBaseCustomerBalanceTotalRefunded($order->getBaseCustomerBalanceTotalRefunded() + $creditmemo->getBaseCustomerBalanceTotalRefunded());
			$order->setCustomerBalanceTotalRefunded($order->getCustomerBalanceTotalRefunded() + $creditmemo->getCustomerBalanceTotalRefunded());
			$websiteId = rm_store($order->getStoreId())->getWebsiteId();
			$balance = Df_CustomerBalance_Model_Balance::i()
				->setCustomerId($order->getCustomerId())
				->setWebsiteId($websiteId)
				->setAmountDelta($creditmemo->getBaseCustomerBalanceTotalRefunded())
				->setHistoryAction(Df_CustomerBalance_Model_Balance_History::ACTION_REFUNDED)
				->setOrder($order)
				->setCreditMemo($creditmemo)
				->save();
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
		$order = $invoice->getOrder();
		if ($invoice->getBaseCustomerBalanceAmount()) {
			$order->setBaseCustomerBalanceInvoiced($order->getBaseCustomerBalanceInvoiced() + $invoice->getBaseCustomerBalanceAmount());
			$order->setCustomerBalanceInvoiced($order->getCustomerBalanceInvoiced() + $invoice->getCustomerBalanceAmount());
		}
		/**
		 * Because of order doesn't save second time, added forced saving below attributes
		 */
		$order->getResource()->saveAttribute($order, 'base_customer_balance_invoiced');
		$order->getResource()->saveAttribute($order, 'customer_balance_invoiced');
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
			&& $order->getCustomerBalanceInvoiced() - $order->getCustomerBalanceRefunded() > 0
		) {
			$order->setForcedCanCreditmemo(true);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_place_after(Varien_Event_Observer $o) {
		if (Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			/** @var Df_Sales_Model_Order $order */
			$order = $o['order'];
			if ($order->getBaseCustomerBalanceAmount() > 0) {
				$websiteId = rm_store($order->getStoreId())->getWebsiteId();
				Df_CustomerBalance_Model_Balance::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId($websiteId)
					->setAmountDelta(-$order->getBaseCustomerBalanceAmount())
					->setHistoryAction(Df_CustomerBalance_Model_Balance_History::ACTION_USED)
					->setOrder($order)
					->save();
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_order_place_before(Varien_Event_Observer $o) {
		if (Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			/** @var Df_Sales_Model_Order $order */
			$order = $o['order'];
			if (0 < $order->getBaseCustomerBalanceAmount()) {
				$websiteId = rm_store($order->getStoreId())->getWebsiteId();
				$balance = Df_CustomerBalance_Model_Balance::i()
					->setCustomerId($order->getCustomerId())
					->setWebsiteId($websiteId)
					->loadByCustomer()
					->getAmount();
				if (($order->getBaseCustomerBalanceAmount() - $balance) >= 0.0001) {
					/** @var Mage_Checkout_Model_Session|Df_Checkout_Model_Session $session */
					$session = Df_Checkout_Model_Type_Onepage::s()->getCheckout();
					/** @used-by Mage_Checkout_OnepageController::saveOrderAction() */
					$session->setUpdateSection('payment-method');
					$session->setGotoSection('payment');
					Mage::throwException(Df_CustomerBalance_Helper_Data::s()->__('Not enough Store Credit Amount to complete this Order.'));
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_quote_collect_totals_before(Varien_Event_Observer $o) {
		/** @var Mage_Sales_Model_Quote|Df_Sales_Model_Quote $quote */
		$quote = $o['quote'];
		$quote->setCustomerBalanceCollected(false);
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Sales_Model_Quote::merge()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_quote_merge_after(Varien_Event_Observer $o) {
		/** @var Mage_Sales_Model_Quote|Df_Sales_Model_Quote $quote */
		$quote = $o['quote'];
		/** @var Mage_Sales_Model_Quote|Df_Sales_Model_Quote $source */
		$source = $o['source'];
		if ($source->getUseCustomerBalance()) {
			$quote->setUseCustomerBalance($source->getUseCustomerBalance());
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
	public function sales_quote_payment_import_data_before(Varien_Event_Observer $o) {
		if (Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			/** @var Varien_Object $input */
			$input = $o['input'];
			/** @var Mage_Sales_Model_Quote_Payment $payment */
			$payment = $o['payment'];
			$this->importPaymentData($payment->getQuote(), $input, $input['use_customer_balance']);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function website_delete_before(Varien_Event_Observer $o) {
		/** @var Mage_Core_Model_Website $input */
		$website = $o['website'];
		Df_CustomerBalance_Model_Resource_Balance::s()->setCustomersBalanceCurrencyTo(
			$website->getWebsiteId(), $website->getBaseCurrencyCode()
		);
	}

	/**
	 * Analyze payment data for quote and set free shipping if grand total is covered by balance
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 * @param Varien_object|Mage_Sales_Model_Quote_Payment $payment
	 * @param bool $shouldUseBalance
	 */
	private function importPaymentData($quote, $payment, $shouldUseBalance)  {
		$store = rm_store($quote->getStoreId());
		if (!$quote || !$quote->getCustomerId()) {
			return;
		}
		$quote->setUseCustomerBalance($shouldUseBalance);
		if ($shouldUseBalance) {
			$balance = Df_CustomerBalance_Model_Balance::i()
				->setCustomerId($quote->getCustomerId())
				->setWebsiteId($store->getWebsiteId())
				->loadByCustomer();
			if ($balance) {
				$quote->setCustomerBalanceInstance($balance);
				if (!$payment->getMethod()) {
					$payment->setMethod('free');
				}
			}
			else {
				$quote->setUseCustomerBalance(false);
			}
		}
	}
}