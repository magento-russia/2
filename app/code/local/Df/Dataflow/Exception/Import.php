<?php
class Df_Dataflow_Exception_Import extends Df_Dataflow_Exception {
	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf(
				"%s\nИмпортируемые данные:\n%s"
				, parent::getMessageRm()
				, df_tab_multiline(df_print_params($this->getRow()->getAsArray()))
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Dataflow_Model_Import_Abstract_Row */
	public function getRow() {return $this->_row;}

	/**
	 * Обратите внимание, что данный метод нам действительно нужен,
	 * и мы не можем инициализировать $row в конструкторе:
	 * @used-by Df_Dataflow_Model_Import_Abstract_Row::error()
	 * @param Df_Dataflow_Model_Import_Abstract_Row $row
	 * @return void
	 */
	public function setRow(Df_Dataflow_Model_Import_Abstract_Row $row) {$this->_row = $row;}

	/** @var Df_Dataflow_Model_Import_Abstract_Row */
	private $_row;
}