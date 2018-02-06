<?php
namespace n2nutil\bootstrap\ui;

use n2n\impl\web\ui\view\html\LibraryAdapter;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\impl\web\ui\view\html\HtmlBuilderMeta;
use n2nutil\jquery\JQueryLibrary;

class BootstrapLibrary  extends LibraryAdapter {
	
	const VERSION = '4.0.0-beta.3';
	
	private $loadBootstrapCss;
	private $loadJs;
	private $bodyEnd;
	
	public function __construct(bool $loadBootstrapCss = true, bool $loadJs = true, bool $bodyEnd = true) {
		$this->loadBootstrapCss = $loadBootstrapCss;
		$this->loadJs = $loadJs;
		$this->bodyEnd = $bodyEnd;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\impl\web\ui\view\html\Library::apply()
	 */
	public function apply(HtmlView $view, HtmlBuilderMeta $htmlMeta) {
		if ($this->loadBootstrapCss) {
			$htmlMeta->addCss('dist/css/bootstrap.min.css', null, 'n2nutil\bootstrap');
		}
		
		if ($this->loadJs) {
			$htmlMeta->addLibrary(new JQueryLibrary(3, $this->bodyEnd));
			if ($this->bodyEnd) {
				$htmlMeta = $htmlMeta->bodyEnd();
			}
			$htmlMeta->addJs('assets/js/vendor/popper.min.js', 'n2nutil\bootstrap');
			$htmlMeta->addJs('dist/js/bootstrap.min.js', 'n2nutil\bootstrap');
		}
	}
}