<?php
namespace n2nutil\bootstrap\img;

use n2n\impl\web\ui\view\html\img\ImgComposer;
use n2n\impl\web\ui\view\html\img\ProportionalImgComposer;
use n2n\util\type\ArgUtils;
use n2n\core\container\N2nContext;
use n2n\io\managed\File;
use n2n\impl\web\ui\view\html\img\ImgSet;
use n2nutil\bootstrap\config\BootstrapConfig;

class BsImgComposer implements ImgComposer {
// 	const BP_SM = 576;
// 	const BP_MD = 768;
// 	const BP_LG = 992;
// 	const BP_XL = 1200;

	const RESERVED_BP = 'xs';
	
	/**
	 * @var BootstrapConfig
	 */
	private $bootstrapConfig;
	private $pics;
	private $widths = array();

	public function __construct(ProportionalImgComposer $xsPic, BootstrapConfig $bootstrapConfig) {
		$this->bootstrapConfig = $bootstrapConfig;
		$this->pics = array(self::RESERVED_BP => $xsPic);
	}

	private function assign($bpName, $arg) {
		unset($this->widths[$bpName]);
		unset($this->pics[$bpName]);

		if ($arg === null) {
			return;
		}

		if (is_int($arg)) {
			$this->widths[$bpName] = $arg;
			return;
		}

		if ($arg instanceof ProportionalImgComposer) {
			$this->pics[$bpName] = $arg;
			return;
		}

		ArgUtils::valType($arg, array('int', ProportionalImgComposer::class), true);
	}

	/**
	 * @param int|ImgComposer $arg
	 * @return \n2nutil\bootstrap\img\BsImgComposer
	 */
	public function sm($arg) {
		$this->assign('sm', $arg);
		return $this;
	}

	/**
	 * @param int|ImgComposer $arg
	 * @return \n2nutil\bootstrap\img\BsImgComposer
	 */
	public function md($arg) {
		$this->assign('md', $arg);
		return $this;
	}

	/**
	 * @param int|ImgComposer $arg
	 * @return \n2nutil\bootstrap\img\BsImgComposer
	 */
	public function lg($arg) {
		$this->assign('lg', $arg);
		return $this;
	}

	/**
	 * @param int|ImgComposer $arg
	 * @return \n2nutil\bootstrap\img\BsImgComposer
	 */
	public function xl($arg) {
		$this->assign('xl', $arg);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param int $width
	 * @return \n2nutil\bootstrap\img\BsImgComposer
	 */
	public function bp(string $name, int $width) {
		$this->assign($name, $width);
		return $this;
	}

	private function getBpWidth($bpName) {
		return $this->bootstrapConfig->getBreakpointValueByName($bpName);
		
// 		switch ($bpName) {
// 			case 'sm':
// 				return self::BP_SM;
// 			case 'md':
// 				return self::BP_MD;
// 			case 'lg':
// 				return self::BP_LG;
// 			case 'xl':
// 				return self::BP_XL;
// 			default:
// 				throw new IllegalStateException();
// 		}
	}

	/**
	 * {@inheritDoc}
	 * @see \n2n\impl\web\ui\view\html\img\ImgComposer::createImgSet()
	 */
	public function createImgSet(File $file = null, N2nContext $n2nContext): ImgSet {
		$imgSets = array();

		$curPic = $this->pics['xs']->copy();
		$curBaseWidth = $curPic->getWidth();
		$curWidths = $curPic->getWidths();
		$curSizes = array($curBaseWidth . 'px');
		$curMediaAttr = null;

		foreach ($this->bootstrapConfig->getBreakpointNames() as $bpName) {
			if (isset($this->widths[$bpName])) {
				foreach ($curWidths as $curWidth) {
					$curPic->widths(round($curWidth / $curBaseWidth * $this->widths[$bpName]));
				}

				$curSizes[] = '(min-width: ' . $this->getBpWidth($bpName) . 'px) ' . $this->widths[$bpName] . 'px';
				continue;
			}

			if (!isset($this->pics[$bpName])) continue;
				
			$curPic->sizes(implode(', ', array_reverse($curSizes)));
			$imgSets[$curMediaAttr] = $curPic->createImgSet($file, $n2nContext);
				
			$curPic = $this->pics[$bpName]->copy();
			$curBaseWidth = $curPic->getWidth();
			$curWidths = $curPic->getWidths();
			$curMediaAttr = '(min-width: ' . $this->getBpWidth($bpName) . 'px)';
			$curSizes = array($curMediaAttr . ' ' . $curBaseWidth . 'px');
		}

		$curPic->sizes(implode(', ', array_reverse($curSizes)));
		$imgSets[$curMediaAttr] = $curPic->createImgSet($file, $n2nContext);

		$defImgSet = array_shift($imgSets);
		foreach ($imgSets as $mediaAttr => $imgSet) {
			$this->combineImgSet($defImgSet, $imgSet, $mediaAttr);
		}
		return $defImgSet;
	}

	private function combineImgSet(ImgSet $imgSet, ImgSet $imgSet2, $mediaAttr) {
		foreach ($imgSet2->getImageSourceSets() as $imageSourceSet) {
			$imageSourceSet->setMediaAttr($mediaAttr);
			$imgageSourceSets = $imgSet->getImageSourceSets();
			array_unshift($imgageSourceSets, $imageSourceSet);
			$imgSet->setImageSourceSets($imgageSourceSets);
		}
		
		if ($imgSet->getDefaultWidthAttr() < $imgSet2->getDefaultWidthAttr()) {
			$imgSet->setDefaultSrcAttr($imgSet2->getDefaultSrcAttr());
			$imgSet->setDefaultAltAttr($imgSet2->getDefaultAltAttr());
			$imgSet->setDefaultWidthAttr($imgSet2->getDefaultWidthAttr());
			$imgSet->setDefaultHeightAttr($imgSet2->getDefaultHeightAttr());
		}
	}
}