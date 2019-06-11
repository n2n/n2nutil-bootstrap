<?php
namespace n2nutil\bootstrap\config;

use n2n\util\type\attrs\Attributes;
use n2n\core\module\ConfigDescriberAdapter;
use n2n\web\dispatch\mag\MagCollection;
use n2n\core\N2N;
use n2n\impl\web\dispatch\mag\model\MagForm;
use n2n\web\dispatch\mag\MagDispatchable;
use n2n\util\type\attrs\LenientAttributeReader;
use n2n\util\type\CastUtils;
use n2n\impl\web\dispatch\mag\model\MagCollectionMag;
use n2n\impl\web\dispatch\mag\model\StringMag;
use n2n\impl\web\dispatch\mag\model\NumericMag;
use n2nutil\bootstrap\img\BsImgComposer;

class BootstrapDescriber extends ConfigDescriberAdapter {
	const ATTR_BREAKPOINTS_KEY = 'breakpoints';
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\core\module\ConfigDescriber::createMagDispatchable()
	 */
	public function createMagDispatchable(): MagDispatchable {
		$lar = new LenientAttributeReader($this->readCustomAttributes());
		
		$magCollection = new MagCollection();
		
		$breakpoints = $lar->getArray(self::ATTR_BREAKPOINTS_KEY, 'numeric');
		if (empty($breakpoints)) {
			$breakpoints = BootstrapConfig::getDefault()->getBreakpoints();
		}
		
		$bpMagCollection = new MagCollection();
		$bpCounter = 0;
		foreach ($breakpoints as $bpName => $bpValue) {
			$bpCounter++;
			$bpMagCollection->addMag($bpCounter, $this->createBreakpointMag($bpCounter, $bpName, $bpValue));
		}
		for ($i = 0; $i < 2; $i++) {
			$bpCounter++;
			$bpMagCollection->addMag($bpCounter, $this->createBreakpointMag($bpCounter, null, null));
		}
		
		$magCollection->addMag('breakpoints', new MagCollectionMag('Breakpoints', $bpMagCollection));
		
		return new MagForm($magCollection);
	}
	
	/**
	 * @param int $bpCounter
	 * @param string $bgName
	 * @param int $number
	 * @return \n2n\impl\web\dispatch\mag\model\MagCollectionMag
	 */
	private function createBreakpointMag(int $bpCounter, ?string $bgName, ?int $number) {
		$mg = new MagCollection();
		$mg->addMag('name', new StringMag('Name', $bgName));
		$mg->addMag('value', new NumericMag('Pixels', $number));
		
		return new MagCollectionMag('Breakpoint ' . $bpCounter, $mg);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\core\module\ConfigDescriber::saveMagDispatchable()
	 */
	public function saveMagDispatchable(MagDispatchable $magDispatchable) {
		$bpsMag = $magDispatchable->getMagCollection()->getMagByPropertyName('breakpoints');
		
		$breakpoints = [];
		
		CastUtils::assertTrue($bpsMag instanceof MagCollectionMag);
		foreach ($bpsMag->getMagCollection()->getMagWrappers() as $magWrapper) {
			$bpMag = $magWrapper->getMag();
			CastUtils::assertTrue($bpMag instanceof MagCollectionMag);
			
			$breakpointValues = $bpMag->getMagCollection()->readValues();
			if (empty($breakpointValues['name']) || empty($breakpointValues['value'])
					|| $breakpointValues['name'] == BsImgComposer::RESERVED_BP) {
				continue;
			}
			
			$breakpoints[$breakpointValues['name']] = (int) $breakpointValues['value'];
		}
		
		$this->writeCustomAttributes(new Attributes([self::ATTR_BREAKPOINTS_KEY => $breakpoints]));
	}
	
    /**
     * {@inheritDoc}
     * @see \n2n\core\module\ConfigDescriber::buildCustomConfig()
     */
	public function buildCustomConfig() {
		$attributes = $this->readCustomAttributes();
		
		$breakpoints = $attributes->optArray(self::ATTR_BREAKPOINTS_KEY, 'numeric', []);
		if (empty($breakpoints)) {
			return BootstrapConfig::getDefault();
		}
		
		return new BootstrapConfig($breakpoints);
	}
}