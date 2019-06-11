<?php
namespace n2nutil\bootstrap\config;

use n2n\util\type\ArgUtils;

class BootstrapConfig {
	private $breakpoints;
	
	function __construct(array $breakpoints) {
		$this->setBreakpoints($this->breakpoints);
	}
	
	function setBreakpoints(array $breakpoints) {
		ArgUtils::valArray($breakpoints, 'numeric');
		$this->breakpoints = $breakpoints;
	}
	
	function getBreakpoints() {
		return $this->breakpoints;
	}
	
}