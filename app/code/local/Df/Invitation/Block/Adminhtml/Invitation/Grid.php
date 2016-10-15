<?php
class Df_Invitation_Block_Adminhtml_Invitation_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @var Df_Invitation_Model_Invitation $row
	 * @return string
	 */
	public function getRowUrl($row) {
		return $this->getUrl('*/*/view', array('id' => $row->getId()));
	}

	/**
	 * @override
	 * @return Df_Invitation_Block_Adminhtml_Invitation_Grid
	 */
	protected function _prepareCollection() {
		/** @var Df_Invitation_Model_Resource_Invitation_Collection $collection */
		$collection = Df_Invitation_Model_Invitation::c();
		$collection
			->addWebsiteInformation()
			->addInviteeInformation()
		;
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Invitation_Block_Adminhtml_Invitation_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				'df_invitation_id'
				,array(
					'header'=> df_h()->invitation()->__('ID')
					,'width' => 80
					,'align' => 'right'
					,'type' => 'text'
					,'index' => 'invitation_id'
				)
			)
			->addColumn(
				'email'
				,array(
					'header' => df_h()->invitation()->__('Invitee Email')
					,'index' => 'invitation_email'
					,'type' => 'text'
				)
			)
			->addColumn(
				'invitee'
				,array(
					'header' => df_h()->invitation()->__('Invitee Name')
					,'index' => 'invitee_email'
					,'type' => 'text'
					,'renderer' =>
						df_admin_allowed('customer/manage')
						? 'df_invitation/adminhtml_invitation_grid_column_invitee'
						: false
				)
			)
			->addColumn(
				'date'
				,array(
					'header' => df_h()->invitation()->__('Date Sent')
					,'index' => 'date'
					,'type' => 'datetime'
					,'gmtoffset' => true
					,'width' => 170
				)
			)
			->addColumn(
				'signup_date'
				,array(
					'header' => df_h()->invitation()->__('Registered')
					,'index' => 'signup_date'
					,'type' => 'datetime'
					,'gmtoffset' => true
					,'width' => 150
				)
			)
			->addColumn(
				'status'
				,array(
					'header' => df_h()->invitation()->__('Status')
					,'index' => 'status'
					,'type' => 'options'
					,'options' => Df_Invitation_Model_Source_Invitation_Status::s()->getOptions()
					,'width' => 140
				)
			)
			->addColumn(
				'website_id'
				,array(
					'header' => df_h()->invitation()->__('Valid on Website')
					,'index' => 'website_id'
					,'type'	=> 'options'
					,'options' =>
							df_mage()->adminhtml()->system()->storeSingleton()
								->getWebsiteOptionHash()
					,'width' => 150
				)
			)
		;
		/** @var Mage_Customer_Model_Resource_Group_Collection $groups */
		$groups = df_model('customer/group')->getCollection();
		$groups
			->addFieldToFilter('customer_group_id', array('gt'=> 0))
			->load()
			->toOptionHash()
		;
		$this
			->addColumn(
				'group_id'
				,array(
					'header' => df_h()->invitation()->__('Invitee Customer Group')
					,'index' => 'group_id'
					,'filter_index' => 'invitee_group_id'
					,'type' => 'options'
					,'options' => $groups
					,'width' => 140
				)
			)
		;
		parent::_prepareColumns();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Invitation_Block_Adminhtml_Invitation_Grid
	 */
	protected function _prepareMassaction() {
		parent::_prepareMassaction();
		$this->setMassactionIdField('invitation_id');
		$this->getMassactionBlock()->setFormFieldName('invitations');
		$this->getMassactionBlock()
			->addItem(
				'cancel'
				,array(
					'label' => df_h()->invitation()->__('Discard selected')
					,'url' => $this->getUrl('*/*/massCancel')
					,'confirm' => df_h()->invitation()->__('Are you sure you want to do this?')
				)
			)
		;
		$this->getMassactionBlock()
			->addItem(
				'resend'
				,array(
					'label' => df_h()->invitation()->__('Send selected')
					,'url' => $this->getUrl('*/*/massResend')
				)
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('invitationGrid');
		$this->setDefaultSort('date');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}
}