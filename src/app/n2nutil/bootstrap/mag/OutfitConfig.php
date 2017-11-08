<?php

namespace n2nutil\bootstrap\mag;

class OutfitConfig {
	private $specialAttrsArr;
	private $child;

	public function __construct(array $specialAttrs = null, $child = null) {
		$this->specialAttrsArr = $specialAttrs;
		$this->child = $child;
	}

	public function getSAttrsForNature($nature) {
		foreach ($this->specialAttrsArr as $sAttrNature => $sAttrs) {
			if ($nature & $sAttrNature) {
				return $sAttrs;
			}
		}
	}

	public function getChild() {
		return $this->child;
	}
}