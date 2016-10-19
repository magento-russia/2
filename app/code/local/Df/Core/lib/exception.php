<?php
use \Df\Core\Exception as DFE;
use Df\Qa\Message\Failure\Exception as QE;
use Exception as E;
/**
 * 2016-07-18
 * @param E $e
 * @return E
 */
function df_ef(E $e) {while ($e->getPrevious()) {$e = $e->getPrevious();} return $e;}

/**
 * @param E|string $e
 * @return string
 */
function df_ets($e) {
	return !$e instanceof E ? $e : ($e instanceof DFE ? $e->message() : $e->getMessage());
}

/**
 * 2016-07-31
 * @param E $e
 * @return DFE
 */
function df_ewrap($e) {return DFE::wrap($e);}

/**
 * К сожалению, не можем перекрыть Exception::getTraceAsString(),
 * потому что этот метод — финальный
 *
 * @param E $exception
 * @param bool $showCodeContext [optional]
 * @return string
 */
function df_exception_get_trace(E $exception, $showCodeContext = false) {
	return QE::i([
		QE::P__EXCEPTION => $exception
		,QE::P__SHOW_CODE_CONTEXT => $showCodeContext
	])->traceS();
}

/**
 * @param Exception|Mage_Core_Exception|\Df\Core\Exception $exception
 * @return void
 */
function df_exception_to_session(Exception $exception) {
	/** @var string $message */
	$message = df_t()->nl2br(df_xml_output_html(df_ets($exception)));
	/** @var bool $isMagentoCoreException */
	$isMagentoCoreException = $exception instanceof Mage_Core_Exception;
	/** @var bool $isRmException */
	$isRmException = $exception instanceof \Df\Core\Exception;
	/** @var bool $needNotifyDeveloper */
	$needNotifyDeveloper = $isRmException && $exception->needNotifyDeveloper();
	/** @var bool $needShowStackTrace */
	$needShowStackTrace = $needNotifyDeveloper && (df_is_admin() || df_my_local());
	if ($message) {
		df_session()->addError($message);
	}
	else if ($isMagentoCoreException && $exception->getMessages()) {
		foreach ($exception->getMessages() as $subMessage) {
			/** @var Mage_Core_Model_Message_Abstract $subMessage */
			df_session()->addError($subMessage->getText());
		}
	}
	else if (!$needShowStackTrace) {
		// Надо хоть какое-то сообщение показать
		df_session()->addError('Произошёл внутренний сбой.');
	}
	if ($needShowStackTrace) {
		df_session()->addError(df_t()->nl2br(df_exception_get_trace($exception)));
	}
	if ($needNotifyDeveloper) {
		df_notify_exception($exception);
	}
}

/**
 * 2016-07-31
 * @param E $e
 * @return void
 */
function df_log_exception(E $e) {
	QE::i([QE::P__EXCEPTION => $e, QE::P__SHOW_CODE_CONTEXT => true])->log();
}

/**
 * Эта функция используется, как правило, при отключенном режиме разработчика.
 * @see mageCoreErrorHandler():
		if (Mage::getIsDeveloperMode()) {
			throw new Exception($errorMessage);
		}
 		else {
			Mage::log($errorMessage, Zend_Log::ERR);
		}
 * @param bool $isOperationSuccessfull [optional]
 * @throws \Df\Core\Exception
 */
function df_throw_last_error($isOperationSuccessfull = false) {
	if (!$isOperationSuccessfull) {
		\Df\Qa\Message\Failure\Error::throwLast();
	}
}

/**
 * @param string|string[]|Exception|null $message [optional]
 * @return void
 */
function df_warning($message = null) {
	if ($message instanceof Exception) {
		$message = df_ets($message);
	}
	else {
		if (is_array($message)) {
			$message = implode("\n\n", $message);
		}
		else {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = df_format($arguments);
		}
	}
	df_notify_admin($message, $doLog = true);
	df_notify_me($message, $doLog = false);
	if (df_is_admin()) {
		df_session()->addWarning($message);
	}
}