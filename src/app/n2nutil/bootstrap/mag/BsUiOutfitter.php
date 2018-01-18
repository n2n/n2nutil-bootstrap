<?php
namespace n2nutil\bootstrap\mag;

use n2n\impl\web\ui\view\html\HtmlElement;
use n2n\impl\web\ui\view\html\HtmlUtils;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\web\dispatch\mag\MagCollection;
use n2n\web\dispatch\mag\UiOutfitter;
use n2n\web\dispatch\map\PropertyPath;
use n2n\web\ui\UiComponent;
use n2nutil\bootstrap\ui\BsConfig;
use n2n\impl\web\ui\view\html\HtmlSnippet;

class BsUiOutfitter implements UiOutfitter {
	private $bsConfig;
	private $outfitConfig;
	private $propertyPath;
	private $controlAttrs;
	private $checkControlAttrs;

	public function __construct(OutfitConfig $outfitConfig = null, BsConfig $bsConfig = null, 
			PropertyPath $propertyPath = null, array $controlAttrs = array(), array $checkControlAttrs = array()) {
		$this->outfitConfig = $outfitConfig;
		$this->bsConfig = $bsConfig;
		$this->propertyPath = $propertyPath;
		$this->controlAttrs = $controlAttrs;
		$this->checkControlAttrs = $checkControlAttrs;
	}

	/**
	 * @param string $nature
	 * @return array
	 */
	public function buildAttrs(int $nature): array {
		$attrs = array();
		if ($nature & self::NATURE_MAIN_CONTROL) {
			$attrs = ($nature & self::NATURE_CHECK) ? $this->checkControlAttrs : $this->controlAttrs;
		}

		if ($nature & self::NATURE_CHECK_LABEL) {
			$attrs = HtmlUtils::mergeAttrs($attrs, array('class' => 'form-check-label'));
		}

		if (null !== $this->outfitConfig) {
			$specialAttrs = $this->outfitConfig->getSAttrsForNature($nature);
			if ($specialAttrs !== null) {
				$attrs = HtmlUtils::mergeAttrs($attrs, $specialAttrs);
			}
		}

		if ($nature & self::NATURE_BTN_PRIMARY) {
			$attrs = HtmlUtils::mergeAttrs($attrs, array('class' => 'btn btn-primary mt-2'));
		}

		if ($nature & self::NATURE_BTN_SECONDARY) {
			$attrs = HtmlUtils::mergeAttrs($attrs, array('class' => 'btn btn-secondary'));
		}

		return $attrs;
	}

	/**
	 * @param int $elemNature
	 * @return HtmlElement
	 */
	public function buildElement(int $elemNature, array $attrs = null, $contents = null): UiComponent {
		if ($elemNature & self::EL_NATRUE_CONTROL_ADDON_SUFFIX_WRAPPER) {
			return new HtmlElement('div', HtmlUtils::mergeAttrs(array('class' => 'input-group'), $attrs), $contents);
		}

		if ($elemNature & self::EL_NATURE_CONTROL_ADDON_WRAPPER) {
			return new HtmlElement('span', HtmlUtils::mergeAttrs(array('class' => 'input-group-addon'), $attrs), $contents);
		}

		if ($elemNature & self::EL_NATURE_CONTROL_ADD) {
			return new HtmlElement('button', HtmlUtils::mergeAttrs(
					$this->buildAttrs(UiOutfitter::NATURE_BTN_SECONDARY), $attrs),
					new HtmlElement('i', array('class' => UiOutfitter::ICON_NATURE_ADD), $contents));
		}

		if ($elemNature & self::EL_NATURE_CONTROL_REMOVE) {
			return new HtmlElement('button', HtmlUtils::mergeAttrs(
				$this->buildAttrs(UiOutfitter::NATURE_BTN_SECONDARY), $attrs),
				new HtmlElement('i', array('class' => UiOutfitter::ICON_NATURE_REMOVE), $contents));
		}

		if ($elemNature & self::EL_NATURE_ARRAY_ITEM_CONTROL) {
			$container = new HtmlElement('div', array('class' => 'row'), '');

			$container->appendLn(new HtmlElement('div', array('class' => 'col-auto'), $contents));
			$container->appendLn(new HtmlElement('div',
					array('class' => 'col-auto ' . MagCollection::CONTROL_WRAPPER_CLASS),
					$this->buildElement(UiOutfitter::EL_NATURE_CONTROL_REMOVE, array('class' => MagCollection::CONTROL_REMOVE_CLASS), '')));

			return $container;
		}
	}

	public function createMagDispatchableView(PropertyPath $propertyPath = null, HtmlView $contextView): UiComponent {
		$bsChild = $this->bsConfig->getChild();
		$bs = (null !== $bsChild) ? $bsChild : $this->bsConfig;
		
		if ($this->outfitConfig === null) {
			return $contextView->getImport('\n2nutil\bootstrap\mag\bsMagForm.html',
					array('propertyPath' => $propertyPath, 'bs' => $bs, 'uiOutfitter' => $this, 'outfit' => null));
		}
		
		$outfitChild = $this->outfitConfig->getChild();
		$outfit = (null !== $outfitChild) ? $outfitChild : $this->outfitConfig;

		return $contextView->getImport('\n2nutil\bootstrap\mag\bsMagForm.html',
				array('propertyPath' => $propertyPath, 'bs' => $bs, 'uiOutfitter' => $this, 'outfit' => $outfit));
	}
}