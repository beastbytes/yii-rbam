---
# https://vitepress.dev/reference/default-theme-home-page
layout: home

hero:
  name: "Role Based Access Manager"
  text: "A web UI for Yii3 Role Based Access Control"
  tagline: RBAC simplified
  actions:
    - theme: brand
      text: Introduction
      link: /introduction
    - theme: alt
      text: Get Started
      link: /installation

features:
  - icon:
      src: /item.svg
    title: Manage RBAC Items
    details: Create, update, and delete RBAC Roles and Permissions
  - icon:
      src: /rule.svg
    title: Manage RBAC Rules
    details: Create, update, delete, attach, and remove RBAC Rules
  - icon:
      src: /assignment.svg
    title: Manage Role Assignments
    details: Assign and revoke Roles
  - icon:
      src: /diagram.svg
    title: Interactive diagrams
    details: Visualise a RBAC Item, its ancestors, and descendants
  - icon:
      src: /attribute.svg
    title: RBAC Item definition with PHP Attributes
    details: Define RBAC Items in application source code
  - icon:
      src: /access-check.svg
    title: Access checker middleware
    details: Check access in route definitions
  - icon:
      src: /i18n.svg  
    title: I18n
    details: RBAM can talk your language
---
