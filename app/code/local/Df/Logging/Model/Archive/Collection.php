<?php
class Df_Logging_Model_Archive_Collection extends Varien_Data_Collection_Filesystem {
	/**
	 * Обратите внимание, что родительский класс Varien_Data_Collection_Filesystem
	 * не является потомком класса Varien_Object,
	 * поэтому у нашего класса нет метода _construct,
	 * и мы перекрываем именно конструктор
	 * @override
	 */
	public function __construct() {
		parent::__construct();
		/** @var string $basePath */
		$basePath = Df_Logging_Model_Archive::i()->getBasePath();
		$file = new Varien_Io_File();
		$file->setAllowCreateFolders(true);
		$file->createDestinationDir($basePath);
		$this->addTargetDir($basePath);
	}

	/**
	 * Custom callback method for 'lteq' fancy filter
	 * @param string $field
	 * @param mixed $filterValue
	 * @param array $row
	 * @return bool
	 * @see addFieldToFilter()
	 * @see addCallbackFilter()
	 */
	public function filterCallbackIsLessThan($field, $filterValue, $row) {
		$rowValue = $row[$field];
		if ($field == 'time') {
			$rowValue	= $row['timestamp'];
		}
		return $rowValue < $filterValue;
	}

	/**
	 * Custom callback method for 'moreq' fancy filter
	 *
	 * @param string $field
	 * @param mixed $filterValue
	 * @param array $row
	 * @return bool
	 * @see addFieldToFilter()
	 * @see addCallbackFilter()
	 */
	public function filterCallbackIsMoreThan($field, $filterValue, $row) {
		$rowValue = $row[$field];
		if ($field == 'time') {
			$rowValue	= $row['timestamp'];
		}
		return $rowValue > $filterValue;
	}

	/**
	 * Row generator
	 * Add 'time' column as Zend_Date object
	 * Add 'timestamp' column as unix timestamp - used in date filter
	 * @param string $filename
	 * @return array
	 */
	protected function _generateRow($filename) {
		$row = parent::_generateRow($filename);
		$date = new Zend_Date(str_replace('.csv', '', $row['basename']), 'yyyyMMddHH', df_locale());
		$row['time'] = $date;
		/**
		 * Used in date filter, becouse $date contains hours
		 */
		$dateWithoutHours = new Zend_Date(
			str_replace('.csv', '', $row['basename']), 'yyyyMMdd', df_locale()
		);
		$row['timestamp'] = df_dts($dateWithoutHours, 'yyyy-MM-dd');
		return $row;
	}

	/** @var string */
	protected $_allowedFilesMask = '/^[a-z0-9\.\-\_]+\.csv$/i';

	/** @return Df_Logging_Model_Archive_Collection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}