<?php
namespace n2nutil\bootstrap\ui;

use n2n\web\dispatch\mag\Mag;
use n2n\web\dispatch\mag\UiOutfitter;
use n2n\web\ui\UiComponent;
use n2n\web\dispatch\Dispatchable;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\impl\web\ui\view\html\HtmlElement;
use n2n\impl\web\ui\view\html\HtmlUtils;
use n2n\web\dispatch\map\PropertyPath;
use n2n\impl\web\ui\view\html\HtmlSnippet;
use n2n\l10n\DynamicTextCollection;
use n2nutil\bootstrap\mag\OutfitComposer;
use n2nutil\bootstrap\mag\BsUiOutfitter;
use n2nutil\bootstrap\mag\OutfitConfig;
use n2nutil\jquery\datepicker\DatePickerHtmlBuilder;
use n2n\reflection\ArgUtils;

class BsFormHtmlBuilder {
	private $view;
	private $formHtml;
	private $ariaFormHtml;
	private $datePickerHtml;
	
	private $globalBsConfig;
	private $inline = false;

	/**
	 * BsFormHtmlBuilder constructor.
	 * @param HtmlView $view
	 * @param BsComposer|BsConfig|null $bsComposer
	 */
	public function __construct(HtmlView $view, $bsComposer = null) {
		$this->view = $view;
		$this->formHtml = $view->getFormHtmlBuilder();
		$this->ariaFormHtml = $view->getAriaFormHtmlBuilder();
		$this->datePickerHtml = new DatePickerHtmlBuilder($view);

		if (null === $bsComposer) return;

		ArgUtils::valType($bsComposer, array(BsComposer::class, BsConfig::class));

		if ($bsComposer instanceof BsComposer) {
			$this->globalBsConfig = $bsComposer->toBsConfig();
		} else if ($bsComposer instanceof BsConfig) {
			$this->globalBsConfig = $bsComposer;
		}

		
// @todo auto detect already added bs libraries 
// 		$view->getHtmlBuilder()->meta()->addLibrary(new BootstrapLibrary());
	}
	
	public function open(Dispatchable $dispatchableObject, string $enctype = null, $method = null, 
			array $attrs = null, $action = null) {
		$this->inline = false;
		return $this->formHtml->open($dispatchableObject, $enctype, $method, $attrs, $action);
	}
	
	public function openInline(Dispatchable $dispatchableObject, string $enctype = null, $method = null, 
			array $attrs = null, $action = null) {
		$attrs = HtmlUtils::mergeAttrs(array('class' => 'form-inline'), (array) $attrs);
		$uiOpen = $this->open($dispatchableObject, $enctype, $method, $attrs, $action);
		$this->inline = true;
		return $uiOpen;
	}
	
	public function close() {
		$this->formHtml->close();
	}
	
	private function createPropertyPath($propertyExpression) {
		return $this->formHtml->meta()->createPropertyPath($propertyExpression, true);
	}

	/**
	 * @param null $bsComposer
	 * @return BsComposer|BsConfig|null
	 */
	private function createBsConfig($bsComposer = null) {
		if ($bsComposer instanceof BsComposer) {
			return $bsComposer->toBsConfig($this->globalBsConfig);
		}
		
		if ($this->globalBsConfig !== null) {
			return $this->globalBsConfig;
		}
		
		return (new BsComposer())->toBsConfig();
	}
	
	public function staticGroup($propertyExpression = null, $fixedValue = null, BsComposer $bsComposer = null, $label = null) {
		return $this->view->out($this->getStaticGroup($propertyExpression, $fixedValue, $bsComposer, $label));
	}
	
