<?php
class Df_Psbank_Request_Void extends Df_Psbank_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'возврате средств покупетелю';}
	/**
	 * @override
	 * @return int
	 */
	protected function getTransactionType() {return 22;}
}


