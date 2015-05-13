<?php
class Df_Kkb_Model_RequestDocument_OrderItems extends Df_Core_Model_SimpleXml_Generator_Document {
	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {return array('item' => $this->getDocumentData_Items());}

	/**
	 * @overide
	 * @return string
	 */
	protected function getTagName() {return 'document';}

	/** @return bool */
	protected function needDecodeEntities() {return true;}

	/**
	 * @overide
	 * @return bool
	 */
	protected function needRemoveLineBreaks() {return true;}

	/** @return bool */
	protected function needSkipXmlHeader() {return true;}

	/**
	 * @param Mage_Sales_Model_Order_Item $item
	 * @return string
	 */
	private function getAmount(Mage_Sales_Model_Order_Item $item) {
		/**
		 * В документации о формате суммы платежа ничего не сказано.
		 *
		 * В примере paysystem_PHP/paysys/kkb.utils.php
		 * в комментации к функции @see process_request()
		 * явно написано, что сумма платежа должна быть целым числом (а не дробным).
		 *
		 * Однако практика показала, что платёжный шлюз Казкоммерцбанка
		 * полне допускает дробные размеры платежей.
		 */
		return $this->getServiceConfig()->getOrderItemAmountInServiceCurrency($item)->getAsString();
	}
	
	/** @return array(array(string => string|array(string => int|float))) */
	private function getDocumentData_Items() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string|array(string => int|string))) $result  */
			$result = array();
			/** @var int $itemOrdering */
			$itemOrdering = 0;
			foreach ($this->getOrderItems() as $item) {
				/** @var Mage_Sales_Model_Order_Item $item */
				$result[]=
					$this->getItemElementData(
						++$itemOrdering
						, $item->getName()
						, rm_nat0($item->getQtyOrdered())
						, $this->getAmount($item)
					)
				;
			}
			if (0.0 < $this->getOrder()->getShippingAmount()) {
				$result[]=
					$this->getItemElementData(
						++$itemOrdering
						, rm_sprintf(
							'Доставка: %s', df_trim($this->getOrder()->getShippingDescription(), ',')
						)
						, 1
						/**
						 * В документации о формате суммы платежа ничего не сказано.
						 * В примере paysystem_PHP/paysys/kkb.utils.php
						 * в комментации к функции @see process_request()
						 * явно написано, что сумма платежа должна быть целым числом (а не дробным).
						 * Однако практика показала, что платёжный шлюз Казкоммерцбанка
						 * полне допускает дробные размеры платежей.
						 */
						, $this->getServiceConfig()->geShippingAmountInServiceCurrency($this->getOrder())
							->getAsString()
					)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int $ordering
	 * @param string $name
	 * @param int $quantity
	 * @param string $amount
	 * @return array(string => array(string => int|string))
	 */
	private function getItemElementData($ordering, $name, $quantity, $amount) {
		df_param_integer($ordering, 0);
		df_param_string_not_empty($name, 1);
		df_param_integer($quantity, 2);
		df_param_string_not_empty($amount, 3);
		return array(
			Df_Varien_Simplexml_Element::KEY__ATTRIBUTES =>
				array(
					'number' => $ordering
					,'name' => $name
					,'quantity' => $quantity
					,'amount' => $amount
				)
		);
	}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {return $this->getRequestPayment()->getOrder();}
	
	/** @return Mage_Sales_Model_Resource_Order_Item_Collection */
	private function getOrderItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getOrder()->getItemsCollection(array(), true);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Kkb_Model_Request_Payment */
	private function getRequestPayment() {return $this->cfg(self::P__REQUEST_PAYMENT);}

	/** @return Df_Kkb_Model_Config_Area_Service */
	private function getServiceConfig() {return $this->getRequestPayment()->getServiceConfig();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REQUEST_PAYMENT, Df_Kkb_Model_Request_Payment::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__REQUEST_PAYMENT = 'request_payment';
	/**
	 * @static
	 * @param Df_Kkb_Model_Request_Payment $requestPayment
	 * @return Df_Kkb_Model_RequestDocument_OrderItems
	 */
	public static function i(Df_Kkb_Model_Request_Payment $requestPayment) {
		return new self(array(self::P__REQUEST_PAYMENT => $requestPayment));
	}
}