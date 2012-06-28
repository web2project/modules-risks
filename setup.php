<?php
if (!defined('W2P_BASE_DIR')){
  die('You should not access this file directly.');
}

$config = array();
$config['mod_name']         = 'Risks';              // name the module
$config['mod_version']      = '2.0';                // add a version number
$config['mod_directory']    = 'risks';              // tell web2project where to find this module
$config['mod_setup_class']  = 'CSetupRisks';        // the name of the PHP setup class (used below)
$config['mod_type']         = 'user';               // 'core' for modules distributed with w2p by standard, 'user' for additional modules
$config['mod_ui_name']      = $config['mod_name'];	// the name that is shown in the main menu of the User Interface
$config['mod_ui_icon']      = '';
$config['mod_description']  = 'Risk management';    // some description of the module
$config['mod_config']       = false;                // show 'configure' link in viewmods
$config['mod_main_class']   = 'CRisk';

$config['permissions_item_table'] = 'risks';
$config['permissions_item_field'] = 'risk_id';
$config['permissions_item_label'] = 'risk_name';

$config['requirements'] = array(
    array('require' => 'web2project',   'comparator' => '>=', 'version' => '3')
);

class CSetupRisks extends w2p_Core_Setup
{
    public function install()
    {
		$q = $this->_getQuery();
		$q->createTable('risks');
		$sql = '(
            `risk_id` int(10) unsigned NOT NULL auto_increment,
            `risk_name` varchar(50) default NULL,
            `risk_description` text,
            `risk_probability` tinyint(3) default 100,
            `risk_priority` int(10) default NULL,
            `risk_status` text default NULL,
            `risk_owner` int(10) default NULL,
            `risk_project` int(10) default NULL,
            `risk_task` int(10) default NULL,
            `risk_impact` int(10) default NULL,
            `risk_created` datetime NOT NULL,
            `risk_updated` datetime NOT NULL,
            `risk_mitigation_date` datetime NOT NULL,
            `risk_duration_type` tinyint(10) default 1,
            `risk_notes` text,
            PRIMARY KEY  (`risk_id`))
            ENGINE = MYISAM DEFAULT CHARSET=utf8 ';
		$q->createDefinition($sql);
		$q->exec();

		$q->clear();
		$q->createTable('risk_notes');
		$sql = '(
            `risk_note_id` int(11) NOT NULL auto_increment,
            `risk_note_risk` int(11) NOT NULL default \'0\',
            `risk_note_creator` int(11) NOT NULL default \'0\',
            `risk_note_date` datetime NOT NULL default \'0000-00-00 00:00:00\',
            `risk_note_description` text NOT NULL,
            PRIMARY KEY  (`risk_note_id`))
            ENGINE = MYISAM DEFAULT CHARSET=utf8 ';
		$q->createDefinition($sql);
		$q->exec();

        $i = 0;
        $impacts = array('Not Specified', 'Low', 'Medium', 'High', 'Super High');
        foreach ($impacts as $impact) {
            $q->clear();
            $q->addTable('sysvals');
            $q->addInsert('sysval_key_id', 1);
            $q->addInsert('sysval_title', 'RiskImpact');
            $q->addInsert('sysval_value', $impact);
            $q->addInsert('sysval_value_id', $i);
            $q->exec();
            $i++;
        }

        $i = 0;
        $probabilities = array('Not Specified', 'Low', 'Medium', 'High');
        foreach ($probabilities as $probability) {
            $q->clear();
            $q->addTable('sysvals');
            $q->addInsert('sysval_key_id', 1);
            $q->addInsert('sysval_title', 'RiskProbability');
            $q->addInsert('sysval_value', $probability);
            $q->addInsert('sysval_value_id', $i);
            $q->exec();
            $i++;
        }

        $i = 0;
        $statii = array('Not Specified', 'Open', 'Closed', 'Not Applicable');
        foreach ($statii as $status) {
            $q->clear();
            $q->addTable('sysvals');
            $q->addInsert('sysval_key_id', 1);
            $q->addInsert('sysval_title', 'RiskStatus');
            $q->addInsert('sysval_value', $status);
            $q->addInsert('sysval_value_id', $i);
            $q->exec();
            $i++;
        }

        return parent::install();
	}

	public function remove()
    {
        $q = $this->_getQuery();
		$q->dropTable('risks');
		$q->exec();
		$q->clear();
		$q->dropTable('risk_notes');
		$q->exec();

		$q->clear();
		$q->setDelete('sysvals');
		$q->addWhere("sysval_title = 'RiskImpact'");
        $q->exec();

		$q->clear();
		$q->setDelete('sysvals');
		$q->addWhere("sysval_title = 'RiskProbability'");
        $q->exec();

		$q->clear();
		$q->setDelete('sysvals');
		$q->addWhere("sysval_title = 'RiskStatus'");
        $q->exec();

        return parent::remove();
	}
}