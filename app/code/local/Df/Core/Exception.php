<?php
class Df_Core_Exception extends Exception {
	/**
	 * Обратите внимание, что PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя:
	 * http://3v4l.org/qQdJ3
	 * @return Df_Core_Exception
	 */
	public function __construct() {
		/** @var mixed $args */
		$args = func_get_args();
		/** @var mixed|null $arg0 */
		$arg0 = df_a($args, 0);
		/** @var string|null $message */
		if (is_string($arg0)) {
			$message = $arg0;
		}
		else if ($arg0 instanceof Exception) {
			/** @used-by wrap() */
			$this->_internal = $arg0;
			/** Благодаря этому коду @see getMessage() вернёт сообщение внутреннего объекта. */
			$message = $this->_internal->getMessage();
		}
		parent::__construct(isset($message) ? $message : null);
		/** @var mixed|null $arg1 */
		$arg1 = df_a($args, 1);
		if ($arg1) {
			is_int($arg1)
				? $this->_stackLevelsCountToSkip = $arg1
				: $this->comment($arg1)
			;
		}
	}

	/**
	 * @used-by __construct()
	 * @used-by Df_Shipping_Collector::call()
	 * @used-by Df_Core_Validator::resolveForProperty()
	 * @param string $comment
	 * @return void
	 */
	public function comment($comment) {
		$args = func_get_args();
		$this->_comments[]= rm_format($args);
	}

	/**
	 * @param string $comment
	 * @return void
	 */
	public function commentPrepend($comment) {
		$args = func_get_args();
		array_unshift($this->_comments, rm_format($args));
	}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::preface()
	 * @return string[]
	 */
	public function comments() {return $this->_comments;}

	/**
	 * Диагностическое сообщение для администратора интернет-магазина.
	 * @return string
	 */
	public function getMessageForAdmin() {return $this->getMessageRm();}

	/**
	 * Диагностическое сообщение для клиента интернет-магазина.
	 * @return string
	 */
	public function getMessageForCustomer() {return $this->getMessageRm();}

	/**
	 * Диагностическое сообщение для разработчика интернет-магазина.
	 * @return string
	 */
	public function getMessageForDeveloper() {return $this->getMessageRm();}

	/**
	 * Стандартный метод @see Exception::getMessage() объявлен как final.
	 * Чтобы метод для получения диагностического сообщения
	 * можно было переопределять — добавляем свой.
	 *
	 * 2015-02-22
	 * Конечно, наша архитектура обладает тем недостатком,
	 * что пользователи нашего класса и его потомков должны для извлечения диагностического сообщения
	 * вместо стандартного интерфейса @see Exception::getMessage()
	 * использовать функцию @see rm_ets()
	 *
	 * Однако неочевидно, как обойти этот недостаток.
	 * В частности, способ, когда диагностическое сообщение формируется прямо в конструкторе
	 * и передается первым параметром родительскому конструктору @see Exception::__construct()
	 * не всегда подходит, потому что полный текст диагностического сообщения
	 * не всегда известен в момент вызова конструктора @see __construct().
	 * Пример, когда неизвестен: @see Df_Core_Exception_Batch::getMessageRm()
	 * (тот класс работает как контеёнер для других исключительных ситуаций,
	 * и полный текст диагностического сообщения
	 * получается объединением текстов элементом контейнера,
	 * которые добавляются динамически, а не сразу в конструкторе).
	 * По этой причине данный метод @see getMessageRm(), несмотря на его некую громоздкость,
	 * нам действительно нужен.
	 * @used-by rm_ets()
	 * @return string
	 */
	public function getMessageRm() {return $this->getMessage();}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::stackLevel()
	 * @return int
	 */
	public function getStackLevelsCountToSkip() {return $this->_stackLevelsCountToSkip;}

	/**
	 * К сожалению, не можем перекрыть @see Exception::getTraceAsString(),
	 * потому что этот метод — финальный
	 * @return string
	 */
	public function getTraceAsText() {
		return Df_Qa_Message_Failure_Exception::i(array(
			Df_Qa_Message_Failure_Exception::P__EXCEPTION => $this
			,Df_Qa_Message_Failure_Exception::P__NEED_LOG_TO_FILE => false
			,Df_Qa_Message_Failure_Exception::P__NEED_NOTIFY_DEVELOPER => false
		))->traceS();
	}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::trace()
	 * @return array(array(string => string|int))
	 */
	public function getTraceRm() {
		return !$this->_internal ? parent::getTrace() : $this->_internal->getTrace();
	}

	/**
	 * @return bool
	 */
	public function needNotifyAdmin() {return true;}

	/**
	 * @return bool
	 */
	public function needNotifyDeveloper() {return true;}

	/**
	 * @used-by comments()
	 * @var string[]
	 */
	private $_comments = array();

	/**
	 * @var Exception|null
	 */
	private $_internal;

	/**
	 * Количество последних элементов стека вызовов,
	 * которые надо пропустить как несущественные
	 * при показе стека вызовов в диагностическом отчёте.
	 * Это значение становится положительным,
	 * когда исключительная ситуация возбуждается не в момент её возникновения,
	 * а в некоей вспомогательной функции-обработчике, вызываемой в сбойном участке:
	 * @see Df_Qa_Method::throwException()
	 * @var int
	 */
	private $_stackLevelsCountToSkip = 0;

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::e()
	 * @used-by Df_Shipping_Collector::call()
	 * @param Exception $e
	 * @return Df_Core_Exception
	 */
	public static function wrap(Exception $e) {return $e instanceof self ? $e : new self($e);}
}