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
	private $controlAttrs;
	private $checkControlAttrs;

	public function __construct(OutfitConfig $outfitConfig = null, BsConfig $bsConfig = null,
			array $controlAttrs = array(), array $checkControlAttrs = array()) {

		$this->outfitConfig = $outfitConfig;
		$this->bsConfig = $bsConfig;
		$this->controlAttrs = !empty($controlAttrs) ? $controlAttrs : ['class' => 'form-control'];
		$this->checkControlAttrs = !empty($checkControlAttrs) ? $checkControlAttrs : ['class' => 'form-check-input'];
	}

	/**
	 * @param string $nature
	 * @return array
	 */
	public function createAttrs(int $nature): array {
		$attrs = array();

		if (null !== $this->outfitConfig) {
			$specialAttrs = $this->outfitConfig->getSAttrsForNature($nature);
			if ($specialAttrs !== null) {
				$attrs = HtmlUtils::mergeAttrs($attrs, $specialAttrs);
			}
		}

		if ($nature & self::NATURE_MAIN_CONTROL) {
			$mainControlAttrs = ($nature & self::NATURE_CHECK) ? $this->checkControlAttrs : $this->controlAttrs;
			$attrs = HtmlUtils::mergeAttrs($attrs, $mainControlAttrs);
		}

		if ($nature & self::NATURE_CHECK_LABEL) {
			$attrs = HtmlUtils::mergeAttrs($attrs, array('class' => 'form-check-label'));
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
	public function createElement(int $elemNature, array $attrs = null, $contents = ''): UiComponent {
		if ($elemNature & self::EL_NATRUE_CONTROL_ADDON_SUFFIX_WRAPPER) {
			$inputGroupAppend = new HtmlElement('span', array('class' => 'input-group-text'), $contents);
			return new HtmlElement('div', HtmlUtils::mergeAttrs(array('class' => 'input-group'), $attrs), $inputGroupAppend);
		}

		if ($elemNature & self::EL_NATURE_CONTROL_ADDON_WRAPPER) {
			$inputGroupAppend = HtmlElement('span', array('class' => 'input-group-text'), $contents);
			return new HtmlElement('div', HtmlUtils::mergeAttrs(array('class' => 'input-group-append'), $attrs), $inputGroupAppend);
		}

		if ($elemNature & self::EL_NATURE_CONTROL_ADD) {
			return new HtmlElement('button', HtmlUtils::mergeAttrs(
					$this->createAttrs(UiOutfitter::NATURE_BTN_SECONDARY), $attrs),
					new HtmlElement('i', array('class' => UiOutfitter::ICON_NATURE_ADD), $contents));

		}

		if ($elemNature & self::EL_NATURE_CONTROL_REMOVE) {
			return new HtmlElement('button', HtmlUtils::mergeAttrs(
				$this->createAttrs(UiOutfitter::NATURE_BTN_SECONDARY), $attrs),
				new HtmlElement('i', array('class' => UiOutfitter::ICON_NATURE_REMOVE), $contents));
		}

		if ($elemNature & self::EL_NATURE_ARRAY_ITEM_CONTROL) {
			$container = new HtmlElement('div', array('class' => 'row'), '');

			$container->appendLn(new HtmlElement('div', array('class' => 'col-auto'), $contents));
			$container->appendLn(new HtmlElement('div',
					array('class' => 'col-auto ' . MagCollection::CONTROL_WRAPPER_CLASS),
					$this->createElement(UiOutfitter::EL_NATURE_CONTROL_REMOVE,
					array('class' => MagCollection::CONTROL_REMOVE_CLASS), '')));

			return $container;
		}

		if ($elemNature & self::EL_NATURE_ARRAY_ITEM_CONTROL) {
			$summary = new HtmlElement('div', array('class' => 'rocket-impl-summary'), '');

			$container = new HtmlElement('div', null, $summary);

			$summary->appendLn(new HtmlElement('div', array('class' => 'col-auto'), $contents));
			$summary->appendLn(new HtmlElement('div', array('class' => 'col-auto ' . MagCollection::CONTROL_WRAPPER_CLASS),
				$this->createElement(UiOutfitter::EL_NATURE_CONTROL_REMOVE, array('class' => MagCollection::CONTROL_REMOVE_CLASS), '')));

			return $container;
		}

		if ($elemNature & self::EL_NATURE_CHECK_WRAPPER) {
			return new HtmlElement('div', array('class' => 'form-check'), $contents);
		}

		return new HtmlSnippet($contents);
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