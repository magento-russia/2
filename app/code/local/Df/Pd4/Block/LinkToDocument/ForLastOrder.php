<?php
class Df_Pd4_Block_LinkToDocument_ForLastOrder extends Df_Pd4_Block_LinkToDocument {
	/**
	 * @override
	 * @used-by Df_Pd4_Block_LinkToDocument_ForLastOrder::getPaymentDocumentUrl()
	 * @used-by Df_Pd4_Block_LinkToDocument_ForLastOrder::needToShow()
	 * @return Df_Sales_Model_Order
	 */
	protected function order() {return rm_last_order();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/pd4/link_to_document/for_last_order.phtml';}
}