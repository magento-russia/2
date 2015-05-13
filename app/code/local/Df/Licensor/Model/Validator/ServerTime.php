<?php
class Df_Licensor_Model_Validator_ServerTime extends Df_Core_Model_Abstract {
	/** @return bool */
	public function isValid() {
		if (!isset($this->{__METHOD__})) {
			try {
				/** @var bool $result */
				$result = df_is_it_my_local_pc();
				if (false === $result) {
					/** @var Zend_Date $currentTimeServer */
					$currentTimeServer = Zend_Date::now();
					/** @var Zend_Date $currentTimeCorrect */
					$currentTimeCorrect = $this->getCorrectCurrentTime();
					/** @var int $intervalInDays */
					$intervalInDays =
						df()->date()->getNumberOfDaysBetweenTwoDates($currentTimeServer, $currentTimeCorrect)
					;
					$result = self::MAX_CORRECT_INTERVAL_IN_DAYS > $intervalInDays;
				}
				if (false === $result) {
					/** @var string $message */
					$message =
						rm_sprintf(
							'Часы на сервере магазина показывают неверное время: «%s».'
							.'<br/>'
							.'Модули Российской сборки Magento будут отключены, '
							.'пока Вы не исправите время на сервере.'
							,Zend_Date::now()->toString(Zend_Date::DATETIME_MEDIUM, null, 'ru_RU')
						)
					;
					df_assert_string($message);
					df_notify_me(
						implode(
							Df_Core_Const::T_NEW_LINE
							,array(
								$message
								,rm_sprintf(
									'Ожидаемое время: %s'
									,$currentTimeCorrect->toString(Zend_Date::DATETIME_MEDIUM, null, 'ru_RU')
								)
								,rm_sprintf('Разница в днях: %d', $intervalInDays)
							)
						)
						,$doLog = false
					);
					rm_session()->addError($message);
				}
			}
			catch(Exception $e) {
				/**
				 * Падать с фатальным сбоем здесь нельзя:
				 * время может быть неверным по причине неправильной настройки часового пояса
				 * в административной части Magento.
				 * Если мы упадём — у администратора не будет возможности исправиться.
				 */
				df_notify_exception($e);
				$result = false;
				rm_exception_to_session($e);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @throws Exception
	 * @return Zend_Date
	 */
	private function getCorrectCurrentTime() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Date $result */
			$result = null;
			/** @var Exception $lastException */
			$lastException = null;
			foreach ($this->getServers() as $server) {
				/** @var Df_Licensor_Model_Server_Time $server */
				try {
					$result = $server->getTime();
				}
				catch(Exception $e) {
					// Просто переходим к следующему серверу
					$lastException = $e;
				}
				if (!$result) {
					// Получили время с текущего сервера,
					// поэтому другие сервера нам теперь не нужны
					break;
				}
			}
			if (!$result) {
				df_assert($lastException instanceof Exception);
				throw $lastException;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Licensor_Model_Server_Time[] */
	private function getServers() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				Df_Licensor_Model_Server_Time_MagentoProRu::i()
				,Df_Licensor_Model_Server_Time_TimeApiOrg::i()
			);
		}
		return $this->{__METHOD__};
	}

	const MAX_CORRECT_INTERVAL_IN_DAYS = 4;

	/** @return Df_Licensor_Model_Validator_ServerTime */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}