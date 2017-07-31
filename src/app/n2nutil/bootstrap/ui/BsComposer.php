<?php
namespace n2nutil\bootstrap\ui;

use n2n\impl\web\ui\view\html\HtmlUtils;

class BsComposer {
	private $required;
	private $labelHidden;
	private $labelAttrs;
	private $labelAttrsCleared = false;
	private $controlAttrs;
	private $controlAttrsCleared = false;
	private $autoPlaceholderUsed;
	private $placeholder;
	private $helpText;
	private $rowClassNames;
	
	/**
	 * @param bool $required
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function req(bool $required = true) {
		$this->required = $required;
		return $this;
	}
	
	/**
	 * @param bool $labelHidden
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function lHide(bool $labelHidden = true) {
		$this->labelHidden = $labelHidden;
		return $this;
	}
	
	/**
	 * @param bool $labelAttrsCleared
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function lAttrsClear(bool $labelAttrsCleared = true) {
		$this->labelAttrsCleared = $labelAttrsCleared;
		return $this;
	}
	
	/**
	 * @param array $labelAttrs
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function lAttrs(array $labelAttrs) {
		$this->labelAttrs = $labelAttrs;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param unknown $value
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function lAttr(string $name, $value = null) {
		$this->labelAttrs = $this->buildAttrs((array) $this->labelAttrs, $name, $value);
		return $this;
	}
	
	/**
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function ph($placeholder) {
		$this->placeholder = $placeholder;
		return $this;
	}
	
	/**
	 * @param bool $noAutoPlaceholder
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function noAutoPh(bool $noAutoPlaceholder = true) {
		$this->autoPlaceholderUsed = !$noAutoPlaceholder;
		return $this;
	}
	
	/**
	 * @param string $helpTxet
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function hTxt($helpText) {
		$this->helpText = $helpText;
		return $this;
	}
	
	/**
	 * @param array $controlAttrs
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function cAttrs(array $controlAttrs) {
		$this->controlAttrs = $controlAttrs;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param unknown $value
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function cAttr(string $name, $value = null) {
		$this->controlAttrs = $this->buildAttrs((array) $this->controlAttrs, $name, $value);
		return $this;
	}
	
	private function buildAttrs(array $attrs, string $name, $value) {
		$newAttrs = null;
		if ($value === null) {
			$newAttrs = array($name);
		} else {
			$newAttrs = array($name => $value);
		}
		
		return HtmlUtils::mergeAttrs($attrs, $newAttrs, true);
	}
	
	/**
	 * @param bool $controlAttrsCleared
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function cAttrsClear(bool $controlAttrsCleared = true) {
		$this->controlAttrsCleared = $controlAttrsCleared;
		return $this;
	}
	
	/**
	 * @param string $labelClassName
	 * @param string $containerClassName
	 * @param string $labelOffsetClassName
	 * @return \n2nutil\bootstrap\ui\BsComposer
	 */
	public function row(string $labelClassName, string $containerClassName, string $labelOffsetClassName) {
		$this->rowClassNames = array('labelClassName' => $labelClassName,
				'containerClassName' => $containerClassName,
				'labelOffsetClassName' => $labelOffsetClassName);
		return $this;
	}
	
	public function toBsConfig(BsConfig $parentBsConfig = null) {
		$required = $this->required ?? false;
		$placeholder = $this->placeholder;
		$helpText = $this->helpText;
		$labelHidden = $this->labelHidden ?? false;
		$autoPlaceholder = $this->autoPlaceholderUsed ?? $labelHidden ? true : false;
		$labelAttrs = (array) $this->labelAttrs;
		$controlAttrs = (array) $this->controlAttrs;
		$rowClassNames = $this->rowClassNames;
		
		if ($parentBsConfig !== null) {
			if ($this->required === null) $required = $parentBsConfig->isRequired();
			if ($this->autoPlaceholderUsed === null) $autoPlaceholder = $parentBsConfig->isAutoPlaceholderUsed();
			if ($this->placeholder === null) $placeholder = $parentBsConfig->getPlaceholder();
			if ($this->helpText === null) $helpText = $parentBsConfig->getHelpText();
			if ($this->labelHidden === null) $labelHidden = $parentBsConfig->isLabelHidden();
			if (!$this->labelAttrsCleared) {
				$labelAttrs = HtmlUtils::mergeAttrs($parentBsConfig->getLabelAttrs(), $labelAttrs);
			}
			if (!$this->controlAttrsCleared) {
				$controlAttrs = HtmlUtils::mergeAttrs($parentBsConfig->getControlAttrs(), $controlAttrs);
			}
			if ($this->rowClassNames === null) $rowClassNames = $parentBsConfig->getRowClassNames();
		}
		
		return new BsConfig($required, $autoPlaceholder, $placeholder, $helpText, $labelHidden, $labelAttrs,
				$controlAttrs, $rowClassNames);
	}
}
