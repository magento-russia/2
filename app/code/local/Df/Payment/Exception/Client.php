<?php
class Df_Payment_Exception_Client extends Df_Core_Exception_Client {
	/**
	 * Если метод вернёт true, то система добавит к сообщению обрамление/пояснение
	 * из @see Df_Payment_Model_Config_Area_Frontend::getMessageFailure()
	 * @see Df_Payment_Model_Action_Confirm::showExceptionOnCheckoutScreen()
	 * @return bool
	 */
	public function needFraming() {return true;}
}