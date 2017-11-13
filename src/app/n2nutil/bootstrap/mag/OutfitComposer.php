<?php

namespace n2nutil\bootstrap\mag;

use n2n\web\dispatch\mag\UiOutfitter;

class OutfitComposer {
	private $child;
	private $specialAttrs = array();

	/**
	 * Sets special attributes for group-mags, key should be the natures that mags are.
	 * @param array $attrs
	 * @return OutfitComposer $this
	 */
	public function arrayItemAttrs(array $attrs) {
		$this->natureAttr(UiOutfitter::NATURE_MASSIVE_ARRAY_ITEM, $attrs);
		return $this;
	}

	public function natureAttr(int $nature, array $attrs) {
		$this->specialAttrs[$nature] = $attrs;
		return $this;
	}

	/**
	 * Sets special attributes for mags, key should be the natures that mags are.
	 * @param array $attrs
	 * @return OutfitComposer $this
	 */
	public function specialAttrs(array $sattrs) {
		$this->specialAttrs = $sattrs;
		return $this;
	}

	public function child(OutfitComposer $outfitComposer) {
		$this->child = $outfitComposer;
		return $this;
	}

	public function toConfig() {
		return new OutfitConfig($this->specialAttrs, $this->child);
	}
}