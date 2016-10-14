<?php
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
Df_AccessControl_Model_Resource_Role::s()->tableCreate($this);
$this->endSetup();