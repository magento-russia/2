<?php
namespace Df\C1\Cml2\Export\Processor;
use Df\C1\Cml2\Export\Entry as Entry;
class Sale extends \Df_Core_Model {
	/** @return Entry */
	protected function entry() {return Entry::s();}
}