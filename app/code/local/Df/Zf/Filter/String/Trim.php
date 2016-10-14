<?php
/**
 * Обратите внимание, что у нас 2 класса-фильтра для обрубки строк.
 * @see Df_Zf_Filter_String_Trim
 * @see Df_Zf_Filter_StringTrim
 * Класс @see Df_Zf_Filter_StringTrim — это всего лишь наследник-замена-заплатка
 * для стандартного класса Zend Framework @see Zend_Filter_StringTrim.
 * Класс @see Df_Zf_Filter_String_Trim делает намного больше, чем класс @see Df_Zf_Filter_StringTrim.
 * Класс @see Df_Zf_Filter_String_Trim инкапсулирует вызов @see df_trim,
 * который, в частности, автоматически добавляет в число фильтруемых символов символы «\r» и «\n».
 */
class Df_Zf_Filter_String_Trim implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param  mixed $value
	 * @throws Zend_Filter_Exception
	 * @return string
	 */
	public function filter($value) {
		/** @var string $result */
		try {
			$result = df_trim($value);
		}
		catch (Exception $e) {
			df_error(new Zend_Filter_Exception(rm_ets($e)));
		}
		return $result;
	}

	/** @return Df_Zf_Filter_String_Trim */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}