<?php
namespace Df\C1\Cml2\Import\Data\Entity;
use Df\C1\Cml2\Import\Data\Collection\Order\Items;
use Df_Sales_Model_Order as O;
class Order extends \Df\C1\Cml2\Import\Data\Entity {
	/** @return string */
	public function getIncrementId() {return $this->leafSne('Номер');}

	/** @return Items */
	public function getItems() {return dfc($this, function() {return
		Items::i($this->e(), $this)
	;});}
	
	/** @return O|null */
	public function getOrder() {return dfc($this, function() {return
		O::ldi($this->getIncrementId(), $throwOnError = false)
	;});}
}