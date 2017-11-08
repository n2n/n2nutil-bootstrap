<?php

namespace n2nutil\bootstrap\mag;

class Outfit {
	/**
	 * Set attributes for group natures, Mags like MagCollectionArrayMag and ArrayMag will be affected
	 * @param $attrs
	 * @return OutfitComposer
	 */
	public static function arrayItemAttrs($attrs) {
		return (new OutfitComposer())->arrayItemAttrs($attrs);
	}

	/**
	 * @param int $nature
	 * @param $attrs
	 * @return OutfitComposer
	 */
	public static function specialAttr(int $nature, $attrs) {
		return (new OutfitComposer())->specialAttrs($nature, $attrs);
	}

	/**
	 * @param OutfitComposer $outfitComposer
	 * @return mixed
	 */
	public static function child(OutfitComposer $outfitComposer) {
		return (new OutfitComposer())->child($outfitComposer);
	}
}