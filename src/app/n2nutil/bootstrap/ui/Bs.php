<?php
namespace n2nutil\bootstrap\ui;

class Bs {
	/**
	 * @param bool $required
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function req(bool $required = true) {
		return (new BsComposer())->req($required);
	}
	
	/**
	 * @param bool $labelHidden
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function lHide(bool $labelHidden = true) {
		return (new BsComposer())->lHide($labelHidden);
	}
	
	/**
	 * @param array $labelAttrs
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function lAttrs(array $labelAttrs) {
		return (new BsComposer())->lAttrs($labelAttrs);
	}
	
	/**
	 * @param bool $labelAttrsCleared
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function lAttrsClear(bool $labelAttrsCleared = true) {
		return (new BsComposer())->lAttrsClear($labelAttrsCleared);
	}
	
	/**
	 * @param string $name
	 * @param unknown $value
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function lAttr(string $name, $value = null) {
		return (new BsComposer())->lAttr($name, $value);
	}
	
	/**
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function ph($placeholder) {
		return (new BsComposer())->ph($placeholder);
	}
	
	/**
	 * @param bool $noAutoPlaceholder
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function noAutoPh(bool $noAutoPlaceholder = true) {
		return (new BsComposer())->noAutoPh($noAutoPlaceholder);
	}
	
	/**
	 * @param bool $noAutoPlaceholder
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function hTxt($helpText) {
		return (new BsComposer())->hTxt($helpText);
	}
	
	/**
	 * @param array $controlAttrs
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function cAttrs(array $controlAttrs) {
		return (new BsComposer())->cAttrs($controlAttrs);
	}
	
	/**
	 * @param string $name
	 * @param unknown $value
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function cAttr(string $name, $value = null) {
		return (new BsComposer())->cAttr($name, $value);
	}
	
	/**
	 * @param bool $controlAttrsCleared
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function cAttrsClear(bool $controlAttrsCleared = true) {
		return (new BsComposer())->cAttrsClear($controlAttrsCleared);
	}
	
	/**
	 * @param string $labelClassName
	 * @param string $containerClassName
	 * @param string $labelOffsetClassName
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public static function row(string $labelClassName, string $containerClassName, string $labelOffsetClassName) {
		return (new BsComposer())->row($labelClassName, $containerClassName, $labelOffsetClassName);
	}
}