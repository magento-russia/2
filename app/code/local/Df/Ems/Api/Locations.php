<?php
namespace Df\Ems\Api;
use Df\Ems\Request as Request;
abstract class Locations extends \Df_Core_Model {
	/** @return array(string => string) */
	public function mapToEmsIdFromName() {return dfc($this, function() {return
		df_key_uc(array_column($this->locationsRaw(), 'value', 'name'))
	;});}

	/** @return array(array(string => string)) */
	protected function locationsRaw() {return dfc($this, function() {return
		Request::i([
			'method' => 'ems.get.locations'
			,'type' => df_class_last_lc($this)
			,'plain' => df_bts(true)
		])->p('locations');
	});}
}