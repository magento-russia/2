<?php
class Df_Autolux_Model_Method_ToPointOfIssue extends Df_Autolux_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'to-point-of-issue';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needDeliverToHome() {return false;}
}