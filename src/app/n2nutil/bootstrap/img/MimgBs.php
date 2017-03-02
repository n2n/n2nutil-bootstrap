<?php

namespace n2nutil\bootstrap\img;

use n2n\impl\web\ui\view\html\img\ProportionalImgComposer;

class MimgBs {
	
	/**
	 * @param ProportionalImgComposer $pic
	 * @return \n2nutil\bootstrap\img\BsImgComposer
	 */
	public static function xs(ProportionalImgComposer $pic) {
		return new BsImgComposer($pic);
	}
}