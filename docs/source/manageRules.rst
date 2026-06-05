Manage Rules
============

Managing Rules consists of creating, updating, and deleting Rules.

Rules
-----

The Rules home page contains a button to create a new Rule and a paged list of Rules;
the following information is given for each Rule:

* Name of the Rule
* Description of the Rule - translated to the current locale (see :doc:`i18n`)
* Buttons to `View`, `Update`, or `Remove` the Rule

.. image:: /_images/rule_index.png

Rules

View a Rule
-----------

The Rule view page contains buttons to update the Rule and manage translations,
details of the Rule including the `execute()` method code, and - in tabs - Roles and Permissions that use the Rule.

.. image:: /_images/rule_view.png

Rule View

Translations
------------

To create or update translations for the Rule description, click the `Translations` button.

Translations are listed by locale. To translate a locale, click on the locale to expand it and complete the form.
Repeat for all required locales then click `Submit`.

.. note::

    The translation may not immediately show in the Rule view; this is due to the way Yii caches translations.

    Refresh the Rule view to see the translation.

.. image:: /_images/rule_translate.png

Translate Rule

Create a Rule
-------------

To create a Rule, click the `Create` button on the Rules index page then
complete the form. The form has the following fields:

* Name - The name of the Rule - Required
* Description - Description of the Rule - Required
* Code - Code for the `execute()` method of the Rule, the code must return a boolean; `true` to allow access or `false` to deny - Required

.. image:: /_images/rule_create.png

Create a Rule

Update a Rule
-------------

The fields and requirements are the same as those for creating a Rule except that the name can not be updated.

.. image:: /_images/rule_update.png

Update a Rule

Remove a Rule
-------------

To remove a Rule, click the Remove button for the Rule and confirm the removal in the dialog.
The Rule will be removed from RBAC Items using it.