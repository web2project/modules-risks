<?php

/*
 * TODO: Deleting a Risk should also delete all the attached Risk Notes.
 */
class CRisk extends w2p_Core_BaseObject {
	public $risk_id = 0;
	public $risk_project = 0;
	public $risk_task = 0;
	public $risk_owner = 0;
	public $risk_name = '';
	public $risk_description = '';
	public $risk_probability = 0;
	public $risk_priority = 0;
	public $risk_status = 0;
	public $risk_impact = 0;
    public $risk_created = NULL;
    public $risk_updated = NULL;
	public $risk_mitigation_date = NULL;

	public function __construct() {
		parent::__construct('risks', 'risk_id');
	}

    public function loadFull($riskId) {
        $q = $this->_getQuery();
        $q->addTable('risks', 'r');
        $q->addQuery('r.*');
        $q->addQuery('p.project_name, p.project_color_identifier');
        $q->addWhere('risk_id = ' . (int) $riskId);
        $q->leftJoin('projects', 'p', 'project_id = risk_project');

        $q->addQuery('t.task_name');
        $q->leftJoin('tasks', 't', 'task_id = risk_task');

        $q->addQuery('contact_display_name as risk_owner_name');
		$q->leftJoin('users', 'u', 'user_id = risk_owner');
		$q->leftJoin('contacts', 'con', 'contact_id = user_contact');
        $q->loadObject($this, true, false);
    }

    public function isValid()
    {
        $baseErrorMsg = get_class($this) . '::store-check failed - ';

        if ('' == trim($this->risk_name)) {
            $this->_error['risk_name'] = $baseErrorMsg . 'risk name is not set';
        }
        if ('' == trim($this->risk_description)) {
            $this->_error['risk_description'] = $baseErrorMsg . 'risk description is not set';
        }
        if (0 == (int) $this->risk_owner) {
            $this->_error['risk_owner'] = $baseErrorMsg . 'risk owner is not set';
        }

        return (count($this->_error)) ? false : true;
    }

    protected function hook_preStore() {
        $q = $this->_getQuery();
        $this->risk_updated = $q->dbfnNowWithTZ();
        $this->risk_mitigation_date = (2 == $this->risk_status) ? $q->dbfnNowWithTZ() : '';
        $this->risk_owner = $this->_AppUI->user_id;

        parent::hook_preStore();
    }

    protected function hook_preCreate() {
        $q = $this->_getQuery();
        $this->risk_created = $q->dbfnNowWithTZ();
        parent::hook_preCreate();
    }

    public function getRisksByProject($project_id, $status = -1) {
        $q = $this->_getQuery();
        $q->addQuery('r.*');
        $q->addTable('risks', 'r');
        if ($status > -1) {
            $q->addWhere('r.risk_status = ' . $status);
        }

        $q->addQuery('p.project_id, p.project_name, p.project_color_identifier, p.project_company');
        $q->leftJoin('projects', 'p', 'p.project_id = r.risk_project');

        $q->addQuery('contact_display_name as risk_owner');
        $q->leftJoin('users', 'u', 'risk_owner = user_id');
        $q->leftJoin('contacts', 'c', 'user_contact = contact_id');

        $projObj = new CProject();
        $projObj->setAllowedSQL($this->_AppUI->user_id, $q, null, 'p');
        if ($project_id > 0 && $this->_perms->checkModuleItem('projects', 'view', $project_id)) {
            $q->addWhere("r.risk_project = $project_id");
        }

        return $q->loadList();
    }

    public function hook_search() {
        $search['table'] = 'risks';
        $search['table_alias'] = 'r';
        $search['table_module'] = 'risks';
        $search['table_key'] = $search['table_alias'].'.risk_id'; // primary key in searched table
        $search['table_link'] = 'index.php?m=risks&a=view&risk_id='; // first part of link
        $search['table_title'] = 'Risks';
        $search['table_orderby'] = 'risk_name';
        $search['search_fields'] = array('risk_name', 'risk_description', 'risk_note_description');
        $search['display_fields'] = $search['search_fields'];
        $search['table_joins'] = array(array('table' => 'risk_notes',
            'alias' => 'rn', 'join' => 'r.risk_id = rn.risk_note_risk'));

        return $search;
    }
}