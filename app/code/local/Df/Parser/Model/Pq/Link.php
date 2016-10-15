<?php
abstract class Df_Parser_Model_Pq_Link extends Df_Parser_Model_Pq {
	/** @return string */
	abstract public function getName();
	
	/** @return Zend_Uri_Http */
	abstract public function getUri();


}