	public function getStaticGroup($propertyExpression = null, $fixedValue = null, BsComposer $bsComposer = null, $label = null) {
		ArgUtils::assertTrue(null !== $propertyExpression || null !== $fixedValue);
		$propertyPath = null;
		$value = $fixedValue;
		
		if (null !== $propertyExpression) {
			$propertyPath = $this->createPropertyPath($propertyExpression);
			if (null === $value) {
				$value = $this->formHtml->meta()->getMapValue($propertyExpression);
			}
		}
		
		$bsConfig = $this->createBsConfig($bsComposer);
		$uiControl = new HtmlElement('p', array('class' => 'form-control-static'), $value);
		
		return $this->createUiFormGroup($propertyPath,
				$this->createUiLabel($propertyPath, $bsConfig, $label),
				$uiControl, $bsConfig);
	}
	
	public function inputGroup($propertyExpression, BsComposer $bsComposer = null, $label = null, 
			string $type = null) {
		$this->view->out($this->getInputGroup($propertyExpression, $bsComposer, $label, $type));
	}
	
	public function getInputGroup($propertyExpression, BsComposer $bsComposer = null, $label = null, 
			string $type = null) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig);
		
		return $this->createUiFormGroup($propertyPath,
				$this->createUiLabel($propertyPath, $bsConfig, $label),
				$this->ariaFormHtml->getInput($propertyPath, $bsConfig->isRequired(), $controlAttrs, $type),
				$bsConfig);
	}
	
	public function selectGroup($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null,
			bool $multiple = false) {
		$this->view->out($this->getSelectGroup($propertyExpression, $options,$bsComposer, $label, $multiple));
	}
	
	public function getSelectGroup($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null,
			bool $multiple = false) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig, null, null, false);
		
		return $this->createUiFormGroup($propertyPath,
				$this->createUiLabel($propertyPath, $bsConfig, $label),
				$this->ariaFormHtml->getSelect($propertyPath, $options, $bsConfig->isRequired(), $controlAttrs, $multiple),
				$bsConfig, true);
	}
	
	public function datePickerGroup($propertyExpression = null, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getDatePickerGroup($propertyExpression, $bsComposer, $label));
	}
	
	public function getDatePickerGroup($propertyExpression = null, BsComposer $bsComposer = null, $label = null) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig);
		
		return $this->createUiFormGroup($propertyPath,
				$this->createUiLabel($propertyPath, $bsConfig, $label),
				$this->datePickerHtml->getFormDatePicker($propertyPath, $controlAttrs),
				$bsConfig);
	}
	
	public function inputFileWithLabelGroup($propertyExpression = null, BsComposer $bsComposer = null, $label = null,
			array $fileLabelAttrs = null) {
		$this->view->out($this->getInputFileWithLabelGroup($propertyExpression, $bsComposer, $label));
	}
	
	public function getInputFileWithLabelGroup($propertyExpression = null, BsComposer $bsComposer = null, $label = null,
			array $fileLabelAttrs = null) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig, null, 'form-control-file');
		
		return $this->createUiFormGroup($propertyPath,
				$this->createUiLabel($propertyPath, $bsConfig, $label),
				$this->ariaFormHtml->getInputFileWithLabel($propertyPath, $bsConfig->isRequired(), $controlAttrs, $fileLabelAttrs),
				$bsConfig);
	}
	
	public function inputPasswordGroup($propertyExpression, BsComposer $bsComposer = null, $label = null, 
			bool $secret = true) {
		$this->view->out($this->getInputPasswordGroup($propertyExpression, $bsComposer, $label, $secret));
	}
	
	public function getInputPasswordGroup($propertyExpression, BsComposer $bsComposer = null, $label = null, 
			bool $secret = true) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig, array('autocomplete' => 'off'));
		
		return $this->createUiFormGroup($propertyPath, 
				$this->createUiLabel($propertyPath, $bsConfig, $label), 
				$this->ariaFormHtml->getInput($propertyPath, $bsConfig->isRequired(), $controlAttrs, 'password', 
						$secret),
				$bsConfig);
	}

	public function inputCheckboxCheck($propertyExpression, $value, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getInputCheckboxCheck($propertyExpression, $value, $bsComposer, $label));
	}
	
	public function getInputCheckboxCheck($propertyExpression, $value, BsComposer $bsComposer = null, $label = null) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		
		$controlAttrs = $this->createFormCheckInputAttrs($propertyPath, $bsConfig);
		
		return $this->createUiFormCheck($propertyPath, $bsConfig, $label,
				$this->ariaFormHtml->getInputCheckbox($propertyPath, $value, $bsConfig->isRequired(), $controlAttrs), true, false);
	}
	
	public function inputRadiosCheck($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getInputRadiosCheck($propertyExpression, $options, $bsComposer, $label));
	}
	
	public function getInputRadiosCheck($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null) {
		return $this->createUiRadiosCheck($propertyExpression, $options, $bsComposer, $label, false);
	}
	
	public function inputRadiosCheckInline($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getInputRadiosCheckInline($propertyExpression, $options, $bsComposer, $label));
	}
	
	public function getInputRadiosCheckInline($propertyExpression, array $options, $bsComposer, $label = null) {
		return $this->createUiRadiosCheck($propertyExpression, $options, $bsComposer, $label, true);
	}
	
	private function createUiRadiosCheck($propertyExpression, array $options, BsComposer $bsComposer = null, $label, bool $inline) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		
		// change back to createUiLegend() tag after flexbox fieldset bugfix
		$uiLegend = $this->createUiLabel($propertyPath, $bsConfig, $label, false);
		
		$controlAttrs = $this->createFormCheckInputAttrs($propertyPath, $bsConfig);
		$uiControl = new HtmlSnippet();
		foreach ($options as $optionValue => $optionLabel) {
			$uiControl->appendLn(
					$this->createUiFormCheck($propertyPath, $bsConfig, $optionLabel,
							$this->ariaFormHtml->getInputRadio($propertyPath, $optionValue, $controlAttrs),
							false, $inline));
		}
		
		return $this->createUiFormGroup($propertyPath, $uiLegend, $uiControl, $bsConfig, false);
	}
	
	public function inputCheckboxesCheck($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getInputCheckboxesCheck($propertyExpression, $options, $bsComposer, $label));
	}
	
	public function getInputCheckboxesCheck($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null) {
		return $this->createUiCheckboxesCheck($propertyExpression, $options, $bsComposer, $label, false);
	}
	
	public function inputCheckboxesCheckInline($propertyExpression, array $options, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getInputCheckboxesCheckInline($propertyExpression, $options, $bsComposer, $label));
	}
	
	public function getInputCheckboxesCheckInline($propertyExpression, array $options, $bsComposer, $label = null) {
		return $this->createUiCheckboxesCheck($propertyExpression, $options, $bsComposer, $label, true);
	}
	
	private function createUiCheckboxesCheck($propertyExpression, array $options, BsComposer $bsComposer = null, $label, bool $inline) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
	
		// change back to legend tag after flexbox fieldset bugfix
		$uiLegend = $this->createUiLegend($propertyPath, $bsConfig, $label);
	
		$controlAttrs = $this->createFormCheckInputAttrs($propertyPath, $bsConfig);
		$uiControl = new HtmlSnippet();
		foreach ($options as $optionValue => $optionLabel) {
			$fieldPropertyPath = $propertyPath->fieldExt($optionValue);
			$uiControl->appendLn(
					$this->createUiFormCheck($fieldPropertyPath, $bsConfig, $optionLabel,
							$this->ariaFormHtml->getInputCheckbox($fieldPropertyPath, $optionValue, false, $controlAttrs),
							false, $inline));
		}
	
		return $this->createUiFormGroup($propertyPath, $uiLegend, $uiControl, $bsConfig, false);
	}
	
	public function inputCheckboxGroup($propertyExpression, $value, $checkboxLabel = null, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getInputCheckboxGroup($propertyExpression, $value, $checkboxLabel, $bsComposer, $label));
	}
	
	public function getInputCheckboxGroup($propertyExpression, $value, $checkboxLabel = null, BsComposer $bsComposer = null, $label = null) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig);
		
		return $this->createUiFormGroup($propertyPath,
				$this->createUiLabel($propertyPath, $bsConfig, $label, true, ''),
				$this->getInputCheckboxCheck($propertyExpression, $value, $bsComposer, (null === $checkboxLabel) ? '': $checkboxLabel),
				$bsConfig);
	}
	
	public function textareaGroup($propertyExpression, BsComposer $bsComposer = null, $label = null) {
		$this->view->out($this->getTextarea($propertyExpression, $bsComposer, $label));
	}
	
	public function getTextarea($propertyExpression, BsComposer $bsComposer = null, $label = null) {
		$propertyPath = $this->createPropertyPath($propertyExpression);
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig);
		
		return $this->createUiFormGroup($propertyPath,
				$this->createUiLabel($propertyPath, $bsConfig, $label),
				$this->ariaFormHtml->getTextarea($propertyPath, $bsConfig->isRequired(), $controlAttrs),
				$bsConfig);
	}

	public function buttonSubmitGroup($methodName = null, $label = null, BsComposer $bsComposer = null) {
		$this->view->out($this->getButtonSubmitGroup($methodName, $label, $bsComposer));
	}
	
	public function getButtonSubmitGroup($methodName = null, $label = null, BsComposer $bsComposer = null) {
		$bsConfig = $this->createBsConfig($bsComposer);
		$controlAttrs = HtmlUtils::mergeAttrs(array('class' => 'btn btn-primary'), $bsConfig->getControlAttrs());
		
		return $this->createUiFormGroup(null, null, 
				$this->formHtml->getButtonSubmit($methodName, $label, $controlAttrs),
				$bsConfig);
	}
	
	private function createUiFormCheck(PropertyPath $propertyPath, BsConfig $bsConfig, $label, UiComponent $uiControl, 
			bool $displayErrors, bool $inline) {
		$formCheckClass = ($inline ? 'form-check-inline' : 'form-check');
		$labelAttrs = $bsConfig->getLabelAttrs();
		$labelAttrs = HtmlUtils::mergeAttrs($labelAttrs, array('class' => 'form-check-label'));
		
		if ($displayErrors && $this->formHtml->meta()->hasErrors($propertyPath)) {
			$formCheckClass .= ' is-invalid';
			$uiMessage = $this->ariaFormHtml->getMessage($propertyPath, 'div', array('class' => 'invalid-feedback'));
		}
		
		if ($inline) {
			$labelAttrs = HtmlUtils::mergeAttrs($labelAttrs, array('class' => $formCheckClass));
			
		}
		
		$uiLabel = new HtmlElement('label', $labelAttrs);
		$uiLabel->appendLn($uiControl);
		
		if ($label === null) {
			$label = $this->formHtml->meta()->getLabel($propertyPath);
		}
		
// 		if ($bsConfig->isLabelHidden()) {
// 			$uiLabel->appendLn(new HtmlElement('span', array('class' => 'sr-only'), $label));
// 		}

		$uiLabel->appendLn($label);
		
		if ($inline) return $uiLabel;
		
		$uiFormGroup = new HtmlElement('div', array('class' => $formCheckClass));
		$uiFormGroup->appendLn($uiLabel);
		
		return $uiFormGroup;
	}

	/**
	 * @param PropertyPath|null $propertyPath
	 * @param UiComponent|null $uiLabel
	 * @param UiComponent $uiControl
	 * @param BsConfig $bsConfig
	 * @param bool $fieldset
	 * @return HtmlElement
	 */
	private function createUiFormGroup(PropertyPath $propertyPath = null, UiComponent $uiLabel = null,
			UiComponent $uiControl, BsConfig $bsConfig, bool $fieldset = false) {
		$rowClassNames = $bsConfig->getRowClassNames();
		$groupAttrs = $bsConfig->getGroupAttrs();

		$formGroupClassNames = array();
		if (!isset($groupAttrs['class'])) {
			$formGroupClassNames[] = 'form-group';
			if (!$this->inline && $rowClassNames !== null) {
				$formGroupClassNames[] = 'row';
			}
		}
		
		$uiMessage = null;
		if ($propertyPath !== null && $this->formHtml->meta()->hasErrors($propertyPath)) {
			$uiMessage = $this->ariaFormHtml->getMessage($propertyPath, 'div', array('class' => 'invalid-feedback'));
		}
		
		$uiFormGroup = new HtmlElement(($fieldset ? 'fieldset' : 'div'),
				HtmlUtils::mergeAttrs(array('class' => implode(' ', $formGroupClassNames)), $groupAttrs));
		$uiFormGroup->appendLn();
		
		if ($uiLabel !== null) $uiFormGroup->appendLn($uiLabel);
		
		$uiContainer = $uiFormGroup;
		
		if ($this->inline || $rowClassNames === null) {
			$uiFormGroup->appendLn($uiControl);
			if ($uiMessage !== null) $uiFormGroup->appendLn($uiMessage);
		} else {
			$className = $rowClassNames['containerClassName'];
			if ($uiLabel === null && !$bsConfig->isLabelHidden()) {
				$className .= ' ' . $rowClassNames['labelOffsetClassName'];
			}
			$uiFormGroup->appendLn($uiContainer = new HtmlElement('div', array('class' => $className), $uiControl));
			if ($uiMessage !== null) $uiContainer->appendLn($uiMessage);
		}
		
		if ($propertyPath !== null && null !== ($helpText = $bsConfig->getHelpText())) {
			$uiContainer->appendLn(new HtmlElement('small', array(
					'class' => 'form-text text-muted',
					'id' => $this->buildHelpTextId($propertyPath)), $helpText));
		}
		
		return $uiFormGroup;
	}
	
	private function createUiLegend(PropertyPath $propertyPath, BsConfig $bsConfig, string $label = null) {
		if ($label === null) {
			$label = $this->formHtml->meta()->getLabel($propertyPath);
		}
		
		if ($bsConfig->isRequired()) {
			$dtc = new DynamicTextCollection('n2n\impl\web\dispatch', $this->view->getN2nContext()->getN2nLocale());
			$label = new HtmlSnippet($label, PHP_EOL, new HtmlElement('abbr',
					array('title' => $dtc->translate('aria_required_label')), '*'));
		}
		
		return new HtmlElement('legend', $this->createLabelAttrs($propertyPath, $bsConfig, 'col-form-legend'), $label);
	}
	
	private function createUiLabel(PropertyPath $propertyPath, BsConfig $bsConfig, $label, bool $applyFor = true, string $className = 'col-form-label') {
		if ($applyFor) {
			return $this->ariaFormHtml->getLabel($propertyPath, $bsConfig->isRequired(), $label, 
					$this->createLabelAttrs($propertyPath, $bsConfig, $className));
		}
		
		if ($label === null) {
			$label = $this->formHtml->meta()->getLabel($propertyPath);
		}
		
		if ($bsConfig->isRequired()) {
			$dtc = new DynamicTextCollection('n2n\impl\web\dispatch', $this->view->getN2nContext()->getN2nLocale());
			$label = new HtmlSnippet($label, PHP_EOL, new HtmlElement('abbr',
					array('title' => $dtc->translate('aria_required_label')), '*'));
		}
		
		return new HtmlElement('label', $this->createLabelAttrs($propertyPath, $bsConfig, $className), $label);
	}
	
	private function createLabelAttrs(PropertyPath $propertyPath, BsConfig $bsConfig, string $className) {
		$rowClassNames = $bsConfig->getRowClassNames();
		$attrs = $bsConfig->getLabelAttrs();
		
		if (!$this->inline && $rowClassNames !== null) {
			$className .= ' ' . $rowClassNames['labelClassName'] /*. ' col-form-label'*/;
		} /*else if ($this->formHtml->meta()->hasErrors($propertyPath)) {
			$className = 'col-form-label';
		}*/
		
		if ($bsConfig->isLabelHidden()) {
			$className .= ' sr-only';
		}
		
		if ($className !== null) {
			$attrs = HtmlUtils::mergeAttrs($attrs, array('class' => $className));
		}
		
		return $attrs;
	}
	
	private function createFormCheckInputAttrs(PropertyPath $propertyPath, BsConfig $bsConfig) {
		$attrs = $bsConfig->getControlAttrs();
		
		return HtmlUtils::mergeAttrs($attrs, array('class' => 'form-check-input'), true);
	}
	
	private function createFormControlAttrs(PropertyPath $propertyPath, BsConfig $bsConfig, 
			array $additionalAttrs = null, string $className = null, $applyPlaceholder = true) {
		$attrs = $bsConfig->getControlAttrs();
		
		if ($additionalAttrs !== null) {
			$attrs = HtmlUtils::mergeAttrs($additionalAttrs, $attrs);
		}
		
		if ($applyPlaceholder) {
			if (null !== ($placeholder = $bsConfig->getPlaceholder())) {
				$attrs['placeholder'] = $placeholder;
			} else if ($bsConfig->isAutoPlaceholderUsed()) {
				$attrs['placeholder'] = $this->formHtml->meta()->getLabel($propertyPath);	
			}
		}

		if (null !== $bsConfig->getHelpText() && !$this->formHtml->meta()->hasErrors($propertyPath, false)) {
			$attrs['aria-describedby'] = $this->buildHelpTextId($propertyPath);
		}

		if (null === $className) {
			$className = 'form-control';
		}

		if ($this->formHtml->meta()->isDispatched()) {
			$className .= ' is-' . ($this->formHtml->meta()->hasErrors($propertyPath) ? 'invalid' : 'valid');
		}

		return HtmlUtils::mergeAttrs($attrs, array('class' => $className), true);
	}
	
	private $ids = array();
	
	private function buildHelpTextId(PropertyPath $propertyPath) {
		$key = (string) $propertyPath;
		if (isset($this->ids[$key])) {
			return $this->ids[$key];
		}
		return $this->ids[$key] = $this->formHtml->meta()->getForm()->buildId($propertyPath, 'helptext');
	}

	// adventure zone

	/**
	 * @param null $propertyExpression
	 * @param BsComposer|BsConfig|null $bsComposer
	 * @param OutfitComposer|OutfitConfig|null $outfitComposer
	 */
	public function magGroup($propertyExpression = null, $bsComposer = null, $outfitComposer = null) {
		$this->view->out($this->getMagGroup($propertyExpression, $bsComposer, $outfitComposer));
	}

	/**
	 * @param null $propertyExpression
	 * @param BsComposer|BsConfig|null $bsComposer
	 * @param OutfitComposer|OutfitConfig|null $outfitComposer
	 * @return HtmlElement
	 */
	public function getMagGroup($propertyExpression = null, $bsComposer = null, $outfitComposer = null) {

		$bsConfig = $this->createBsConfig($bsComposer);

		$propertyPath = $this->createPropertyPath($propertyExpression);

		$outfitConfig = null;
		if ($outfitComposer instanceof OutfitConfig) {
			$outfitConfig = $outfitComposer;
		} elseif ($outfitComposer !== null) {
			$outfitConfig = $outfitComposer->toConfig();
		}

		$controlAttrs = array();
		$checkControlAttrs = array();
		if ($bsComposer !== null) {
			$controlAttrs = $this->createFormControlAttrs($propertyPath, $bsConfig);
			$checkControlAttrs = $this->createFormCheckInputAttrs($propertyPath, $bsConfig);
		}

		$magWrapper = $this->formHtml->meta()->lookupMagWrapper($propertyPath);
		$mag = $magWrapper->getMag();
		$containerAttrs = $magWrapper->getContainerAttrs($this->view);

		$bsUiOutfitter = new BsUiOutfitter($outfitConfig, $bsConfig, $controlAttrs, $checkControlAttrs);
		$nature = $mag->getNature();
        test($nature);
		$uiLabel = null;
		if ($nature & Mag::NATURE_GROUP) {
			$uiLabel = $this->createUiLegend($propertyPath, $bsConfig, $mag->getLabel($this->view->getN2nLocale()));
			$uiLabel->setAttrs(HtmlUtils::mergeAttrs($uiLabel->getAttrs(), $bsUiOutfitter->createAttrs(UiOutfitter::NATURE_LEGEND)));
		} else if (!($nature & Mag::NATURE_LABELLESS)) {
			$uiLabel = $this->createUiLabel($propertyPath, $bsConfig, $mag->getLabel($this->view->getN2nLocale()));
		}

		$uiControl = $magWrapper->getMag()->createUiField($propertyPath, $this->view, $bsUiOutfitter);

		$htmlElement = $this->createUiFormGroup($propertyPath, $uiLabel, $uiControl, $bsConfig);
		$htmlElement->setAttrs(HtmlUtils::mergeAttrs($containerAttrs, $htmlElement->getAttrs()));

		if ($nature & Mag::NATURE_GROUP) {
			$htmlElement = new HtmlElement('fieldset', null, $htmlElement);
		}

		return $htmlElement;
	}

