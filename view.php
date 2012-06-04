<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$risk_id = (int) w2PgetParam($_GET, 'risk_id', 0);

// check permissions for this company
$perms = &$AppUI->acl();

$canView = $perms->checkModuleItem($m, 'view', $risk_id);
$canAdd = canAdd($m);
$canEdit = $perms->checkModuleItem($m, 'edit', $risk_id);
$canDelete = $perms->checkModuleItem($m, 'delete', $risk_id);

if (!$canView) {
	$AppUI->redirect( "m=public&a=access_denied" );
} 

$riskProbability = w2PgetSysVal( 'RiskProbability' );
$riskStatus = w2PgetSysVal( 'RiskStatus' );
$riskImpact = w2PgetSysVal( 'RiskImpact' );
$riskDuration = array(1=>'Hours', 24=>'Days', 168=>'Weeks');
$tab = $AppUI->processIntState('RiskVwTab', $_GET, 'tab', 0);
$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');
$format = $df . ' ' . $tf;

$risk = new CRisk();
$risk->loadFull($AppUI, $risk_id);

if (!$risk) {
	$AppUI->setMsg('Risk');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}

// setup the title block
$titleBlock = new CTitleBlock('View Risk', 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCell();
if ($canAdd) {
    $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('new risk') . '" />', '', '<form action="?m=risks&amp;a=addedit" method="post" accept-charset="utf-8">', '</form>');
}
$titleBlock->addCrumb('?m='.$m, 'risks list');
if ($canEdit) {
	$titleBlock->addCrumb('?m=risks&amp;a=addedit&amp;risk_id=' . $risk_id, 'edit this risk');
}
if ($canDelete) {
	$titleBlock->addCrumbDelete('delete risk', $canDelete, $msg);
}
$titleBlock->show();
?>
<script type="text/javascript">
function delIt(){
	var form = document.frmDelete;
	if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Risk', UI_OUTPUT_JS).'?';?>" )) {
		form.submit();
	}
}
</script>

<form name="frmDelete" action="?m=risks" method="post">
    <input type="hidden" name="dosql" value="do_risk_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="risk_id" value="<?php echo $risk_id; ?>" />
</form>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
    <tr>
        <td width="50%">
            <table width="100%" cellspacing="1" cellpadding="2">
                <tr>
                    <td nowrap="nowrap" colspan=2><strong><?php echo $AppUI->_('Details'); ?></strong></td>
                </tr>
                <?php if ($risk->risk_project) { ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project');?>:</td>
                    <td style="background-color:#<?php echo $risk->project_color_identifier; ?>">
                        <font color="<?php echo bestColor($risk->project_color_identifier); ?>">
                            <?php echo '<a href="?m=projects&amp;a=view&amp;project_id=' . $risk->risk_project . '">' . htmlspecialchars($risk->project_name, ENT_QUOTES) . '</a>'; ?>
                        </font>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($risk->risk_task) { ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task');?>:</td>
                    <td class="hilite">
                        <?php echo '<a href="?m=projects&amp;a=view&amp;task_id=' . $risk->risk_task . '">' . htmlspecialchars($risk->task_name, ENT_QUOTES) . '</a>'; ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Risk Name'); ?>:</td>
                    <td class="hilite"><strong><?php echo $risk->risk_name; ?></strong></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Owner');?>:</td>
                    <td class="hilite"><?php echo $risk->risk_owner_name;?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Probability'); ?>:</td>
                    <td class="hilite"><?php echo $riskProbability[$risk->risk_probability]; ?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Impact'); ?>:</td>
                    <td class="hilite"><?php echo $riskImpact[$risk->risk_impact]; ?></td>
                </tr>
                <tr>
                    <td align="right"><?php echo $AppUI->_('Risk Priority'); ?>:</td>
                    <td class="hilite"><?php echo $risk->risk_priority; ?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Status'); ?>:</td>
                    <td class="hilite"><?php echo $riskStatus[$risk->risk_status]; ?></td>
                </tr>
                <tr>
                    <td nowrap="nowrap" colspan="2"><strong><?php echo $AppUI->_('Dates and Targets'); ?></strong></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Mitigation Date');?>:</td>
                    <td class="hilite">
                        <?php
                            echo intval( $risk->risk_mitigation_date ) ? $AppUI->formatTZAwareTime($risk->risk_mitigation_date,  $format) : '-';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Create Date');?>:</td>
                    <td class="hilite">
                        <?php
                            echo intval( $risk->risk_created ) ? $AppUI->formatTZAwareTime($risk->risk_created, $format) : '-';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Update Date');?>:</td>
                    <td class="hilite">
                        <?php
                            echo intval( $risk->risk_updated ) ? $AppUI->formatTZAwareTime($risk->risk_updated, $format) : '-';
                        ?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" valign="top">
            <table cellspacing="1" cellpadding="2" border="0" width="100%">
                <tr><td></td></tr>
                <tr>
                    <td>
                        <strong><?php echo $AppUI->_('Description'); ?></strong><br />
                    </td>
                </tr>
                <tr>
                    <td class="hilite">
                        <?php echo w2p_textarea($risk->risk_description); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php

// Last parameter makes tab box javascript based.
$moddir = W2P_BASE_DIR . "/modules/$m/";
$tabBox = new CTabBox( "?m=$m&amp;a=view&amp;risk_id=$risk_id", '', $tab);
$tabBox->add($moddir.'vw_notes', $AppUI->_('Risk Notes' ));
$tabBox->add($moddir.'vw_note_add', $AppUI->_('Add Risk Note'));
$tabBox->show();