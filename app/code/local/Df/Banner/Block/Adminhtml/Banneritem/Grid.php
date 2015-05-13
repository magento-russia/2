<?php
class Df_Banner_Block_Adminhtml_Banneritem_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @param Mage_Core_Model_Abstract $row
	 * @return string
	 */
	public function getRowUrl($row) {return $this->getUrl('*/*/edit', array('id' => $row->getId()));}

	/**
	 * @override
	 * @return string|null
	 */
	public function getTemplate() {
		/** @var string|null $result */
		$result =
			/**
			 * В отличие от витрины, шаблоны административной части будут отображаться
			 * даже если модуль отключен (но модуль должен быть лицензирован)
			 */
			!(df_enabled(Df_Core_Feature::BANNER))
			? null
			: parent::getTemplate()
		;
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('df_banner_itemGrid');
		$this->setDefaultSort('banner_item_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banneritem_Grid
	 */
	protected function _prepareCollection() {
		/** @var Df_Banner_Model_Resource_Banneritem_Collection $collection */
		$collection = Df_Banner_Model_Banneritem::c();
		$collection
			->setOrder('banner_id','DESC')
			->setOrder('banner_order','ASC')
		;
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/** @return Df_Banner_Block_Adminhtml_Banneritem_Grid */
	protected function _prepareColumns() {
		$this->setTemplate('df/banner/grid.phtml');
		$this
			->addColumn(
				'banner_item_id'
				,array(
					'header' => df_h()->banner()->__('ID')
					,'align' =>'right'
					,'width' => '50px'
					,'index' => 'banner_item_id'
				)
			)
		;
		/** @var string[] $banners */
		$banners = array();
		$collection = Df_Banner_Model_Banner::c();
		foreach ($collection as $banner) {
			/** @var Df_Banner_Model_Banner $banner */
			$banners[$banner->getId()] = $banner->getTitle();
		}
		$this
			->addColumn(
				'banner_id'
				,array(
					'header' => df_h()->banner()->__('Banner')
					,'align' =>'left'
					,'index' => 'banner_id'
					,'type' => 'options'
					,'options' => $banners
				)
			)
		;
		$this
			->addColumn(
				'image'
				,array(
					'header'=> df_h()->banner()->__('Image')
					,'type' => 'image'
					,'width' => 64
					,'index' => 'image'
				)
			)
		;
		$this
			->addColumn(
				'title'
				,array(
					'header' => df_h()->banner()->__('Title')
					,'align' =>'left'
					,'index' => 'title'
				)
			)
		;
		$this
			->addColumn(
				'url'
				,array(
					'header' => df_h()->banner()->__('Url')
					,'align' =>'left'
					,'index' => 'url'
				)
			)
		;
		$this
			->addColumn('banner_order', array(
		'header'	=> df_h()->banner()->__('Order'),  'align'	 =>'left',
		'width' 	  => 64,  'index'	 => 'banner_order',  ));

		$this
			->addColumn('status', array(
		'header'	=> df_h()->banner()->__('Status'),  'align'	 => 'left',  'width'	 => '80px',  'index'	 => 'status',  'type'	  => 'options',  'options'   => array(
		1 => 'Enabled',  2 => 'Disabled',  ),  ));

		$this
			->addColumn(
				'action'
				,array(
					'header' => df_h()->banner()->__('Action')
					,'width' => '100'
					,'type' => 'action'
					,'getter' => 'getId'
					,'actions' =>
						array(
							array(
								'caption' => df_h()->banner()->__('Edit')
								,'url' => array('base'=> '*/*/edit')
								,'field' => 'id'
							)
						)
					,'filter' => false
					,'sortable' => false
					,'index' => 'stores'
					,'is_system' => true
				)
			)
		;
		$this->addExportType('*/*/exportCsv', df_h()->banner()->__('CSV'));
		$this->addExportType('*/*/exportXml', df_h()->banner()->__('XML'));
		return parent::_prepareColumns();
	}

	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banneritem_Grid
	 */
	protected function _prepareMassaction() {
		parent::_prepareMassaction();
		$this->setMassactionIdField('banner_item_id');
		$this->getMassactionBlock()->setFormFieldName('df_banner_item');
		$this->getMassactionBlock()
			->addItem(
				'delete'
				,array(
					'label'	=> df_h()->banner()->__('Delete')
					,'url' => $this->getUrl('*/*/massDelete')
					,'confirm' => df_h()->banner()->__('Are you sure?')
				)
			)
		;
		$statuses = Df_Banner_Model_Status::s()->getOptionArray();
		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()
			->addItem(
				'status'
				,array(
					'label' => df_h()->banner()->__('Change status')
					,'url' =>
						$this->getUrl(
							'*/*/massStatus'
							,array('_current'=>true)
						)
						,'additional' =>
							array(
								'visibility' =>
									array(
										'name' => 'status'
										,'type' => 'select'
										,'class' => 'required-entry'
										,'label' => df_h()->banner()->__('Status')
										,'values' => $statuses
									)
							)
				)
			)
		;
		return $this;
	}

	/** @return Df_Banner_Block_Adminhtml_Banneritem_Grid */
	public static function i() {return df_block(__CLASS__);}
}