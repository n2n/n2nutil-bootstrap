<?php
namespace n2nutil\bootstrap\config;

use n2n\util\type\ArgUtils;

class BootstrapConfig {
	private $breakpoints;
	
	function __construct(array $breakpoints) {
		$this->setBreakpoints($breakpoints);
	}
	
	function setBreakpoints(array $breakpoints) {
		ArgUtils::valArray($breakpoints, 'numeric');
		$this->breakpoints = $breakpoints;
	}
	
	/**
	 * @return int[]
	 */
	function getBreakpoints() {
		return $this->breakpoints;
	}
	
	/**
	 * @return string[]
	 */
	function getBreakpointNames() {
		return array_keys($this->breakpoints);
	}
	
	/**
	 * @param string $name
	 * @throws UnknownBreakpointException
	 * @return int
	 */
	function getBreakpointValueByName(string $name) {
		if (isset($this->breakpoints[$name])) {
			return $this->breakpoints[$name];
		}
		
		throw new UnknownBreakpointException('Breakpoint ' . $name . ' is not defined.');
	}
	
	/**
	 * @return \n2nutil\bootstrap\config\BootstrapConfig
	 */
	static function getDefault() {
		return new BootstrapConfig([
			'sm' => 576,
			'md' => 768,
			'lg' => 992,
			'xl' => 1200
		]);
	}
}

class UnknownBreakpointException extends \RuntimeException {
	
}