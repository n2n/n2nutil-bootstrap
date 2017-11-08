<?php
namespace n2nutil\bootstrap\ui;

class BsConfig {
	protected $required;
	protected $autoPlaceholder;
	protected $placeholder;
	protected $helpText;
	protected $labelHidden;
	protected $labelAttrs;
	protected $controlAttrs;
	protected $groupAttrs;
	protected $rowClassNames;
	protected $child;

	public function __construct(bool $required, bool $autoPlaceholder, string $placeholder = null,
			$helpText = null, bool $labelHidden, array $labelAttrs, array $controlAttrs, array $groupAttrs,
			array $rowClassNames = null, BsComposer $child = null) {
		$this->required = $required;
		$this->autoPlaceholder = $autoPlaceholder;
		$this->placeholder = $placeholder;
		$this->helpText = $helpText;
		$this->labelHidden = $labelHidden;
		$this->labelAttrs = $labelAttrs;
		$this->controlAttrs = $controlAttrs;
		$this->groupAttrs = $groupAttrs;
		$this->rowClassNames = $rowClassNames;
		$this->child = $child;
	}
	
	public function isRequired() {
		return $this->required;
	}
	
	public function isAutoPlaceholderUsed() {
		return $this->autoPlaceholder;
	}
	
	public function getPlaceholder() {
		return $this->placeholder;
	}
	
	public function getHelpText() {
		return $this->helpText;
	}
	
	public function isLabelHidden() {
		return $this->labelHidden;
	}
	
	public function getLabelAttrs() {
		return $this->labelAttrs;
	}
	
	public function getControlAttrs() {
		return $this->controlAttrs;
	}
	
	public function getGroupAttrs() {
		return $this->groupAttrs;
	}
	
	public function getRowClassNames() {
		return $this->rowClassNames;
	}

	public function getChild() {
		return $this->child;
	}
}