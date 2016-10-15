<?php
class Df_Payment_Exception extends \Df\Core\Exception {
	/**
	 * Если метод вернёт true, то система добавит к сообщению обрамление/пояснение
	 * из @see Df_Payment_Config_Area_Frontend::getMessageFailure()
	 * @see Df_Payment_Model_Action_Confirm::showExceptionOnCheckoutScreen()
	 * @return bool
	 */
	public function needFraming() {return true;}
}