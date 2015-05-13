<?php
/**
 * Extends SimpleXML to add valuable functionality to SimpleXMLElement class
 *
 * @category   Varien
 * @package    Varien_Simplexml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Simplexml_Element extends SimpleXMLElement
{

    /**
     * Would keep reference to parent node
     *
     * If SimpleXMLElement would support complicated attributes
     *
     * @todo make use of spl_object_hash to keep global array of simplexml elements
     *       to emulate complicated attributes
     * @var Varien_Simplexml_Element
     */
    protected $_parent = null;

    /**
     * For future use
     *
     * @param Varien_Simplexml_Element $element
     */
    public function setParent($element)
    {
        #$this->_parent = $element;
    }

    /**
     * Returns parent node for the element
     *
     * Currently using xpath
     *
     * @return Varien_Simplexml_Element
     */
    public function getParent()
    {
        if (!empty($this->_parent)) {
            $parent = $this->_parent;
        } else {
            $arr = $this->xpath('..');
            $parent = $arr[0];
        }
        return $parent;
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function hasChildren()
    {
        if (!$this->children()) {
            return false;
        }

        // simplexml bug: @attributes is in children() but invisible in foreach
        foreach ($this->children() as $k=>$child) {
            return true;
        }
        return false;
    }

    /**
     * Returns attribute value by attribute name
     *
     * @return string
     */
    public function getAttribute($name){
        $attrs = $this->attributes();
        return isset($attrs[$name]) ? (string)$attrs[$name] : null;
    }

/*
    public function addAttribute($name, $value=null, $namespace=null)
    {
        if (is_null($value)) {
            return parent::addAttribute($name);
        } else {
            if (!is_null($value)) {
                $value = $this->xmlentities($value);
            }
            if (!is_null($namespace)) {
                return parent::addAttribute($name, $value, $namespace);
            } else {
                return parent::addAttribute($name, $value);
            }
        }
    }

    public function addChild($name, $value=null, $namespace=null)
    {
        if (is_null($value)) {
            return parent::addChild($name);
        } else {
            if (!is_null($value)) {
                $value = $this->xmlentities($value);
            }
            if (!is_null($namespace)) {
                return parent::addChild($name, $value, $namespace);
            } else {
                return parent::addChild($name, $value);
            }
        }
    }
*/

    /**
     * Find a descendant of a node by path
     *
     * @todo    Do we need to make it xpath look-a-like?
     * @todo    Check if we still need all this and revert to plain XPath if this makes any sense
     * @todo    param string $path Subset of xpath. Example: "child/grand[@attrName='attrValue']/subGrand"
     * @param   string $path Example: "child/grand@attrName=attrValue/subGrand" (to make it faster without regex)
     * @return  Varien_Simplexml_Element
     */
    public function descend($path)
    {
        # $node = $this->xpath($path);
        # return $node[0];
        if (is_array($path)) {
            $pathArr = $path;
        } else {
            // Simple exploding by / does not suffice,
            // as an attribute value may contain a / inside
            // Note that there are three matches for different kinds of attribute values specification
            if(strpos($path, "@") === false) {
                $pathArr = explode('/', $path);
            }
            else {
                $regex = "#([^@/\\\"]+(?:@[^=/]+=(?:\\\"[^\\\"]*\\\"|[^/]*))?)/?#";
                $pathArr = $pathMatches = array();
                if(preg_match_all($regex, $path, $pathMatches)) {
                    $pathArr = $pathMatches[1];
                }
            }
        }
        $desc = $this;
        foreach ($pathArr as $nodeName) {
            if (strpos($nodeName, '@')!==false) {
                $a = explode('@', $nodeName);
                $b = explode('=', $a[1]);
                $nodeName = $a[0];
                $attributeName = $b[0];
                $attributeValue = $b[1];
                //
                // Does a very simplistic trimming of attribute value.
                //
                $attributeValue = trim($attributeValue, '"');
                $found = false;
                foreach ($desc->$nodeName as $subdesc) {
                    if ((string)$subdesc[$attributeName]===$attributeValue) {
                        $found = true;
                        $desc = $subdesc;
                        break;
                    }
                }
                if (!$found) {
                    $desc = false;
                }
            } else {
                $desc = $desc->$nodeName;
            }
            if (!$desc) {
                return false;
            }
        }
        return $desc;
    }

    /**
     * Returns the node and children as an array
     *
     * @return array|string
     */
    public function asArray()
    {
        return $this->_asArray();
    }

    /**
     * asArray() analog, but without attributes
     * @return array|string
     */
    public function asCanonicalArray()
    {
        return $this->_asArray(true);
    }

    /**
     * Returns the node and children as an array
     *
     * @param bool $isCanonical - whether to ignore attributes
     * @return array|string
     */
    protected function _asArray($isCanonical = false)
    {
        $result = array();
        if (!$isCanonical) {
            // add attributes
            foreach ($this->attributes() as $attributeName => $attribute) {
                if ($attribute) {
                    $result['@'][$attributeName] = (string)$attribute;
                }
            }
        }
        // add children values
        if ($this->hasChildren()) {
            foreach ($this->children() as $childName => $child) {
                $result[$childName] = $child->_asArray($isCanonical);
            }
        } else {
            if (empty($result)) {
                // return as string, if nothing was found
                $result = (string) $this;
            } else {
                // value has zero key element
                $result[0] = (string) $this;
            }
        }
        return $result;
    }

	/**
	 * @param string $filename
	 * @param int|boolean $level [optional]
	 * @return string
	 */
	public function asNiceXml($filename = '', $level = 0) {
		/** @var string $result */
		/** @var bool $dfExtensionEnabled */
		static $dfExtensionEnabled;
		if (!isset($dfExtensionEnabled)) {
			$dfExtensionEnabled = function_exists('rm_xml_serialize_nice');
		}
		if (self::$dfExtensionEnable && $dfExtensionEnabled) {
			if (!is_numeric($level)) {
				$level = -1;
			}
			$result = rm_xml_serialize_nice($this, is_numeric($level) ? $level : -1);
			if (((0 === $level) || (false === $level)) && $filename) {
				file_put_contents($filename, $result);
			}
		}
		else {
			$result = $this->asNiceXml_Original($filename, $level);
		}
		return $result;
	}

    /**
     * Makes nicely formatted XML from the node
     *
     * @param string $filename
     * @param int|boolean $level if false
     * @return string
     */
    public function asNiceXml_Original($filename='', $level=0)
    {
        if (is_numeric($level)) {
            $pad = str_pad('', $level*3, ' ', STR_PAD_LEFT);
            $nl = "\n";
        } else {
            $pad = '';
            $nl = '';
        }

        $out = $pad.'<'.$this->getName();

        if ($attributes = $this->attributes()) {
            foreach ($attributes as $key=>$value) {
                $out .= ' '.$key.'="'.str_replace('"', '\"', (string)$value).'"';
            }
        }

        if ($this->hasChildren()) {
            $out .= '>'.$nl;
            foreach ($this->children() as $child) {
                $out .= $child->asNiceXml_Original('', is_numeric($level) ? $level+1 : true);
            }
            $out .= $pad.'</'.$this->getName().'>'.$nl;
        } else {
            $value = (string)$this;
            if (strlen($value)) {
                $out .= '>'.$this->xmlentities($value).'</'.$this->getName().'>'.$nl;
            } else {
                $out .= '/>'.$nl;
            }
        }

        if ((0===$level || false===$level) && !empty($filename)) {
            file_put_contents($filename, $out);
        }

        return $out;
    }

    /**
     * Enter description here...
     *
     * @param int $level
     * @return string
     */
    public function innerXml($level=0)
    {
        $out = '';
        foreach ($this->children() as $child) {
			/** @var Varien_Simplexml_Element $child */
            $out .= $child->asNiceXml($level);
        }
        return $out;
    }

    /**
     * Converts meaningful xml characters to xml entities
     *
     * @param  string
     * @return string
     */
    public function xmlentities($value = null)
    {
        if (is_null($value)) {
            $value = $this;
        }
        $value = (string)$value;

        $value = str_replace(
            array('&', '"', "'", '<', '>'),
            array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'),
            $value
        );

        return $value;
    }

    /**
     * Appends $source to current node
     *
     * @param Varien_Simplexml_Element $source
     * @return Varien_Simplexml_Element
     */
    public function appendChild($source)
    {
        if ($source->children()) {
            /**
             * @see http://bugs.php.net/bug.php?id=41867 , fixed in 5.2.4
             */
            if (version_compare(phpversion(), '5.2.4', '<')===true) {
                $name = $source->children()->getName();
            }
            else {
                $name = $source->getName();
            }
            $child = $this->addChild($name);
        } else {
            $child = $this->addChild($source->getName(), $this->xmlentities($source));
        }
        $child->setParent($this);

        $attributes = $source->attributes();
        foreach ($attributes as $key=>$value) {
            $child->addAttribute($key, $this->xmlentities($value));
        }

        foreach ($source->children() as $sourceChild) {
            $child->appendChild($sourceChild);
        }
        return $this;
    }

	/** @var bool */
	static $dfExtensionEnable = true;

    /**
     * Extends current node with xml from $source
     *
     * If $overwrite is false will merge only missing nodes
     * Otherwise will overwrite existing nodes
     *
     * @param Varien_Simplexml_Element|bool $source
     * @param boolean $overwrite
     * @return Varien_Simplexml_Element
     */
    public function extend($source, $overwrite=false)
    {
		/**
		 * 2014-11-25
		 * Обратите внимание, что $source может принимать значение false.
		 * В код Magento CE уже давно была заложена обработка подобного случая,
		 * однако впервые встретил его только сегодня при установке Magento CE 1.9.1.0.
		 */
		if ($source instanceof Varien_Simplexml_Element) {
			/** @var bool $dfExtensionEnabled */
			static $dfExtensionEnabled;
			if (!isset($dfExtensionEnabled)) {
				$dfExtensionEnabled = function_exists('rm_xml_extend');
			}
			if (self::$dfExtensionEnable && $dfExtensionEnabled) {
				rm_xml_extend($this, $source, $overwrite);
			}
			else {
				foreach ($source->children() as $child) {
					$this->extendChild($child, $overwrite);
				}
			}
		}
        return $this;
    }

    /**
     * Extends one node
     *
     * @param Varien_Simplexml_Element $source
     * @param boolean $overwrite
     * @return Varien_Simplexml_Element
     */
    public function extendChild($source, $overwrite=false)
    {
		/** @var bool $dfExtensionEnabled */
		static $dfExtensionEnabled;
		if (!isset($dfExtensionEnabled)) {
			$dfExtensionEnabled = function_exists('rm_xml_extend_child');
		}
		if (self::$dfExtensionEnable && $dfExtensionEnabled) {
			rm_xml_extend_child($this, $source, $overwrite);
		}
		else {
			// this will be our new target node
			$targetChild = null;

			// name of the source node
			$sourceName = $source->getName();

			// here we have children of our source node
			$sourceChildren = $source->children();

			if (!$source->hasChildren()) {
				// handle string node
				if (isset($this->$sourceName)) {
					// if target already has children return without regard
					if ($this->$sourceName->hasChildren()) {
						return $this;
					}
					if ($overwrite) {
						unset($this->$sourceName);
					} else {
						return $this;
					}
				}

				$targetChild = $this->addChild($sourceName, $source->xmlentities());
				$targetChild->setParent($this);
				foreach ($source->attributes() as $key=>$value) {
					$targetChild->addAttribute($key, $this->xmlentities($value));
				}
				return $this;
			}

			if (isset($this->$sourceName)) {
				$targetChild = $this->$sourceName;
			}

			if (is_null($targetChild)) {
				// if child target is not found create new and descend
				$targetChild = $this->addChild($sourceName);
				$targetChild->setParent($this);
				foreach ($source->attributes() as $key=>$value) {
					$targetChild->addAttribute($key, $this->xmlentities($value));
				}
			}

			// finally add our source node children to resulting new target node
			foreach ($sourceChildren as $childKey=>$childNode) {
				$targetChild->extendChild($childNode, $overwrite);
			}
		}

        return $this;
    }

    public function setNode($path, $value, $overwrite=true)
    {
		/** @var bool $is_php_lt_5_2_6 */
		static $is_php_lt_5_2_6;
		if (!isset($is_php_lt_5_2_6)) {
			$is_php_lt_5_2_6 = (true === version_compare(phpversion(), '5.2.6', '<'));
		}
        $arr1 = explode('/', $path);
        $arr = array();
        foreach ($arr1 as $v) {
            if (!empty($v)) $arr[] = $v;
        }
        $last = sizeof($arr)-1;
        $node = $this;
        foreach ($arr as $i=>$nodeName) {
			// BEGIN PATCH
			/** @var bool $nodeIsSet */
			$nodeIsSet = isset($node->$nodeName);
			// END PATCH
            if ($last===$i) {
                /*
                if (isset($xml->$nodeName)) {
                    if ($overwrite) {
                        unset($xml->$nodeName);
                    } else {
                        continue;
                    }
                }
                $xml->addChild($nodeName, $xml->xmlentities($value));
                */
                if (!$nodeIsSet || $overwrite) {
					// BEGIN PATCH
					/**
					 * Избегаем сбоя
					 * «Warning: Varien_Simplexml_Element::setNode():
					 * Cannot assign to an array of nodes (duplicate subnodes or attr detected)»,
					 * который случается, когда в одном из файлов XML ядра Magento
					 * ошибочно дублируется один и тот же узел.
					 * Например, в Magento CE 1.7.0.2 ошибочно дублируется узел discount_tax:
							<calculation>
								<algorithm>TOTAL_BASE_CALCULATION</algorithm>
								<apply_after_discount>0</apply_after_discount>
								<discount_tax>0</discount_tax>
								<based_on>shipping</based_on>
								<price_includes_tax>0</price_includes_tax>
								<shipping_includes_tax>0</shipping_includes_tax>
								<discount_tax>0</discount_tax>
								<apply_tax_on>0</apply_tax_on>
							</calculation>
					 */
					if ($nodeIsSet) {
						unset($node->$nodeName);
					}
					// END PATCH
                    // http://bugs.php.net/bug.php?id=36795
                    // comment on [8 Feb 8:09pm UTC]
                    if ($nodeIsSet && $is_php_lt_5_2_6) {
                        $node->$nodeName = $node->xmlentities($value);
                    } else {
                        $node->$nodeName = $value;
                    }
                }
            } else {
                if (!$nodeIsSet) {
                    $node = $node->addChild($nodeName);
                } else {
                    $node = $node->$nodeName;
                }
            }

        }
        return $this;
    }

/*
    public function extendChildByNode($source, $overwrite=false, $mergeBy='name')
    {
        // this will be our new target node
        $targetChild = null;

        // name of the source node
        $sourceName = $source->getName();

        // here we have children of our source node
        $sourceChildren = $source->children();

        if (!$sourceChildren) {
            // handle string node
            if (isset($this->$sourceName)) {
                if ($overwrite) {
                    unset($this->$sourceName);
                } else {
                    return $this;
                }
            }
            $targetChild = $this->addChild($sourceName, (string)$source);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
            return $this;
        }

        if (isset($this->$sourceName)) {
            // search for target child with same name subnode as node's name
            if (isset($source->$mergeBy)) {
                foreach ($this->$sourceName as $targetNode) {
                    if (!isset($targetNode->$mergeBy)) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                    if ((string)$source->$mergeBy==(string)$targetNode->$mergeBy) {
                        $targetChild = $targetNode;
                        break;
                    }
                }
            } else {
                $existsWithId = false;
                foreach ($this->$sourceName as $targetNode) {
                    if (isset($targetNode->$mergeBy)) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                }
                $targetChild = $this->$sourceName;
            }
        }

        if (is_null($targetChild)) {
            // if child target is not found create new and descend
            $targetChild = $this->addChild($sourceName);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
        }

        // finally add our source node children to resulting new target node
        foreach ($sourceChildren as $childKey=>$childNode) {
            $targetChild->extendChildByNode($childNode, $overwrite, $mergeBy);
        }

        return $this;
    }

    public function extendChildByAttribute($source, $overwrite=false, $mergeBy='name')
    {
        // this will be our new target node
        $targetChild = null;

        // name of the source node
        $sourceName = $source->getName();

        // here we have children of our source node
        $sourceChildren = $source->children();

        if (!$sourceChildren) {
            // handle string node
            if (isset($this->$sourceName)) {
                if ($overwrite) {
                    unset($this->$sourceName);
                } else {
                    return $this;
                }
            }
            $targetChild = $this->addChild($sourceName, (string)$source);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
            return $this;
        }

        if (isset($this->$sourceName)) {
            // search for target child with same name subnode as node's name
            if (isset($source[$mergeBy])) {
                foreach ($this->$sourceName as $targetNode) {
                    if (!isset($targetNode[$mergeBy])) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                    if ((string)$source[$mergeBy]==(string)$targetNode[$mergeBy]) {
                        $targetChild = $targetNode;
                        break;
                    }
                }
            } else {
                $existsWithId = false;
                foreach ($this->$sourceName as $targetNode) {
                    if (isset($targetNode[$mergeBy])) {
                        Zend::exception("Can't merge identified node with non identified");
                    }
                }
                $targetChild = $this->$sourceName;
            }
        }

        if (is_null($targetChild)) {
            // if child target is not found create new and descend
            $targetChild = $this->addChild($sourceName);
            foreach ($source->attributes() as $key=>$value) {
                $targetChild->addAttribute($key, $value);
            }
        }

        // finally add our source node children to resulting new target node
        foreach ($sourceChildren as $childKey=>$childNode) {
            $targetChild->extendChildByAttribute($childNode, $overwrite, $mergeBy);
        }

        return $this;
    }
*/


}
