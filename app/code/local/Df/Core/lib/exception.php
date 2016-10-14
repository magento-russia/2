<?php
use Df_Core_Exception as DFE;
use Df_Qa_Message_Failure_Exception as QE;
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
 * 2016-07-31
 * @param E $e
 * @return void
 */
function df_log_exception(E $e) {
	QE::i([QE::P__EXCEPTION => $e, QE::P__SHOW_CODE_CONTEXT => true])->log();
}