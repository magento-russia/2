<?php
/**
 * @method Df_Checkout_Model_Event_CheckoutTypeOnepage_SaveOrderAfter getEvent()
 */
class Df_Checkout_Model_Handler_SendGeneratedPasswordToTheCustomer extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
				df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce()
			&&
				$this->getGeneratedPassword()
		) {
 			$this->getMailer()->send();
			// Важно!
			// Удаляем пароль из сессии после отсылки,
			// чтобы потом система не пыталась создавать клиенту пароль повторно.
			rm_session_customer()->unsetData(Df_Customer_Const_Session::GENERATED_PASSWORD);
		}
	}

	/** @return Df_Core_Model_Email_Template_Mailer */
	private function getMailer() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Email_Template_Mailer $result */
			$result = Df_Core_Model_Email_Template_Mailer::i();
			$result->addEmailInfo($this->getMailInfo());
			$result->setSender($this->getMailSender());
			$result->setTemplateId($this->getMailTemplateId());
			$result->setTemplateParams(array(
				'password' => $this->getGeneratedPassword()
				,'email' => $this->getEvent()->getOrder()->getCustomerEmail()
				,'name' => $this->getEvent()->getOrder()->getCustomerName()
			));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Email_Info */
	private function getMailInfo() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Email_Info::i();
			$this->{__METHOD__}
				->addTo(
					$this->getEvent()->getOrder()->getCustomerEmail()
					,$this->getEvent()->getOrder()->getCustomerName()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getMailSender() {
		return Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $this->getStore());
	}

	/** @return string */
	private function getMailTemplateId() {
		return Mage::getStoreConfig('df_checkout/email/generated_password', $this->getStore());
	}

	/** @return Mage_Core_Model_Store */
	private function getStore() {return $this->getEvent()->getOrder()->getStore();}

	/** @return string */
	private function getGeneratedPassword() {
		return df_string(rm_session_customer()->getData(Df_Customer_Const_Session::GENERATED_PASSWORD));
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Checkout_Model_Event_CheckoutTypeOnepage_SaveOrderAfter::_CLASS;
	}

	const _CLASS = __CLASS__;
}