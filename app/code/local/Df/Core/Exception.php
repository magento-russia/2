<?php
use \Exception as E;
use Df\Qa\Message\Failure\Exception as QE;
class Df_Core_Exception extends E implements \ArrayAccess {
	/**
	 * Обратите внимание, что PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя:
	 * http://3v4l.org/qQdJ3
	 * @param mixed ...$args
	 */
	public function __construct(...$args) {
		/** @var string|E|array(string => mixed)|null $arg0 */
		$arg0 = dfa($args, 0);
		/** @var E|null $prev */
		$prev = null;
		/** @var string|null $message */
		$message = null;
		// 2015-10-10
		if (is_array($arg0)) {
			$this->_data = $arg0;
		}
		else if (is_string($arg0)) {
			$message = $arg0;
		}
		else if ($arg0 instanceof E) {
			$prev = $arg0;
		}
		/** @var int|string|E|null $arg1 */
		$arg1 = dfa($args, 1);
		if (!is_null($arg1)) {
			if ($arg1 instanceof E) {
				$prev = $arg1;
			}
			else if (is_int($prev)) {
				$this->_stackLevelsCountToSkip = $arg1;
			}
			else if (is_string($arg1)) {
				$this->comment((string)$arg1);
			}
		}
		if (is_null($message)) {
			$message = $prev ? df_ets($prev) : 'No message';
		}
		parent::__construct($message, $prev);
	}

	/**
	 * @used-by __construct()
	 * @used-by Df_Shipping_Collector::call()
	 * @used-by Df_Core_Validator::resolveForProperty()
	 * @param mixed ...$args
	 * @return void
	 */
	public function comment(...$args) {$this->_comments[]= df_format($args);}

	/**
	 * @param mixed ...$args
	 * @return void
	 */
	public function commentPrepend(...$args) {array_unshift($this->_comments, df_format($args));}

	/**
	 * @used-by \Df\Qa\Message\Failure\Exception::preface()
	 * @return string[]
	 */
	public function comments() {return $this->_comments;}

	/**
	 * @used-by \Df\Qa\Message\Failure\Exception::stackLevel()
	 * @return int
	 */
	public function getStackLevelsCountToSkip() {return $this->_stackLevelsCountToSkip;}

	/**
	 * К сожалению, не можем перекрыть @see \Exception::getTraceAsString(),
	 * потому что этот метод — финальный
	 * @return string
	 */
	public function getTraceAsText() {return QE::i([QE::P__EXCEPTION => $this])->traceS();}

	/**
	 * 2016-07-31
	 * @used-by \Df\Qa\Message\Failure\Exception::main()
	 * @return bool
	 */
	public function isMessageHtml() {return $this->_messageIsHtml;}

	/**
	 * 2016-07-31
	 * @used-by df_error_html()
	 * @return void
	 */
	public function markMessageAsHtml() {$this->_messageIsHtml = true;}

	/**
	 * Стандартный метод @see \Exception::getMessage() объявлен как final.
	 * Чтобы метод для получения диагностического сообщения
	 * можно было переопределять — добавляем свой.
	 *
	 * 2015-02-22
	 * Конечно, наша архитектура обладает тем недостатком,
	 * что пользователи нашего класса и его потомков должны для извлечения диагностического сообщения
	 * вместо стандартного интерфейса @see \Exception::getMessage()
	 * использовать функцию @see df_ets()
	 *
	 * Однако неочевидно, как обойти этот недостаток.
	 * В частности, способ, когда диагностическое сообщение формируется прямо в конструкторе
	 * и передается первым параметром родительскому конструктору @see \Exception::__construct()
	 * не всегда подходит, потому что полный текст диагностического сообщения
	 * не всегда известен в момент вызова конструктора @see __construct().
	 * Пример, когда неизвестен: @see \Df\Core\Exception_Batch::message()
	 * (тот класс работает как контеёнер для других исключительных ситуаций,
	 * и полный текст диагностического сообщения
	 * получается объединением текстов элементом контейнера,
	 * которые добавляются динамически, а не сразу в конструкторе).
	 * По этой причине данный метод @see message(), несмотря на его некую громоздкость,
	 * нам действительно нужен.
	 * @used-by df_ets()
	 * @return string
	 */
	public function message() {return $this->getMessage();}

	/**
	 * A message for a buyer.
	 * @return string
	 */
	public function messageForCustomer() {return $this->message();}

	/**
	 * A message for a developer.
	 * @return string
	 */
	public function messageForDeveloper() {return $this->message();}

	/**
	 * 2016-08-19
	 * @used-by \Df\Qa\Message\Failure\Exception::main()
	 * @return string
	 */
	public function messageForLog() {return $this->messageForDeveloper();}

	/**
	 * @return bool
	 */
	public function needNotifyAdmin() {return true;}

	/**
	 * @return bool
	 */
	public function needNotifyDeveloper() {return true;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset) {return isset($this->_data[$offset]);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {return dfa($this->_data, $offset);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value) {$this->_data[$offset] = $value;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset) {unset($this->_data[$offset]);}

	/**
	 * 2015-11-27
	 * Мы не можем перекрыть метод @see \Exception::getMessage(), потому что он финальный.
	 * С другой стороны, наш метод @see \Df\Core\Exception::message()
	 * не будет понят стандартной средой,
	 * и мы в стандартной среде не будем иметь диагностического сообщения вовсе.
	 * Поэтому если мы сами не в состоянии обработать исключительную ситуацию,
	 * то вызываем метод @see \Df\Core\Exception::standard().
	 * Этот метод конвертирует исключительную ситуацию в стандартную,
	 * и стандартная среда её успешно обработает.
	 * @return \Exception
	 */
	public function standard() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new \Exception($this->message(), 0, $this);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Цель этого метода — предоставить потомкам возможность
	 * указывать тип предыдущей исключительной ситуации в комментарии PHPDoc для потомка.
	 * Метод @uses \Exception::getPrevious() объявлен как final,
	 * поэтому потомки не могут в комментариях PHPDoc указывать его тип: IntelliJ IDEA ругается.
	 * 2016-08-19
	 * @return E
	 */
	protected function prev() {return $this->getPrevious();}

	/**
	 * @used-by comments()
	 * @var string[]
	 */
	private $_comments = [];

	/**
	 * 2015-10-10
	 * @var array(string => mixed)
	 */
	private $_data = [];

	/**
	 * 2016-07-31
	 * @used-by \Df\Core\Exception::isMessageHtml()
	 * @used-by \Df\Core\Exception::markMessageAsHtml()
	 * @var bool
	 */
	private $_messageIsHtml = false;

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
	 * @used-by \Df\Qa\Message\Failure\Exception::e()
	 * @used-by Df_Shipping_Collector::call()
	 * @param \Exception $e
	 * @return $this
	 */
	public static function wrap(E $e) {return $e instanceof self ? $e : new self($e);}
}