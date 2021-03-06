<?php
class Df_Chronopay_StandardController extends Mage_Core_Controller_Front_Action {
	/** @return Df_Sales_Model_Order */
	public function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Sales_Model_Order::i();
			$this->{__METHOD__}->loadByIncrementId(rm_session_checkout()->getLastRealOrderId());
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	public function redirectAction() {
		if (!df_enabled(Df_Core_Feature::CHRONOPAY)) {
			df_error('Вам надо купить лицензию на Российскую сборку Magento');
		}
		rm_session_checkout()->setChronopayStandardQuoteId(rm_session_checkout()->getQuoteId());
		$order = $this->getOrder();
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}
		$order
			->addStatusToHistory(
				$order->getStatus()
				,df_h()->chronopay()->__('Customer was redirected to Chronopay')
			)
			->save()
		;
		/** @var Df_Chronopay_Block_Standard_Redirect $block */
		$block = Df_Chronopay_Block_Standard_Redirect::i();
		$block->setData('order', $order);
		$this->getResponse()->setBody($block->toHtml());
		rm_session_checkout()->unsQuoteId();
	}

	/** @return void */
	public function  successAction()
	{
		$session = rm_session_checkout();
		$session->setQuoteId($session->getChronopayStandardQuoteId());
		$session->unsChronopayStandardQuoteId();
		$order = $this->getOrder();
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}
		$order->addStatusToHistory(
			$order->getStatus(),df_h()->chronopay()->__('Customer successfully returned from Chronopay')
		);
		$order->save();
		$this->_redirect('checkout/onepage/success');
	}

	/** @return void */
	public function notifyAction()
	{
		$processingResult = false;
		$this->checkOutputNotStarted();
		ob_start();
		try {
			$postData = $this->getRequest()->getPost();
			if (!count($postData)) {
				$this->norouteAction();
			}
			else {
				/** @var Df_Sales_Model_Order $order */
				$order = Df_Sales_Model_Order::i();
				$order->loadByIncrementId(df_mage()->coreHelper()->decrypt($postData['cs1']));
				if (!$order->getId()) {
					df_error("invalid order id");
				}
				else {
					if ($this->isOrderAlreadyPayed($order)) {
						$processingResult = true;
					}
					else {
						$result =
							$order->getPayment()->getMethodInstance()
								->setOrder($order)
								->validateResponse($postData)
						;
						if ($result instanceof Exception) {
							if ($order->getId()) {
								$order->addStatusToHistory($order->getStatus(), $result->getMessage());
								$order->cancel();
							}
							df_handle_entry_point_exception($result, true);
						}
						else {
							$order->sendNewOrderEmail();
							$order->getPayment()->getMethodInstance()->setTransactionId($postData['transaction_id']);
							if ($this->saveInvoice($order)) {
								$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
								$order
									->addStatusToHistory(
										Mage_Sales_Model_Order::STATE_PROCESSING
										,$this->getPayedComment()
										//Mage::helper("df_chronopay/standard")->getStatusOfPayedOrder()
										,true
									)
								;
								$order->save();
								$processingResult = true;
							}
						}
					}
				}
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
		/**
		 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
		 * в другой точке программы при аналогичном вызове @see ob_get_clean.
		 */
		$output = @ob_get_clean();
		if (!empty($output)) {
			df_error(
				"invalid (but catched) output\n:%s"
				,$output
			);
		}
		if (!$processingResult) {
			df_error("invalid processing");
		}
	}

	/** @return string */
	private function getPayedComment() {
		return "The payment has been confirmed by ChronoPay";
	}

	/**
	 * @param Mage_Sales_Model_Order $order
	 * @return bool
	 */
	protected function isOrderAlreadyPayed(Mage_Sales_Model_Order $order) {
		$result = false;
		foreach ($order->getStatusHistoryCollection() as $historyItem) {
			/** @var Mage_Sales_Model_Order_Status_History $historyItem */
			if ($this->getPayedComment() === $historyItem->getComment()) {
				$result = true;
				break;
			}
		}
		return $result;
	}

	/** @return void */
	private function checkOutputNotStarted() {
		$file = null;
		$line = null;
		if (headers_sent($file, $line)) {
			df_error("Error: headers already sent in %s on %s", $file, $line);
		}
	}

	/**
	 * @param Mage_Sales_Model_Order $order
	 * @return bool
	 */
	protected function saveInvoice(Mage_Sales_Model_Order $order)
	{
		//if ($order->canInvoice()) {
			$invoice = $order->prepareInvoice();
			$invoice->register();
			$invoice->capture();
			Df_Core_Model_Resource_Transaction::i()
			   ->addObject($invoice)
			   ->addObject($invoice->getOrder())
			   ->save()
			;
			return true;
		//}
	   // Mage::log("can not invoice!");
	   // Mage::log("order state is: " . $this->getState());
		//return false;
	}

	/** @return void */
	public function failureAction()
	{
		$errorMsg = df_h()->chronopay()->__('There was an error occurred during paying process.');
		$order = $this->getOrder();
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}
		if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
			$order->addStatusToHistory($order->getStatus(), $errorMsg);
			$order->cancel();
			$order->save();
		}
		$this->loadLayout();
		$this->renderLayout();
		rm_session_checkout()->unsLastRealOrderId();
	}
}