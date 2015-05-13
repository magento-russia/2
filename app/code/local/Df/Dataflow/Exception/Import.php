<?php
class Df_Dataflow_Exception_Import extends Df_Dataflow_Exception {
	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf(
				"%s\r\nИмпортируемые данные:\r\n%s"
				, parent::getMessageRm()
				, df_tab_multiline(rm_print_params($this->getRow()->getAsArray()))
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Dataflow_Model_Import_Abstract_Row */
	public function getRow() {return $this->cfg(self::$P__ROW);}

	/**
	 * @param Df_Dataflow_Model_Import_Abstract_Row $row
	 * @return void
	 */
	public function setRow(Df_Dataflow_Model_Import_Abstract_Row $row) {
		$this->setData(self::$P__ROW, $row);
	}

	/** @var string */
	private static $P__ROW = 'row';
}