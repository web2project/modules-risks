<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI, $risk, $canView;

if (!$canView) {
	$AppUI->redirect("m=public&a=access_denied");
}

$notes = $risk->getNotes($AppUI);
$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');

?>
<table cellpadding="5" width="100%" class="tbl">
    <tr>
        <th><?php echo $AppUI->_('Date'); ?></th>
        <th><?php echo $AppUI->_('User'); ?></th>
        <th><?php echo $AppUI->_('Note'); ?></th>
    </tr>
    <?php foreach($notes as $note) { ?>
    <tr>
        <td nowrap>
            <?php echo $AppUI->formatTZAwareTime($note['risk_note_date'], $df . ' ' . $tf); ?>
        </td>
        <td nowrap><?php echo $note['risk_note_owner']; ?></td>
        <td width="100%">
            <?php echo w2p_textarea($note['risk_note_description']); ?>
        </td>
    </tr>
    <?php } ?>
</table>