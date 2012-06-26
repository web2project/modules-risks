<?php
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$perms = & $AppUI->acl();
if (!$perms->checkModuleItem($m, 'access')) {
    $AppUI->redirect('m=public&a=access_denied');
}

$tab = (int) w2PgetParam($_GET, 'tab', 0);

$riskStatus = array(-1 => $AppUI->_('All Risks')) + w2PgetSysVal('RiskStatus');
$durnTypes = array(1=>'Hours', 24=>'Days', 168=>'Weeks');

// setup the title block
$titleBlock = new w2p_Theme_TitleBlock( 'Risks', 'scales.png', $m, $m.$a );
// Use permissions check directly rather than $canEdit, because this 
// file can be included by other modules, in which case the $canEdit will
// have a different context.
if ($perms->checkModule($m, 'add')) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new risk').'">', '',
		'<form action="?m=risks&amp;a=addedit" method="post">', '</form>'
	);
}
$titleBlock->show();

$tabBox = new CTabBox("?m=$m", W2P_BASE_DIR . "/modules/$m/", $tab);
foreach ($riskStatus as $status) {
	$tabBox->add('vw_idx_risks', $AppUI->_($status));
}
$tabBox->show();