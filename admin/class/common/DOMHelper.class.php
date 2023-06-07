<?php
class DOMHelper
{
	/**
	 * @param DOMElement $element
	 */
	public static function dump($element)
	{
		return $element->ownerDocument->saveHTML($element);
	}
}