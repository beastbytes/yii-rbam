import { defineConfig } from 'vitepress'

let currentYear = new Date().getFullYear();

// https://vitepress.dev/reference/site-config
export default defineConfig({
  lang: 'en-GB',
  base: '/yii-rbam/',
  srcDir: 'src',
  
  title: 'Role Based Access Manager',
  description: 'A web UI for Yii3 Role Based Access Control (RBAC)',
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'RBAM', link: '/web-ui/dashboard' }
    ],

    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Installation', link: '/installation' },
          { text: 'Configuration', link: '/configuration' },
          { text: 'Application Integration', link: '/application-integration' },
          { text: 'Tips', link: '/tips'}
        ]
      },
      {
        text: 'Web UI',
        items: [
          { text: 'Introduction', link: '/web-ui/web-ui' },
          { text: 'RBAC Initialisation', link: '/web-ui/rbac-initialisation' },
          { text: 'RBAM Dashboard', link: '/web-ui/dashboard' },
          { text: 'Manage Permissions', link: '/web-ui/manage-permissions' },
          { text: 'Manage Roles', link: '/web-ui/manage-roles' },
          { text: 'Manage Rules', link: '/web-ui/manage-rules' },
          { text: 'Manage Users', link: '/web-ui/manage-users' }
        ]
      },
      {
        text: 'Middleware',
        items: [
          { text: 'Access Checker', link: '/middleware/access-checker' }
        ]
      },
      {
        text: 'Defining RBAC in Source Code',
        items: [
          { text: 'PHP Attributes', link: '/attributes' },
          { text: 'Item Enums', link: '/item-enums' }
        ]
      },
      {
        text: 'Reference',
        items: [
          { text: 'RBAM RBAC Items', link: '/rbam-rbac-items' },
          { text: 'Item Interface API', link: '/api/item-interface' },
          { text: 'Item Trait API', link: '/api/item-trait' },
          { text: 'Prefix Attribute API', link: '/api/prefix-attribute' },
          { text: 'User Interface API', link: '/api/user-interface' },
          { text: 'User Repository Interface API', link: '/api/user-repository-interface' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/beastbytes/yii-rbam' },
    ],

    footer: {
      copyright: 'Copyright © 2026-${currentYear} BeaatBytes'
    }
  }
})
