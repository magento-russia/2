<?php
class Df_Logging_Block_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/grid', array('_current' => true));
	}

	/**
	 * @override
	 * @param Df_Logging_Model_Event $row
	 * @return string
	 */
	public function getRowUrl($row) {
		return $this->getUrl('*/*/details', array('event_id'=>$row->getId()));
	}

	/**
	 * @override
	 * @return Df_Logging_Block_Index_Grid
	 */
	protected function _prepareCollection() {
		$this->setCollection(Df_Logging_Model_Event::c());
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Logging_Block_Index_Grid
	 */
	protected function _prepareColumns() {
		/** @var string[] $actions */
		$actions = array();
		foreach (Df_Logging_Model_Resource_Event::s()->getAllFieldValues('action') as $action) {
			$actions[$action] = df_h()->logging()->__($action);
		}
		$this
			->addColumn(
				'time'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Time')
					,'index' => 'time'
					,'type' => 'datetime'
					,'width' => 160
				)
			)
			->addColumn(
				'event'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Action Group')
					,'index' => 'event_code'
					,'type' => 'options'
					,'sortable' => false
					,'options' => Df_Logging_Model_Config::s()->getLabels()
				)
			)
			->addColumn(
				'action'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Action')
					,'index' => 'action'
					,'type' => 'options'
					,'options' => $actions
					,'sortable' => false
					,'width' => 75
				)
			)
			->addColumn(
				'ip'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('IP-address')
					,'index' => 'ip'
					,'type'	=> 'text'
					,'filter' => 'df_logging/adminhtml_grid_filter_ip'
					,'renderer' => 'adminhtml/widget_grid_column_renderer_ip'
					,'sortable' => false
					,'width' => 125
				)
			)
			->addColumn(
				'user'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Username')
					,'index' => 'user'
					,'type'	=> 'text'
					,'sortable' => false
					,'filter' => 'df_logging/adminhtml_grid_filter_user'
					,'width' => 150
				)
			)
			->addColumn(
				'status'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Result')
					,'index' => 'status'
					,'sortable' => false
					,'type'	=> 'options'
					,'options' =>
						array(
							Df_Logging_Model_Event::RESULT_SUCCESS =>
								Df_Logging_Helper_Data::s()->__('Success')
							,Df_Logging_Model_Event::RESULT_FAILURE =>
								Df_Logging_Helper_Data::s()->__('Failure')
						)
					,'width' => 100
				)
			)
			->addColumn(
				'fullaction'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Full Action Name')
					,'index' => 'fullaction'
					,'sortable' => false
					,'type'	=> 'text'
				)
			)
			->addColumn(
				'info'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Short Details')
					,'index' => 'info'
					,'type' => 'text'
					,'sortable' => false
					,'filter' => 'adminhtml/widget_grid_column_filter_text'
					,'width' => 100
				)
			)
			->addColumn(
				'view'
				,array(
					'header' => Df_Logging_Helper_Data::s()->__('Full Details')
					,'width' => 50
					,'type'	=> 'action'
					,'getter' => 'getId'
					,'actions' =>
						array(
							array(
								'caption' => Df_Logging_Helper_Data::s()->__('View Details')
								,'url' => array('base' => '*/*/details')
								,'field'   => 'event_id'
							)
						)
					,'filter' => false
					,'sortable'  => false
				)
			)
		;
		$this->addExportType('*/*/exportCsv', 'CSV');
		$this->addExportType('*/*/exportXml', 'MSXML');
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('loggingLogGrid');
		$this->setDefaultSort('time');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	/**
	 * @used-by Df_Logging_Adminhtml_LoggingController::exportCsvAction()
	 * @return string
	 */
	public static function csv() {return self::i()->getCsvFile();}

	/**
	 * @used-by Df_Logging_Adminhtml_LoggingController::exportXmlAction()
	 * @return string
	 */
	public static function excel() {return self::i()->getExcelFile();}

	/**
	 * @used-by csv()
	 * @used-by xml()
	 * @return Df_Logging_Block_Index_Grid
	 */
	private static function i() {return rm_block_l(__CLASS__);}
}