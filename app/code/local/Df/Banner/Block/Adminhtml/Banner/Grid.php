<?php
class Df_Banner_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid {
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
		$this->setId('df_banner_grid');
		$this->setDefaultSort('banner_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banner_Grid
	 */
	protected function _prepareCollection() {
		$this->setCollection(Df_Banner_Model_Banner::c());
		return parent::_prepareCollection();
	}

	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banner_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				'banner_id'
				,array(
					'header' => df_h()->banner()->__('ID')
					,'align' =>'right'
					,'width' => '50px'
					,'index' => 'banner_id'
				)
			)
			->addColumn(
				'identifier'
				,array(
					'header' =>
						df_h()->banner()->__(
							'Внутреннее системное имя (Вы потом используете его в макете)'
						)
					,'align' =>'left'
					,'index' => 'identifier'
				)
			)
			->addColumn(
				'title'
				,array(
					'header' => df_h()->banner()->__('Название')
					,'align' =>'left'
					,'index' => 'title'
				)
			)
			->addColumn(
				'show_title'
				,array(
					'header' => df_h()->banner()->__('Показывать название посетителям?')
					,'align' => 'left'
					,'width' => '40px'
					,'index' => 'show_title'
					,'type' => 'options'
					,'options' =>
						array(
							1 => 'Yes'
							,2 => 'No'
						)
				)
			)
			->addColumn(
				'width'
				,array(
					'header' => df_h()->banner()->__('Ширина (в пикселях)')
					,'align' =>'right'
					,'width' => '40px'
					,'index' => 'width'
				)
			)
			->addColumn(
				'height'
				,array(
					'header' => df_h()->banner()->__('Высота (в пикселях)')
					,'align' =>'right'
					,'width' => '40px'
					,'index' => 'height'
				)
			)
			->addColumn(
				'delay'
				,array(
					'header' =>
						df_h()->banner()->__(
							'Продолжительность показа одного объявления (в милисекундах)'
						)
					,'align' =>'right'
					,'width' => '40px'
					,'index' => 'delay'
				)
			)
			->addColumn(
				'status'
				,array(
					'header' => df_h()->banner()->__('Включен?')
					,'align' => 'left'
					,'width' => '80px'
					,'index' => 'status'
					,'type' => 'options'
					,'options' =>
						array(
							1 => 'Да'
							,2 => 'Нет'
						)
				)
			)
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
	 * @return Df_Banner_Block_Adminhtml_Banner_Grid
	 */
	protected function _prepareMassaction() {
		parent::_prepareMassaction();
		$this->setMassactionIdField('banner_id');
		$this->getMassactionBlock()->setFormFieldName('df_banner');
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
					'label'=> df_h()->banner()->__('Change status')
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
									,'label' => df_h()->banner()->__('Status'),'values' => $statuses
								)
						)
				)
			)
		;
		return $this;
	}

	/** @return Df_Banner_Block_Adminhtml_Banner_Grid */
	public static function i() {return df_block(__CLASS__);}
}