import { defineConfig } from 'vitepress'

export default defineConfig({
  title: "Laravel Social Auto Post",
  description: "A comprehensive Laravel package for automatic social media posting across 8 major platforms.",
  
  // Base path if deployed to GitHub pages (repo name)
  base: '/laravel-social-auto-post/',

  locales: {
    en: {
      label: 'English',
      lang: 'en',
      dir: 'ltr',
      themeConfig: {
        nav: [
          { text: 'Home', link: '/en/' },
          { text: 'Docs', link: '/en/installation' }
        ],
        sidebar: [
          {
            text: 'Getting Started',
            items: [
              { text: 'Introduction', link: '/en/' },
              { text: 'Installation', link: '/en/installation' },
              { text: 'Setup Platforms', link: '/en/setup-platforms' }
            ]
          },
          {
            text: 'Usage',
            items: [
              { text: 'Basic Usage', link: '/en/usage' },
              { text: 'Dynamic Credentials', link: '/en/dynamic-credentials' },
              { text: 'Advanced Usage', link: '/en/advanced' },
              { text: 'Comprehensive Examples', link: '/en/examples' }
            ]
          }
        ]
      }
    },
    ar: {
      label: 'العربية',
      lang: 'ar',
      dir: 'rtl',
      title: 'Laravel Social Auto Post',
      description: 'حزمة شاملة للارافيل للنشر التلقائي عبر 8 منصات مختلفة.',
      themeConfig: {
        nav: [
          { text: 'الرئيسية', link: '/ar/' },
          { text: 'التوثيق', link: '/ar/installation' }
        ],
        sidebar: [
          {
            text: 'البداية',
            items: [
              { text: 'مقدمة', link: '/ar/' },
              { text: 'التثبيت', link: '/ar/installation' },
              { text: 'إعداد المنصات', link: '/ar/setup-platforms' }
            ]
          },
          {
            text: 'الاستخدام',
            items: [
              { text: 'الاستخدام الأساسي', link: '/ar/usage' },
              { text: 'حسابات متعددة ديناميكية', link: '/ar/dynamic-credentials' },
              { text: 'استخدام متقدم', link: '/ar/advanced' },
              { text: 'أمثلة شاملة', link: '/ar/examples' }
            ]
          }
        ]
      }
    },
    tr: {
      label: 'Türkçe',
      lang: 'tr',
      themeConfig: {
        nav: [
          { text: 'Ana Sayfa', link: '/tr/' },
          { text: 'Belgeler', link: '/tr/installation' }
        ],
        sidebar: [
          {
            text: 'Başlangıç',
            items: [
              { text: 'Giriş', link: '/tr/' },
              { text: 'Kurulum', link: '/tr/installation' },
              { text: 'Platform Kurulumu', link: '/tr/setup-platforms' }
            ]
          },
          {
            text: 'Kullanım',
            items: [
              { text: 'Temel Kullanım', link: '/tr/usage' },
              { text: 'Dinamik Kimlik Bilgileri', link: '/tr/dynamic-credentials' },
              { text: 'Gelişmiş Kullanım', link: '/tr/advanced' },
              { text: 'Kapsamlı Örnekler', link: '/tr/examples' }
            ]
          }
        ]
      }
    },
    fr: {
      label: 'Français',
      lang: 'fr',
      themeConfig: {
        nav: [
          { text: 'Accueil', link: '/fr/' },
          { text: 'Docs', link: '/fr/installation' }
        ],
        sidebar: [
          {
            text: 'Commencer',
            items: [
              { text: 'Introduction', link: '/fr/' },
              { text: 'Installation', link: '/fr/installation' },
              { text: 'Configuration', link: '/fr/setup-platforms' }
            ]
          },
          {
            text: 'Utilisation',
            items: [
              { text: 'Utilisation de base', link: '/fr/usage' },
              { text: 'Identifiants Dynamiques', link: '/fr/dynamic-credentials' },
              { text: 'Utilisation Avancée', link: '/fr/advanced' },
              { text: 'Exemples Complets', link: '/fr/examples' }
            ]
          }
        ]
      }
    },
    es: {
      label: 'Español',
      lang: 'es',
      themeConfig: {
        nav: [
          { text: 'Inicio', link: '/es/' },
          { text: 'Documentación', link: '/es/installation' }
        ],
        sidebar: [
          {
            text: 'Empezando',
            items: [
              { text: 'Introducción', link: '/es/' },
              { text: 'Instalación', link: '/es/installation' },
              { text: 'Configuración de plataformas', link: '/es/setup-platforms' }
            ]
          },
          {
            text: 'Uso',
            items: [
              { text: 'Uso Básico', link: '/es/usage' },
              { text: 'Credenciales Dinámicas', link: '/es/dynamic-credentials' },
              { text: 'Uso Avanzado', link: '/es/advanced' },
              { text: 'Ejemplos Completos', link: '/es/examples' }
            ]
          }
        ]
      }
    },
    zh: {
      label: '简体中文',
      lang: 'zh',
      themeConfig: {
        nav: [
          { text: '首页', link: '/zh/' },
          { text: '文档', link: '/zh/installation' }
        ],
        sidebar: [
          {
            text: '开始',
            items: [
              { text: '介绍', link: '/zh/' },
              { text: '安装', link: '/zh/installation' },
              { text: '平台设置', link: '/zh/setup-platforms' }
            ]
          },
          {
            text: '使用',
            items: [
              { text: '基本使用', link: '/zh/usage' },
              { text: '动态凭证', link: '/zh/dynamic-credentials' },
              { text: '高级用法', link: '/zh/advanced' },
              { text: '综合示例', link: '/zh/examples' }
            ]
          }
        ]
      }
    }
  },

  themeConfig: {
    socialLinks: [
      { icon: 'github', link: 'https://github.com/hamzahassanm/laravel-social-auto-post' }
    ]
  }
})
