<?php
namespace app\n2nutil\bootstrap\ui;

class BsConfig {
	protected $required;
	protected $autoPlaceholder;
	protected $placeholder;
	protected $helpText;
	protected $labelHidden;
	protected $labelAttrs;
	protected $controlAttrs;
	protected $rowClassNames;
	
	public function __construct(bool $required, bool $autoPlaceholder, string $placeholder = null,
			$helpText = null, bool $labelHidden, array $labelAttrs, array $controlAttrs, array $rowClassNames = null) {
				$this->required = $required;
				$this->autoPlaceholder = $autoPlaceholder;
				$this->placeholder = $placeholder;
				$this->helpText = $helpText;
				$this->labelHidden = $labelHidden;
				$this->labelAttrs = $labelAttrs;
				$this->controlAttrs = $controlAttrs;
				$this->rowClassNames = $rowClassNames;
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
	
	public function getRowClassNames() {
		return $this->rowClassNames;
	}
}