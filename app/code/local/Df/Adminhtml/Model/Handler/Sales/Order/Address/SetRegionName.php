<?php
/**
 * @method Df_Adminhtml_Model_Event_Sales_Order_Create_Process_Data_Before getEvent()
 * http://magento-forum.ru/topic/2612/
 */
class Df_Adminhtml_Model_Handler_Sales_Order_Address_SetRegionName extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var array|null $order */
		$order = $this->getEvent()->getRequest()->getParam('order');
		/**
		 * Как показывают отчёты от клиентов,
		 * иногда переменная $order в этом месте равна null
		 */
		if (!is_null($order)) {
			$this->processAddress($order, 'billing_address');
			$this->processAddress($order, 'shipping_address');
			$this->getEvent()->getRequest()->setParam('order', $order);
			/**
			 * Приходится делать так,
			 * потому что $this->getRequest()->getPost('order')
			 * в методе Mage_Adminhtml_Sales_Order_CreateController::saveAction()
			 * обращается напрямую к $_POST
			 */
			$_POST['order'] = $order;
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Adminhtml_Model_Event_Sales_Order_Create_Process_Data_Before::_C;
	}

	/**
	 * @param array(string => mixed) $order
	 * @param string $addressName
	 * @return void
	 */
	private function processAddress(array &$order, $addressName) {
		/** @var array(string => mixed)|null $result */
		$address = dfa($order, $addressName);
		/**
		 * Как ни странно, судя по отчетам $address — не всегда массив (может быть null).
		 * http://magento-forum.ru/topic/3283/
		 */
		if (is_array($address)) {
			/** @var string|null $regionName */
			$regionName = dfa($address, 'region');
			if (!$regionName) {
				/** @var int $regionId */
				$regionId = rm_nat0(dfa($address, 'region_id'));
				if (0 < $regionId) {
					$address['region'] = Df_Directory_Model_Region::ld($regionId)->getName();
				}
			}
		}
		$order[$addressName] = $address;
	}

	/** @used-by Df_Adminhtml_Observer::adminhtml_sales_order_create_process_data_before() */
	const _C = __CLASS__;
}