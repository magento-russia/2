<?php
class Df_Spsr_Model_Validator_Origin
	extends Df_Shipping_Model_Config_Backend_Validator_Strategy_Origin {
	/**
	 * @override
	 * @return bool
	 */
	public function validate() {
		/** @var bool $result */
		$result = false;
		try {
			Df_Spsr_Model_Locator::i(
				array(
					Df_Spsr_Model_Locator::P__CITY => $this->getOrigin()->getCity()
					,Df_Spsr_Model_Locator::P__COUNTRY_ID => $this->getOrigin()->getCountryId()
					,Df_Spsr_Model_Locator::P__REGION_ID => $this->getOrigin()->getRegionId()
					,Df_Spsr_Model_Locator::P__REGION_NAME => $this->getOrigin()->getRegionName()
					,Df_Spsr_Model_Locator::P__REQUEST => null
					,Df_Spsr_Model_Locator::P__IS_DESTINATION => false
				)
			)->getResult();
			$result = true;
		}
		catch(Exception $e) {
			df_notify_exception($e);
			$this->getBackend()->getMessages()->addMessage(new Mage_Core_Model_Message_Error(
				rm_ets($e)
				? rm_ets($e)
				:
					"Служба доставки СПСР не может забрать груз"
					. " из указанного администратором в настройках магазина склада,"
					. " потому что не работает с этим населённым пунктом"
					. " или не понимает указанный администратором адрес."
					. "<br/>Проверьте правильность указания"
					. " <a href='http://magento-forum.ru/topic/3575/'>адреса склада магазина</a>."
					. "<br/>Если Вы считаете, что указали адрес складка магазина правильно,"
					. " и что служба доставки СПСР должна работать с этим адресом,"
					. " то напишите об этом на форуме в"
					. " <a href='http://magento-forum.ru/forum/198/'>разделе модуля «СПСР-ЭКСПРЕСС»</a>,"
					. " соблюдая"
					. " <a href='http://magento-forum.ru/topic/3263/'>регламент описания отклонений</a>"
					. " и указав диагностическое сообщение модуля «СПСР-ЭКСПРЕСС»"
					. " из <a href='http://magento-forum.ru/topic/1056/'>системного журнала</a>"
					. " и снимок экрана с данными адреса склада магазина."
			));
		}
		return $result;
	}

	const _CLASS = __CLASS__;
}