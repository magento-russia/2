<?php
class Df_Client_IndexController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {Df_Core_Model_RemoteControl_Action_Front::i($this)->process();}
}