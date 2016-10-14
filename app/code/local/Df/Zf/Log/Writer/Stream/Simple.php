<?php
/**
 * Этот класс перекрывает системный класс @see Zend_Log_Writer_Stream
 * в файле Df/Zf/etc/config.xml следующим образом:
	<global>
		<log>
			<core>
				<writer_model>Df_Zf_Log_Writer_Stream_Simple</writer_model>
			</core>
		</log>
	</global>
 */
class Df_Zf_Log_Writer_Stream_Simple extends Zend_Log_Writer_Stream {
	/**
	 * @override
	 * @param Zend_Log_Formatter_Interface $formatter
	 * @return Df_Zf_Log_Writer_Stream_Simple
	 */
	public function setFormatter(Zend_Log_Formatter_Interface $formatter) {
		if ($formatter instanceof Zend_Log_Formatter_Simple) {
			$formatter = new Df_Zf_Log_Formatter_Simple();
		}
		parent::setFormatter($formatter);
		return $this;
	}
}