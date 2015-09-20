<?php
/**
 * 2015-08-14
 * @return string
 */
function rm_ruri() {static $r; return $r ? $r : $r = Mage::app()->getRequest()->getRequestUri();}