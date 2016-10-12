<?php
class Df_Core_Model_RemoteControl_MessageSerializer_Http extends Df_Core_Model {
	/**
	 * @static
	 * @param Mage_Core_Controller_Request_Http $httpRequest
	 * @return Df_Core_Model_RemoteControl_Message_Request
	 */
	public static function restoreMessageRequest(Mage_Core_Controller_Request_Http $httpRequest) {
		/** @var string $classNameEncoded */
		$classNameEncoded = $httpRequest->getHeader(self::HEADER__CLASS_NAME);
		df_assert_string_not_empty($classNameEncoded);
		/** @var string $className */
		$className = Df_Core_Model_RemoteControl_Coder::s()->decodeClassName($classNameEncoded);
		df_assert_string_not_empty($className);
		/** @var string $body */
		$body = $httpRequest->getRawBody();
		df_assert_string_not_empty($body);
		/** @var array $messageData */
		$messageData = Df_Core_Model_RemoteControl_Coder::s()->decode($body);
		df_assert_array($messageData);
		/** @var Df_Core_Model_RemoteControl_Message_Request $result */
		$result = df_model($className, $messageData);
		df_assert($result instanceof Df_Core_Model_RemoteControl_Message_Request);
		return $result;
	}

	/**
	 * @static
	 * @param Zend_Http_Response $httpResponse
	 * @return Df_Core_Model_RemoteControl_Message_Response
	 */
	public static function restoreMessageResponse(Zend_Http_Response $httpResponse) {
		/** @var string|null $contentType */
		$contentType = $httpResponse->getHeader('Content-Type');
		// Раньше тут стояла проверка:
		// if (self::$CONTENT_TYPE !== $contentType) {
		// Однако заметил, что некоторые сервера (или Zend Framework?)
		// самовольно добавляют к строке «application/octet-stream» окончание «; charset=UTF-8»,
		// возвращая тем самым «application/octet-stream; charset=UTF-8»
		// вместо «application/octet-stream».
		/**
		 * Раньше тут стояла проверка:
		 * 	if (self::$CONTENT_TYPE !== $contentType) {...}
		 *
		 * Однако ядро Magento (@see Mage_Core_Model_App::getResponse())
		 * автоматически добавляет свой заголовок «Content-Type» при конструировании объекта
		 * @see Mage_Core_Controller_Response_Http.
		 *
		 * Ранее я об этом не думал, и добавлял свой заголовок «Content-Type» методом
		 * @see Zend_Controller_Response_Abstract::setHeader()
		 * без использования 3-го параметра $replace, который удалял бы заголовок «Content-Type»,
		 * установленный в методе @see Mage_Core_Model_App::getResponse().
		 *
		 * Таким образом, у объекта @see Mage_Core_Controller_Response_Http
		 * присутствовали сразу 2 заголовка «Content-Type», и, видимо,
		 * далее происходило каким-то образом их слияние (?),
		 * что приводило к получению в данном методе
		 * заголовка «application/octet-stream; charset=UTF-8»
		 * вместо ожидаемого «application/octet-stream».
		 *
		 * Поэтому я заменил
		 * if (self::$CONTENT_TYPE !== $contentType) {...}
		 * на
		 * if (!rm_contains($contentType, self::$CONTENT_TYPE)) {...}
		 *
		 * Однако этого мало!
		 * Потому что если клиент установит старую версию Российской сборки Magento,\
		 * то там код будет прежний: if (self::$CONTENT_TYPE !== $contentType) {...}
		 *
		 * Поэтому я поправил ещё и сервер:
		 * @see Df_Core_Model_RemoteControl_MessageSerializer_Http::serializeMessageResponse()
		 */
		if (!rm_contains($contentType, self::$CONTENT_TYPE)) {
			/** @var string $diagnosticMessage */
			$diagnosticMessage =
				rm_sprintf(
					"Ответ имеет неверный тип: «%s».\r\nТребуемый тип: «%s».",
					$contentType, self::$CONTENT_TYPE
				)
			;
			if ('text/html' === $contentType) {
				if (df_is_it_my_local_pc()) {
					rm_report('rm.response.{date}--{time}.html', $httpResponse->getBody());
				}
				$diagnosticMessage .= "\r\n\r\n" . htmlspecialchars($httpResponse->getBody());
			}
			df_error_internal($diagnosticMessage);
		}
		/** @var string|null $classNameEncoded */
		$classNameEncoded = $httpResponse->getHeader(self::HEADER__CLASS_NAME);
		if (!$classNameEncoded) {
			df_error_internal(
				"В заголовках HTTP отсутствует класс ответа."
				."\r\nВсе заголовки HTTP:\r\n%s"
				,rm_print_params($httpResponse->getHeaders())
			);
		}
		df_assert_string_not_empty($classNameEncoded);
		/** @var string $className */
		$className = Df_Core_Model_RemoteControl_Coder::s()->decodeClassName($classNameEncoded);
		df_assert_string_not_empty($className);
		/** @var string $body */
		$body = $httpResponse->getBody();
		df_assert_string_not_empty($body);
		/** @var array $messageData */
		$messageData = Df_Core_Model_RemoteControl_Coder::s()->decode($body);
		df_assert_array($messageData);
		/** @var Df_Core_Model_RemoteControl_Message_Response $result */
		$result = df_model($className, $messageData);
		df_assert($result instanceof Df_Core_Model_RemoteControl_Message_Response);
		return $result;
	}

	/**
	 * @param Zend_Http_Client $httpClient
	 * @param Df_Core_Model_RemoteControl_Message_Request $message
	 * @return void
	 */
	public static function serializeMessageRequest(
		Zend_Http_Client $httpClient
		,Df_Core_Model_RemoteControl_Message_Request $message
	) {
		$httpClient
			->setHeaders(
				self::HEADER__CLASS_NAME
				,Df_Core_Model_RemoteControl_Coder::s()->encodeClassName(
					$message->getCurrentClassNameInMagentoFormat()
				)
			)
			->setRawData(
				Df_Core_Model_RemoteControl_Coder::s()->encode(
					$message->getPersistentData()
					, 'application/octet-stream'
				)
			)
		;
	}
	/**
	 * @param Mage_Core_Controller_Response_Http $httpResponse
	 * @param Df_Core_Model_RemoteControl_Message_Response $message
	 * @return void
	 */
	public static function serializeMessageResponse(
		Mage_Core_Controller_Response_Http $httpResponse
		,Df_Core_Model_RemoteControl_Message_Response $message
	) {
		rm_response_content_type($httpResponse, self::$CONTENT_TYPE);
		$httpResponse
			->setHeader(
				$name = Df_Core_Model_RemoteControl_MessageSerializer_Http::HEADER__CLASS_NAME
				,$value =
					Df_Core_Model_RemoteControl_Coder::s()->encodeClassName(
						$message->getCurrentClassNameInMagentoFormat()
					)
			)
			->setBody(Df_Core_Model_RemoteControl_Coder::s()->encode($message->getPersistentData()))
		;
	}
	/** @var string */
	private static $CONTENT_TYPE = 'application/octet-stream';
	const _CLASS = __CLASS__;
	const HEADER__CLASS_NAME = 'rm-message-class';
	const REQUEST_PARAM__BODY = 'body';
}