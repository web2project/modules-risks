<?php
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI, $task_id, $tab;

$project_id = (int) w2PgetParam($_GET, 'project_id', 0);

$tab--;

$risk = new CRisk();
$risks = $risk->getRisksByProject($project_id, $tab--);

$module = new w2p_Core_Module();
$fields = $module->loadSettings('risks', 'index_list');

$fieldList = array_keys($fields);
$fieldNames = array_values($fields);

$htmlHelper = new w2p_Output_HTMLHelper($AppUI);

$riskProbability = w2PgetSysVal( 'RiskProbability' );
$riskImpact = w2PgetSysVal( 'RiskImpact' );
$riskStatus = w2PgetSysVal( 'RiskStatus' );
$customLookups = array('risk_probability' => $riskProbability, 
    'risk_impact' => $riskImpact, 'risk_status' => $riskStatus);
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl list">
    <?php
    foreach ($fieldNames as $index => $name) { ?>
        <th nowrap="nowrap">
            <?php echo $AppUI->_($fieldNames[$index]); ?>
        </th>
    <?php }


    $prev_project = -1;
    foreach ($risks as $row) {
        if ($prev_project != (int) $row['project_id']) {
            echo '<tr><td colspan="15" style="background-color:#' . $row['project_color_identifier'] . '">
                <a href="?m=projects&a=view&amp;project_id=' . $row['project_id'] . '">
                    <font color="' . bestColor( $row["project_color_identifier"] ) . '">' . $row['project_name'] . '</font>&nbsp</a>
                </td></tr>';
        }
//TODO: center probability and impact
//TODO: when no task is specified, say so
//TODO: make sure mitigation date works
        echo '<tr>';
        $htmlHelper->stageRowData($row);
        foreach ($fieldList as $index => $column) {
            echo $htmlHelper->createCell($fieldList[$index], $row[$fieldList[$index]], $customLookups);
        }
        echo '</tr>';
        $prev_project = $row['project_id'];
    }
    ?>
</table>