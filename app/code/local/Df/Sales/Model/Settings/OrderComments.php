<?php
class Df_Sales_Model_Settings_OrderComments extends Df_Core_Model_Settings {
	/** @return boolean */
	public function adminOrderCreate_commentIsVisibleOnFront() {
		return $this->getYesNo('admin_order_create__comment_is_visible_on_front');
	}
	/** @return string[] */
	public function getTagsToPreserveInAdminOrderView() {
		return df_csv_parse($this->getStringNullable('tags_to_preserve_in_admin_order_view'));
	}
	/** @return boolean */
	public function preserveLineBreaksInAdminOrderView() {
		return $this->getYesNo('preserve_line_breaks_in_admin_order_view');
	}
	/** @return boolean */
	public function preserveLineBreaksInCustomerAccount() {
		return $this->getYesNo('preserve_line_breaks_in_customer_account');
	}
	/** @return boolean */
	public function preserveLineBreaksInOrderEmail() {
		return $this->getYesNo('preserve_line_breaks_in_order_email');
	}
	/** @return boolean */
	public function preserveSomeTagsInAdminOrderView() {
		return $this->getYesNo('preserve_some_tags_in_admin_order_view');
	}
	/** @return boolean */
	public function wrapInStandardFrameInOrderEmail() {
		return $this->getYesNo('wrap_in_standard_frame_in_order_email');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_sales/order_comments/';}
	/** @return Df_Sales_Model_Settings_OrderComments */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}