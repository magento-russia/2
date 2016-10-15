<?php
/**
 * @see df_encrypt()
 * @param $value
 * @return string
 */
function df_decrypt($value) {return df_mage()->coreHelper()->decrypt($value);}

/**
 * @see df_decrypt()
 * @param $value
 * @return string
 */
function df_encrypt($value) {return df_mage()->coreHelper()->encrypt($value);}


