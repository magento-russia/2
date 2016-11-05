<?php
namespace Df\Kkb\RequestDocument;
use Mage_Sales_Model_Order_Item as OI;
class OrderItems extends \Df\Xml\Generator\Document {
	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {return array('item' => $this->getDocumentData_Items());}

	/**
	 * @overide
	 * @return string
	 */
	protected function tag() {return 'document';}

	/** @return bool */
	protected function needDecodeEntities() {return true;}

	/**
	 * @overide
	 * @return bool
	 */
	protected function needRemoveLineBreaks() {return true;}

	/** @return bool */
	protected function needSkipXmlHeader() {return true;}

	/** @return \Df\Kkb\Config\Area\Service */
	private function configS() {return $this->getRequestPayment()->configS();}

	/**
	 * В документации о формате суммы платежа ничего не сказано.
	 * В примере paysystem_PHP/paysys/kkb.utils.php
	 * в комментации к функции @see process_request()
	 * явно написано, что сумма платежа должна быть целым числом (а не дробным).
	 * Однако практика показала, что платёжный шлюз Казкоммерцбанка
	 * полне допускает дробные размеры платежей.
	 * @param OI $item
	 * @return string
	 */
	private function getAmount(OI $item) {return
		$this->configS()->getOrderItemAmountInServiceCurrency($item)->getAsString()
	;}
	
	/** @return array(array(string => string|array(string => int|float))) */
	private function getDocumentData_Items() {return dfc($this, function() {
		/** @var array(array(string => string|array(string => int|string))) $result  */
		$result = [];
		/** @var int $itemOrdering */
		$itemOrdering = 0;
		foreach ($this->getOrderItems() as $item) {
			/** @var OI $item */
			$result[]=
				$this->getItemElementData(
					++$itemOrdering
					, $item->getName()
					/**
					 * 2016-05-04
					 * Раньше тут использовалось @see df_nat0(),
					 * однако количество заказанного товара
					 * почему-то хранится в системе как вещественное число,
					 * что приводит к сбою
					 * «Система не смогла распознать значение «1.0000» типа «string»
					 * как целое число»
					 * http://magento-forum.ru/topic/5424/
					 *
					 * Кстати, PHPDoc тоже говорит, что метод
					 * @uses Mage_Sales_Model_Order_Item::getQtyOrdered()
					 * возвращает именно вещественное число:
					 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.4/app/code/core/Mage/Sales/Model/Order/Item.php#L74-L74
					 *
					 * Т.к. платёжный шлюз Казкоммерцбанка, видимо, удвивится вещественному числу,
					 * да на практике там всегда целое число (типа того же «1.0000»),
					 * то счёл лучшим использовать @uses round()
					 */
					, round($item->getQtyOrdered())
					, $this->getAmount($item)
				)
			;
		}
		if (0.0 < $this->order()->getShippingAmount()) {
			$result[]= $this->getItemElementData(
				++$itemOrdering
				, 'Доставка: ' . df_trim($this->order()->getShippingDescription(), ',')
				, 1
				/**
				 * В документации о формате суммы платежа ничего не сказано.
				 * В примере paysystem_PHP/paysys/kkb.utils.php
				 * в комментации к функции @see process_request()
				 * явно написано, что сумма платежа должна быть целым числом (а не дробным).
				 * Однако практика показала, что платёжный шлюз Казкоммерцбанка
				 * полне допускает дробные размеры платежей.
				 */
				, $this->configS()->geShippingAmountInServiceCurrency($this->order())->getAsString()
			);
		}
		return $result;
	});}

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
		return [\Df\Xml\X::ATTR => [
			'number' => $ordering, 'name' => $name, 'quantity' => $quantity, 'amount' => $amount
		]];
	}
	
	/** @return \Mage_Sales_Model_Resource_Order_Item_Collection */
	private function getOrderItems() {return dfc($this, function() {return
		$this->order()->getItemsCollection([], true)
	;});}
	
	/** @return \Df\Kkb\Request\Payment */
	private function getRequestPayment() {return $this->cfg(self::P__REQUEST_PAYMENT);}

	/** @return \Df_Sales_Model_Order */
	private function order() {return $this->getRequestPayment()->order();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REQUEST_PAYMENT, \Df\Kkb\Request\Payment::class);
	}

	const P__REQUEST_PAYMENT = 'request_payment';
	/**
	 * @static
	 * @param \Df\Kkb\Request\Payment $requestPayment
	 * @return \Df\Kkb\RequestDocument\OrderItems
	 */
	public static function i(\Df\Kkb\Request\Payment $requestPayment) {
		return new self([self::P__REQUEST_PAYMENT => $requestPayment]);
	}
}