<?php
	$view = \n2n\impl\web\ui\view\html\HtmlView::view($view);
	$formHtml = \n2n\impl\web\ui\view\html\HtmlView::formHtml($view);

	$bs = $view->getParam('bs', false);
	$view->assert($bs === null || $bs instanceof \n2nutil\bootstrap\ui\BsComposer || $bs instanceof \n2nutil\bootstrap\ui\BsConfig);

	$outfit = $view->getParam('outfit', false);
	$view->assert($outfit === null || $outfit instanceof \n2nutil\bootstrap\mag\OutfitComposer  || $outfit instanceof \n2nutil\bootstrap\mag\OutfitConfig);

	$bsFormHtml = new \n2nutil\bootstrap\ui\BsFormHtmlBuilder($view, $bs);
?>
<?php if (!$formHtml->meta()->isFormOpen()): ?>

<?php endif ?>

<?php $formHtml->meta()->objectProps(null, function () use ($bs, $bsFormHtml, $outfit) { ?>
	<?php $bsFormHtml->magGroup(null, $bs, $outfit) ?>
<?php }) ?>