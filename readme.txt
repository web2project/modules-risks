Risk Management Module v1.0
CaseySoftware, LLC
webmaster@caseysoftware.com

The Risk Management Module is a simple module that allows Users to log issues 
- or potential issues - against Projects or Tasks and attach notes/logs until 
the issue is resolved or mitigated.

It does not conform to the Project Management Book of Knowledge (PMBOK)'s Risk 
Management Process but does assist in tracking the results of the 
Identification stage, assisting in the Quantitative Risk Analysis, and the 
Risk Monitoring.  Feedback in improving the module and more closely adhering 
to this process is welcome.

COMPATIBLE VERSIONS

=====================================

*  This module has been validated to work with web2project v2.2 and above and
has not been tested for compatibility with any previous versions.

*  If you are using any version of web2project prior to this, please upgrade
as soon as possible to address various security, usability, and functionality
issues.

*  This won't work with dotProject, don't even bother trying.

KNOWN/PREVIOUS ISSUES

=====================================

Open Issues:

*  The store() method on the Risks class has a hardcoded Magic Number.  The 
RiskStatus of 2 is assumed to be "Closed" and when a Risk is updated to have 
this value, the Mitigation Date will be calculated and stored.  Moving out of 
this state will clear the Mitigation Date.  At a later point, this Magic 
Number should be removed.

*  Additional reporting functionality would be useful.  Feedback in improving 
this module's reporting and more closely adhering to this process is welcome.

*  Since this is the first official release for web2project, there is
no upgrade path included in this release.  If this is an issue for you or your 
organization, please contact D. Keith Casey, Jr. at CaseySoftware, LLC to 
evaluate.

Planned:

*  Additional reporting!

*  Support for the Custom Field Editor within core Web2Project.

*  The module should be scrubbed of the inline html/php nastiness.

INSTALL

=====================================

1.  To install this module, please follow the standard module installation 
procedure.  Download the latest version from SourceForge and unzip
this directory into your web2project/modules directory or (as of web2project
v2.2) you can upload the zip file via the System Admin.

2.  Select to System Admin -> View Modules and you should see "Risks" near 
the bottom of the list.

3.  On the "Risks" row, select "install".  The screen should refresh.  Now 
select "hidden" and then "disabled" to make it display in your module 
navigation.

If you find this module particularly useful and would like to express 
gratitude or seek additional development, please do not hesitate to contact 
CaseySoftware, LLC via webmaster@caseysoftware.com