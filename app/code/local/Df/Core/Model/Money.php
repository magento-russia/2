<?php
class Df_Core_Model_Money extends Df_Core_Model {
	/**
	 * @override
	 * @return string
	 */
	public function __toString() {return $this->getAsString();}

	/** @return float */
	public function getAsFixedFloat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = round($this->getOriginalAsFloat(), 2);;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getAsInteger() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_round($this->getAsFixedFloat());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = number_format($this->getAsFixedFloat(), 2, '.', '');
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getFractionalPart() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_round(100 * ($this->getAsFixedFloat() - $this->getIntegerPart()));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFractionalPartAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf('%02d', $this->getFractionalPart());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getIntegerPart() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (int)floor($this->getAsFixedFloat());
		}
		return $this->{__METHOD__};
	}

	/** @return float|string|int */
	public function getOriginal() {return $this->cfg(self::$P__AMOUNT);}

	/** @return float */
	public function getOriginalAsFloat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_float($this->getOriginal());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__AMOUNT, DF_V_FLOAT);
	}
	/** @var string */
	private static $P__AMOUNT = 'amount';

	/**
	 * @used-by rm_money()
	 * @param float|int|string $amount
	 * @return Df_Core_Model_Money
	 */
	public static function i($amount) {return new self(array(self::$P__AMOUNT => $amount));}
}