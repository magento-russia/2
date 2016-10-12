<?php
/**
 * @method Df_Client_Model_Resource_DelayedMessage getResource()
 */
class Df_Client_Model_DelayedMessage extends Df_Core_Model {
	/** @return string */
	public function getBody() {
		return $this->cfg(self::P__BODY);
	}

	/** @return string */
	public function getClassName() {
		return $this->cfg(self::P__CLASS_NAME);
	}

	/** @return string */
	public function getCreationTime() {
		return $this->cfg(self::P__CREATION_TIME);
	}

	/** @return Zend_Date */
	public function getCreationTimeAsDateTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->date()->fromDb($this->getCreationTime());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLastRetryTime() {return $this->cfg(self::P__LAST_RETRY_TIME);}

	/** @return Zend_Date */
	public function getLastRetryTimeAsDateTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->date()->fromDb($this->getLastRetryTime());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Client_Model_Message_Request */
	public function getMessage() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $className */
			$className = Df_Core_Model_RemoteControl_Coder::s()->decodeClassName($this->getClassName());
			df_assert_string($className);
			/** @var array $messageData */
			$messageData = Df_Core_Model_RemoteControl_Coder::s()->decode($this->getBody());
			df_assert_array($messageData);
			/** @var Df_Client_Model_Message_Request $result */
			$result = df_model($className, $messageData);
			df_assert($result instanceof Df_Client_Model_Message_Request);
			$result->setDelayedMessage($this);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getNumRetries() {return $this->cfg(self::P__NUM_RETRIES);}

	/** @return Df_Client_Model_DelayedMessage */
	public function updateNumRetries() {
		/** @var int $numRetries */
		$numRetries = max(0, is_null($this->getId()) ? self::MAX_RETRIES : $this->getNumRetries() - 1);
		$this->setData(self::P__NUM_RETRIES, $numRetries);
		if (
				(0 >= $this->getNumRetries())
			&&
				(
						self::MAX_FAILURED_DAYS
					<=
						df()->date()->getNumberOfDaysBetweenTwoDates(
							Zend_Date::now()
							,$this->getCreationTimeAsDateTime()
						)
				)
		) {
			df_error('Ваш сервер настроен неправильно. Обратитесь к специалисту');
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _beforeSave() {
		$this->initTime();
		parent::_beforeSave();
	}

	/** @return Df_Client_Model_DelayedMessage */
	private function initTime() {
		/** @var string $timeAsMySqlString */
		$timeAsMySqlString = df()->date()->toDbNow();
		if (is_null($this->_getData(self::P__CREATION_TIME))) {
			$this->setData(self::P__CREATION_TIME, $timeAsMySqlString);
		}
		$this->setData(self::P__LAST_RETRY_TIME, $timeAsMySqlString);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Client_Model_Resource_DelayedMessage::mf());
		$this->_prop(self::P__NUM_RETRIES, self::V_NAT0);
		$this->initTime();
	}
	const _CLASS = __CLASS__;
	const MAX_FAILURED_DAYS = 3;
	const MAX_RETRIES = 10;
	const P__BODY = 'body';
	const P__CLASS_NAME = 'class_name';
	const P__CREATION_TIME = 'creation_time';
	const P__LAST_RETRY_TIME = 'last_retry_time';
	const P__MESSAGE_ID = 'message_id';
	const P__NUM_RETRIES = 'num_retries';

	/** @return Df_Client_Model_Resource_DelayedMessage_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Client_Model_DelayedMessage
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Client_Model_DelayedMessage
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Client_Model_Resource_DelayedMessage_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Client_Model_DelayedMessage */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}