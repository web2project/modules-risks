
# Risk Management Module v2.0

The Risk Management Module is a simple module that allows Users to log issues - or potential issues - against Projects or Tasks and attach notes/logs until the issue is resolved or mitigated.

It does not conform to the Project Management Book of Knowledge (PMBOK)'s Risk Management Process but does assist in tracking the results of the Identification stage, assisting in the Quantitative Risk Analysis, and the Risk Monitoring.  Feedback in improving the module and more closely adhering to this process is welcome.

## Open Issues:

* The CRisk->hook_preStore() method has a hardcoded Magic Number.  The RiskStatus of 2 is assumed to be "Closed" and when a Risk is updated to have this value, the Mitigation Date will be calculated and stored.  Moving out of this state will clear the Mitigation Date.  At a later point, this Magic Number should be removed.

## Updated in 2.0

* Major rewrite using core web2project v3.0 functionality
* Updated the setup class to take advantage of new w2p_Core_Setup functionality;
* Renamed the CRiskNotes class to CRisk_Notes to take advantage of our autoloader;
* Updated all classes (CRisk and CRiskNotes) to take advantage of the pre/post hooks, this allowed us to eliminate the store() and delete() methods;
* Updated all controllers (do_risk_aed.php and do_risk_note_aed.php) to use the w2p_Controllers_Base class;
* Updated the List and View views to use the HTMLHelper;
* Deleted lots of code;

## Install

1.  To install this module, please follow the standard module installation procedure.  Download the latest version from SourceForge and unzip this directory into your web2project/modules directory or (as of web2project v2.2) you can upload the zip file via the System Admin.
1.  Select to System Admin -> View Modules and you should see "Risks" near the bottom of the list.
1.  On the "Risks" row, select "install".  The screen should refresh.  Now select "hidden" and then "disabled" to make it display in your module navigation.

If you find this module particularly useful and would like to express gratitude, seek additional development, or have large piles of money that need a new home, please do not hesitate to contact CaseySoftware, LLC via webmaster@caseysoftware.com
