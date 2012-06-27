<?php

class CRisk_Note extends w2p_Core_BaseObject {
    public $risk_note_id = null;
    public $risk_note_risk = null;
    public $risk_note_creator = null;
    public $risk_note_date = null;
    public $risk_note_description = '';

	public function __construct() {
		parent::__construct('risk_notes', 'risk_note_id', 'risks');
	}
}