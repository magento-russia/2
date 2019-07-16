<?php
class Df_Client_Model_Request extends Df_Core_Model_RemoteControl_Request {
	/**
	 * @override
	 * @return Df_Core_Model_RemoteControl_Message_Response
	 */
	public function send() {
		/** @var Df_Core_Model_RemoteControl_Message_Response $result */
		$result = null;
		try {
			$result = parent::send();
			// Удаляем сообщение из базы данных, ибо оно уже отослано.
			$this->getMessageRequest()->deleteDelayedMessage();
		}
		catch(Exception $exception) {
			$result = $this->handleException($exception);
			df_assert($result instanceof Df_Core_Model_RemoteControl_Message_Response_GenericFailure);
		}
		return $result;
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	protected function createUri() {
		/** @var Zend_Uri_Http $result */
		$result = Zend_Uri::factory('http');
		if (false && df_is_it_my_local_pc()) {
			$result->setHost('localhost.com');
			$result->setPort(811);
		}
		else {
			$result->setHost('server.magento-forum.ru');
		}
		$result->setPath('/df-server/');
		return $result;
	}

	/**
	 * @override
	 * @return Df_Client_Model_Message_Request
	 */
	protected function getMessageRequest() {return parent::getMessageRequest();}

	/**
	 * @param Exception $exception
	 * @return Df_Core_Model_RemoteControl_Message_Response
	 */
	protected function handleException(Exception $exception) {
		/** @var Df_Core_Model_RemoteControl_Message_Response $result */
		$result = Df_Core_Model_RemoteControl_Message_Response_GenericFailure::i(rm_ets($exception));
		/**
		 * Если мы не смогли соединиться с сервером Российской сборки Magento,
		 * то нам нельзя падать, потому что честные клиенты сборки не виноваты
		 * в неработоспособности сервера.
		 *
		 * С другой стороны, сообщение для сервера Российской сборки Magento не должно пропадать,
		 * потому что нечестные клиенты сборки могут просто на своём сервере магазина
		 * назначить домену сервера Российской сборки Magento посторонний адрес IP
		 * (чтобы Российская сборка Magento не могла связаться с сервером).
		 *
		 * Поэтому мы должны отложить отправку сообщения на будущее,
		 * а пока записать его в базу данных.
		 *
		 * Более того, мы должны ограничить число попыток отправки сообщения,
		 * и превышению этого числа будет означать, что нечестный клиент
		 * подменил домен сервера Российской сборки Magento.
		 *
		 * Более того, не разумней ли использовать для хранения неотправленных сообщений
		 * не таблицу базы данных (она ведь будет бросаться владельцам магазинов в глаза),
		 * а файл?
		 * Причём, чтобы и файл не бросался в глаза,
		 * можно хранить данные посредством стандартной системы кэширования.
		 * При этом задать для нашего индивидуального кэша неограниченный срок хранения.
		 * Но ведь кэш может быть уничтожен? Не подходит. Лучше БД.
		 */
		Df_Qa_Model_Message_Failure_Exception::i(array(
			Df_Qa_Model_Message_Failure_Exception::P__EXCEPTION => $exception
			,Df_Qa_Model_Message_Failure_Exception::P__NEED_LOG_TO_FILE => df_is_it_my_local_pc()
			,Df_Qa_Model_Message_Failure_Exception::P__NEED_NOTIFY_DEVELOPER => true
		))->log();
		$this->getMessageRequest()->saveDelayedMessage();
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MESSAGE_REQUEST, Df_Client_Model_Message_Request::_CLASS);
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Client_Model_Request
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

	/**
	 * @param Df_Client_Model_Message_Request $messageRequest
	 * @return Df_Core_Model_RemoteControl_Message_Response
	 */
	public static function sendStatic(Df_Client_Model_Message_Request $messageRequest) {
		return self::i(array(self::P__MESSAGE_REQUEST => $messageRequest))->send();
	}
}