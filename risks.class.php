<?php

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

    public function loadFull(CAppUI $AppUI, $risksId) {
        global $AppUI;

        $q = $this->_getQuery();
        $q->addTable('risks', 'r');
        $q->addQuery('r.*');
        $q->addQuery('p.project_name, p.project_color_identifier');
        $q->addWhere('risk_id = ' . (int) $risksId);
        $q->leftJoin('projects', 'p', 'project_id = risk_project');

        $q->addQuery('t.task_name');
        $q->leftJoin('tasks', 't', 'task_id = risk_task');

        $q->addQuery('CONCAT_WS(\' \',contact_first_name,contact_last_name) as risk_owner_name');
		$q->leftJoin('users', 'u', 'user_id = risk_owner');
		$q->leftJoin('contacts', 'con', 'contact_id = user_contact');
        $q->loadObject($this, true, false);
    }

    public function check() {
        $errorArray = array();
        $baseErrorMsg = get_class($this) . '::store-check failed - ';

        if ('' == trim($this->risk_name)) {
            $errorArray['risk_name'] = $baseErrorMsg . 'risk name is not set';
        }
        if ('' == trim($this->risk_description)) {
            $errorArray['risk_description'] = $baseErrorMsg . 'risk description is not set';
        }
        if (0 == (int) $this->risk_owner) {
            $errorArray['risk_owner'] = $baseErrorMsg . 'risk owner is not set';
        }

        return $errorArray;
	}

	public function store(CAppUI $AppUI)
    {
        $perms = $AppUI->acl();
        $stored = false;

        $errorMsgArray = $this->check();
        if (count($errorMsgArray) > 0) {
          return $errorMsgArray;
        }

        $q = $this->_getQuery();
        $this->risk_updated = $q->dbfnNowWithTZ();
        $this->risk_mitigation_date = (2 == $this->risk_status) ? $q->dbfnNowWithTZ() : '';
//echo '<pre>'; print_r($this); die();
        if ($this->risk_id && $perms->checkModuleItem('risks', 'edit', $this->risk_id)) {
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }
        if (0 == $this->risk_id && $perms->checkModuleItem('risks', 'add')) {
            $this->risk_created = $q->dbfnNowWithTZ();
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }

        return $stored;
	}

	public function delete(CAppUI $AppUI) {
        $perms = $AppUI->acl();

        if ($perms->checkModuleItem('risks', 'delete', $this->risk_id)) {
          if ($msg = parent::delete()) {
              return $msg;
          }
          return true;
        }
        return false;
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

    public function getNotes(CAppUI $AppUI) {
        $results = array();
        $perms =& $AppUI->acl();

        if ($perms->checkModuleItem('risks', 'view', $this->risk_id)) {
            $q = $this->_getQuery();
            $q->addQuery('risk_notes.*');
            $q->addQuery("CONCAT(contact_first_name, ' ', contact_last_name) as risk_note_owner");
            $q->addTable('risk_notes');
            $q->leftJoin('users', 'u', 'risk_note_creator = user_id');
            $q->leftJoin('contacts', 'c', 'user_contact = contact_id');
            $q->addWhere('risk_note_risk = ' . (int) $this->risk_id);
            $results = $q->loadList();
        $projObj = new CProject();
        $projObj->setAllowedSQL($this->_AppUI->user_id, $q, null, 'p');
        if ($project_id > 0 && $this->_perms->checkModuleItem('projects', 'view', $project_id)) {
            $q->addWhere("r.risk_project = $project_id");
        }

        return $results;
    }
    public function storeNote(CAppUI $AppUI) {
        $perms =& $AppUI->acl();

        if ($this->link_id && $perms->checkModuleItem('risks', 'edit', $this->risk_id)) {
            $q = new DBQuery;
            $this->risk_note_date = $q->dbfnNow();
            addHistory('risks', $this->risk_id, 'update', $this->risk_name, $this->risk_id);
            $stored = true;
        }
    }
    public function deleteNote() {
        
    }

    public function getTasks(CAppUI $AppUI, $projectId) {
        $results = array();
        $perms = $AppUI->acl();

        if ($perms->checkModule('tasks', 'view')) {
            $q = $this->_getQuery();
            $q->addQuery('t.task_id, t.task_name');
            $q->addTable('tasks', 't');
            $q->addWhere('task_project = ' . (int) $projectId);
            $results = $q->loadHashList('task_id');
        }
        return $results;
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