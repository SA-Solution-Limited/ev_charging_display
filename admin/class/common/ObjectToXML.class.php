<?php
class ObjectToXML {
	/**
	 * @param mixed $obj
	 * @param SimpleXMLElement $parent
	 * @throws Exception
	 * @return SimpleXMLElement
	 */
	private function convertObj($obj, $parent = null) {
		if (is_array($obj)) {
			//Helper::log($obj, '1 array');
			if ($parent == null)
				throw new Exception("parent not found but array is given");
			
			foreach ($obj as $item) {
				Helper::log($item, '1 array item');
				$this->convertObj($item, $parent);
			}
			return $parent;
		}
		
		if ($parent == null) {
			$parent = new SimpleXMLElement('<'.$this->get_real_class($obj).'/>');
		} else {
			$parent = $parent->addChild($this->get_real_class($obj));
		}
		$reflect = new ReflectionClass($obj);
		$comment = $reflect->getDocComment();
		$props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		//Helper::log($props, '$props');
		
		$obj_vars = get_object_vars($obj);
		foreach ($obj_vars as $key => $value) {
			if (is_array($value)) {
				//Helper::log($value, 'array found '.$key);
				if (count($value) == 0) {
					//Helper::log('array empty');
					continue;
				}
				// obtain comment
				$comment = null;
				$tmp_props = arr::select()->from($props)->where("{name} == '$key'")->take(1);
				if (count($tmp_props) > 0)
					$comment = $tmp_props[0]->getDocComment();
				
				if ($this->contains($comment, '@skip_tag'))
					$container = $parent;
				else {
					// add array container
					$container = $parent->addChild($key);
				}
				
				if (count($tmp_props) > 0) {
					//Helper::log($tmp_props[0], '$prop');
					$tag = $this->getParam($tmp_props[0]->getDocComment(), 'string_array');
					if ($tag != null) {
						//Helper::log('begin construct array of string tag='.$tag);
						foreach ($value as $value_string) {
							$container->addChild($tag, $value_string);
						}
						continue;
					}
				}
				//Helper::log('done search');
				$this->convertObj($value, $container);
			} else if (is_object($value)) {
				//Helper::log($value, 'object found');
				$child = $parent->addChild($this->get_real_class($value));
				$this->convertObj($value, $child);
				//Helper::log($child, 'object converted');
			} else {
				//Helper::log($value, 'primitive found');
				$parent->addChild($key, $value);
			}
		}
		return $parent;
	}
	
	public function convert($obj) {
		$parent = $this->convertObj($obj);
		return $parent->asXML();
	}
	
	/**
	 * Obtains an object class name without namespaces
	 */
	private function get_real_class($obj) {
		$classname = get_class($obj);
	
		if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
			$classname = $matches[1];
		}
	
		return $classname;
	}
	
	/**
	 * Parse class comment
	 * @param string $comment
	 * @param string $word
	 * @return string
	 */
	private function getParam($comment, $word) {
		$params = array();
		foreach (explode("\n", $comment) as $line) {
			if (preg_match('/\*\s+@' . $word . '\s+(.[^\s]+)/', trim($line), $match))
				return $match[1];
		}
		return null;
	}
	
	/**
	 * Check if given comment contains word
	 * @param string $comment
	 * @param string $word
	 * @return boolean
	 */
	private function contains($comment, $word) {
		if ($comment == null) return false;
		return strpos($comment, $word) !== false;
	}
}