// 	public function getFormGroupWithCheckboxes($propertyExpression, array $options,
// 			$label = null, $required = false, FormGroupConfig $formGroupConfig = null, $inline = false) {
// 				$controlRaw = '';
// 				$controlAttrs = array('class' => 'control-label ' . ($inline ? 'checkbox-inline' : 'checkbox'));
// 				foreach ($options as $value => $labelDesc) {
// 					$controlContainerLabel = new HtmlElement('label', $controlAttrs,
// 							$this->ariaFormHtml->getInputCheckbox($propertyExpression . '[' . $value .']', $value,
// 									$this->improveControlAttrs(array(), $formGroupConfig, null, false), null, null, $required));
// 					$controlContainerLabel->appendContent($labelDesc);
// 					$controlRaw .= $controlContainerLabel->getContents();
// 				}
// 				$elemLabel = null;
// 				if (null !== $label && !($label instanceof UiComponent)) {
// 					$elemLabel = new HtmlElement('label',
// 							$this->determineLabelAttrs($formGroupConfig), $label);
// 					$this->ariaFormHtml->applyLabelAttrs($elemLabel, $required);
// 				}
// 				return $this->createUiFormGroup($elemLabel,
// 						new Raw($controlRaw), $propertyExpression, $required, $formGroupConfig);
// 	}
	
