Manage Rules
============

Managing Rules consists of creating, updating, and deleting Rules.

Rules
-----

The Rules home page contains a button to create a new Rule and a paged list of Rules;
the following information is given for each Rule:

* Name of the Rule
* Description of the Rule - either as entered or in the current locale (see :doc:`i18n`)
* Buttons to `View`, `Update`, or `Remove` the Rule

View a Rule
-----------

The Rule view page contains buttons to update the Rule and manage translations,
details of the Rule including the `execute()` method code, and - tabs - Roles and Permissions that use the Rule

Translations
------------

Translations are listed by locale. To translate a locale, click on the locale to expand it and complete the form.
Repeat for all required locales then click `Submit`.

Create a Rule
-------------

To create a Rule, click the `Create` button on the Rules index page then
complete the form. The form has the following fields:

* Name - The name of the Rule - Required
* Description - Description of the Rule - Optional, *must* be left blank if using translations
* Code - Code for the `execute()` method of the Rule, the code must return a boolean; `true` to allow access or `false` to deny - Required

Update a Rule
-------------

The fields and requirements are the same as those for creating a Rule.

Remove a Rule
-------------

To remove a Rule, click the Remove button for the Rule and confirm the removal in the dialog.
The Rule will be removed from RBAC.