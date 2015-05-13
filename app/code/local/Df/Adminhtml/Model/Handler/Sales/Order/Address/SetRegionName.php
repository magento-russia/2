<?php
/**
 * @method Df_Adminhtml_Model_Event_Sales_Order_Create_Process_Data_Before getEvent()
 * @link http://magento-forum.ru/topic/2612/
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
			$order[self::REQUEST_PARAM__BILLING_ADDRESS] =
				$this->processAddress(
					df_a($order, self::REQUEST_PARAM__BILLING_ADDRESS)
				)
			;
			$order[self::REQUEST_PARAM__SHIPPING_ADDRESS] =
				$this->processAddress(
					df_a($order, self::REQUEST_PARAM__SHIPPING_ADDRESS)
				)
			;
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
		return Df_Adminhtml_Model_Event_Sales_Order_Create_Process_Data_Before::_CLASS;
	}

	/**
	 * @param array|null $address
	 * @return array|null
	 */
	private function processAddress($address) {
		/** @var array|null $result */
		$result = $address;
		/**
		 * Как ни странно, судя по отчетам $address — не всегда массив
		 * @link http://magento-forum.ru/topic/3283/
		 */
		if (is_array($address)) {
			/** @var string|null $regionName */
			$regionName = df_a($address, self::KEY__REGION_NAME);
			if (!$regionName) {
				/** @var int $regionId */
				$regionId = rm_nat0(df_a($address, self::KEY__REGION_ID));
				if (0 < $regionId) {
					$result[self::KEY__REGION_NAME] =
						Df_Directory_Model_Region::ld($regionId)->getName()
					;
				}
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	const KEY__REGION_NAME = 'region';
	const KEY__REGION_ID = 'region_id';
	const REQUEST_PARAM__BILLING_ADDRESS = 'billing_address';
	const REQUEST_PARAM__SHIPPING_ADDRESS = 'shipping_address';
}