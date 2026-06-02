RBAC Tips
=========

RBAC Item Naming Convention
---------------------------

It is strongly suggested to use some kind of naming convention for application RBAC items to make management simpler,
particularly in larger applications.

RBAM Naming Convention
++++++++++++++++++++++

RBAM Permission and Role names use the following convention:

`rbam.{section}.{task}`

where:

* `rbam` indicates it is an RBAM item
* {section} is one of `item`, `rule`, or `user` (section and the trailing '.' are omitted for top-level items)
* {task} describes what the item does, e.g. `manager`, `view`, `update`, etc.

For example, the Permission for updating Permissions and Roles (items) is `rbam.item.update`,
and the Role that gives that Permission is `rbam.item.manager`. The `rbam.admin` Role is an example of a top level item.

RBAM item descriptions simply have `.description` appended to the name; e.g. `rbam.admin.description`

Using a hierarchical naming convention such as this helps keep items organised.
Descriptive names can be shown using translations, though translating item descriptions may mean this is not necessary.

RBAC Item Enums
+++++++++++++++

Using Enums to define RBAC items helps to ensure a consistent naming convention. See :doc:`itemEnums` for details.