// 	public function formGroupWithCheckboxes($propertyExpression, array $options, $label = null,
// 			$required = false, FormGroupConfig $formGroupConfig = null, $inline = false) {
// 				$this->view->out($this->getFormGroupWithCheckboxes($propertyExpression, $options, $label, $required,
// 						$formGroupConfig, $inline));
// 	}
	
// 	public function getFormGroupWithRadioButtons($propertyExpression, array $options,
// 			$label = null, $required = false, FormGroupConfig $formGroupConfig = null, $inline = false) {
// 				$controlRaw = '';
// 				$controlAttrs = array('class' => 'control-label ' . ($inline ? 'radio-inline' : 'radio'));
// 				foreach ($options as $value => $labelDesc) {
// 					$controlContainerLabel = new HtmlElement('label', $controlAttrs,
// 							$this->ariaFormHtml->getInputRadio($propertyExpression, $value,
// 									$this->improveControlAttrs(array(), $formGroupConfig, null, false),
// 									$required));
// 					$controlContainerLabel->appendContent($labelDesc);
// 					$controlRaw .= $controlContainerLabel->getContents();
// 				}
// 				$elemLabel = null;
// 				if (null !== $label && !($label instanceof UiComponent)) {
// 					$elemLabel = new HtmlElement('label',
// 							$this->determineLabelAttrs($formGroupConfig), $label);
// 					$this->ariaFormHtml->applyLabelAttrs($elemLabel, $required);
// 				}
// 				return $this->createUiFormGroup($elemLabel, new Raw($controlRaw), $propertyExpression, $required, $formGroupConfig);
// 	}
	
