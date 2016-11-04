<?php
namespace Df\C1\Cml2\Import\Data\Entity;
class Order extends \Df\C1\Cml2\Import\Data\Entity {
	/** @return string */
	public function getIncrementId() {return $this->leafSne('Номер');}

	/** @return \Df\C1\Cml2\Import\Data\Collection\Order\Items */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\C1\Cml2\Import\Data\Collection\Order\Items::i($this->e(), $this);
		}
		return $this->{__METHOD__};
	}
	
	/** @return \Df_Sales_Model_Order|null */
	public function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				\Df_Sales_Model_Order::ldi($this->getIncrementId(), $throwOnError = false)
			);
		}
		return df_n_get($this->{__METHOD__});
	}
}