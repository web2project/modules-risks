<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI, $risk_id, $canView;

if (!$canView) {
	$AppUI->redirect("m=public&a=access_denied");
}

$module = new w2p_System_Module();
$fields = $module->loadSettings('risks', 'risk_view_notes');
$fieldList = array_keys($fields);
$fieldNames = array_values($fields);

$note = new CRisk_Note();
$items = $note->loadAll('risk_note_date', 'risk_note_risk = '. $risk_id);

?>
<table cellpadding="5" width="100%" class="tbl list risknotes">
    <?php
    echo '<tr>';
    foreach ($fieldNames as $index => $name) { ?>
        <th nowrap="nowrap">
            <?php echo $AppUI->_($fieldNames[$index]); ?>
        </th>
    <?php }
    echo '</tr>';

    $htmlHelper = new w2p_Output_HTMLHelper($AppUI);
    foreach($items as $row) {
        $htmlHelper->stageRowData($row);

        echo '<tr>';        
        foreach ($fieldList as $index => $column) {
            echo $htmlHelper->createCell($fieldList[$index], $row[$fieldList[$index]], $customLookups);
        }
        echo '</tr>';
    } ?>
</table>