// 	public function formGroupWithRadioButtons($propertyExpression, array $options, $label = null,
// 			$required = false, FormGroupConfig $formGroupConfig = null, $inline = false) {
// 				$this->view->out($this->getFormGroupWithRadioButtons($propertyExpression, $options, $label, $required,
// 						$formGroupConfig, $inline));
// 	}
	
// 	public function getFormGroupWithUneditableInput($propertyExpression = null, $label = null, $printedValue = null,
// 			FormGroupConfig $formGroupConfig = null) {
// 				$value = ($printedValue === null) ?  $this->formHtml->getValue($propertyExpression) : $printedValue;
// 				$controllAttrs = $this->improveControlAttrs(array('class' => 'uneditable-input form-control'), $formGroupConfig, null, false);
// 				return $this->createUiFormGroup(new HtmlElement('label', $this->determineLabelAttrs($formGroupConfig), $label),
// 						new HtmlElement('span', $controllAttrs, $value), $propertyExpression, false, $formGroupConfig);
// 	}
	
// 	public function formGroupWithUneditableInput($propertyExpression = null, $label = null, $printedValue = null,
// 			FormGroupConfig $formGroupConfig = null) {
// 				$this->view->out($this->getFormGroupWithUneditableInput($propertyExpression, $label, $printedValue,
// 						$formGroupConfig));
// 	}
	


