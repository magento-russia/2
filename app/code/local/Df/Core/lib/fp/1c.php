<?php
/**
 * @param string|mixed[] $arguments
 * @return void
 */
function rm_1c_log($arguments) {
	// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	df_h()->_1c()->log(rm_sprintf($arguments));
}


 