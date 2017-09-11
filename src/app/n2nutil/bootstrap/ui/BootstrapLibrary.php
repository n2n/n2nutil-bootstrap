<?php
namespace n2nutil\bootstrap\ui;

use n2n\impl\web\ui\view\html\LibraryAdapter;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\impl\web\ui\view\html\HtmlBuilderMeta;
use n2nutil\jquery\JQueryLibrary;

class BootstrapLibrary  extends LibraryAdapter {
	
	const VERSION = '4.0.0-alpha.6';
	
	private $loadBootstrapCss;
	private $loadJs;
	
	public function __construct(bool $loadBootstrapCss = true, bool $loadJs = true) {
		$this->loadBootstrapCss = $loadBootstrapCss;
		$this->loadJs = $loadJs;
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
			$htmlMeta->addLibrary(new JQueryLibrary(3, true));
			$htmlMeta->bodyEnd()->addJs('js/tether.min.js', 'n2nutil\bootstrap');
			$htmlMeta->bodyEnd()->addJs('dist/js/bootstrap.min.js', 'n2nutil\bootstrap');
		}
	}
}