// 	public function getFormGroupWithDatePicker($propertyExpression, $label = null, $required = false,
// 			FormGroupConfig $formGroupConfig = null) {
// 				$controlAttrs = $this->improveControlAttrs(
// 						array('class' => 'form-control'), $formGroupConfig, $label);
// 				return $this->createUiFormGroup(
// 						$this->createLabel($propertyExpression, $label, $required, $formGroupConfig),
// 						$this->ariaFormHtml->getDatePicker($propertyExpression, $controlAttrs, $required),
// 						$propertyExpression, $required, $formGroupConfig);
// 	}
	
// 	public function formGroupWithDatePicker($propertyExpression, $label = null, $required = false,
// 			FormGroupConfig $formGroupConfig = null) {
// 				$this->view->out($this->getFormGroupWithDatePicker($propertyExpression, $label, $required, $formGroupConfig));
// 	}
	
// 	public function getFormGroupWithAutoCompletion($propertyExpression, $label = null,
// 			array $options = null, $required = false, FormGroupConfig $formGroupConfig = null,
// 			$initialValueClosure = null) {
	
// 				$autoCompletionAttrs = array('class' => 'form-control');
// 				$initialValue = null;
// 				if (null !== $initialValueClosure
// 						&& null !== ($value = $this->formHtml->getValue($propertyExpression))) {
// 							$magicMethodInvoker = new MagicMethodInvoker(new \ReflectionFunction($initialValueClosure));
// 							$magicMethodInvoker->setParamValue('initialValue', $value);
// 							$initialValue = $magicMethodInvoker->invoke();
// 						}
// 						if (null !== $initialValue) {
// 							$autoCompletionAttrs['data-initial-value'] = $initialValue;
// 						}
	
// 						return $this->createUiFormGroup(
// 								$this->createLabel($propertyExpression, $label, $required, $formGroupConfig),
// 								$this->ariaFormHtml->getAutoCompletion($propertyExpression, $this->improveControlAttrs(
// 										$autoCompletionAttrs, $formGroupConfig, $label), $options, $required),
// 								$propertyExpression, $required, $formGroupConfig);
// 	}
	
// 	public function formGroupWithAutocompletion($propertyExpression, $label = null,
// 			array $options = null, $required = false, FormGroupConfig $formGroupConfig = null,
// 			$initialValueClosure = null) {
// 				$this->view->out($this->getFormGroupWithAutoCompletion($propertyExpression, $label,
// 						$options, $required, $formGroupConfig, $initialValueClosure));
// 	}
}