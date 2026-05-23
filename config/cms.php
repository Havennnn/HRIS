<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Pages
    |--------------------------------------------------------------------------
    |
    | Define the pages that exist in the system. These are seeded into the
    | database by `php artisan piacore:sync-cms`. Admins edit the content of
    | these pages — they cannot create or delete them.
    |
    | Each page references a template below. The template defines what fields
    | the page has and which Vue component renders it publicly.
    |
    | Status is managed by the admin in the panel (draft → publish toggle).
    | The model defaults to draft (0) when synced.
    |
    */

    'pages' => [
        'home' => [
            'title'    => 'Home',
            'slug'     => 'home',
            'template' => 'home',
        ],
        'about-us' => [
            'title'    => 'About Us',
            'slug'     => 'about-us',
            'template' => 'about',
        ],
        'contact-us' => [
            'title'    => 'Contact Us',
            'slug'     => 'contact-us',
            'template' => 'contact',
        ],
        'faq' => [
            'title'    => 'FAQ',
            'slug'     => 'faq',
            'template' => 'faq',
        ],
        'privacy-policy' => [
            'title'    => 'Privacy Policy',
            'slug'     => 'privacy-policy',
            'template' => 'default',
        ],
        'careers' => [
            'title'    => 'Careers',
            'slug'     => 'careers',
            'template' => 'careers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Content
    |--------------------------------------------------------------------------
    |
    | Content that appears globally (header, footer, etc.) rather than on a
    | specific page. Stored in the pages table with reserved slugs (prefixed
    | with underscore). Rendered on every page via the public API.
    |
    */

    'globals' => [
        'header' => [
            'title'    => 'Header',
            'slug'     => '_header',
            'template' => 'header',
        ],
        'footer' => [
            'title'    => 'Footer',
            'slug'     => '_footer',
            'template' => 'footer',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Each template defines what fields its pages have. The admin editor
    | generates a dynamic form from the field definitions. The 'component'
    | value is the Vue component used for public rendering.
    |
    | Supported field types:
    |   text     → Single-line text input
    |   textarea → Multi-line text input
    |   select   → Dropdown — options managed by admin in the UI
    |   file     → File upload — use 'accept' to restrict MIME types
    |   repeater → Repeatable group of sub-fields (like FAQ items, team members)
    |
    */

    'templates' => [

        // ─── Default (generic text page) ────────────────────────────────

        'default' => [
            'label'       => 'Default Page',
            'description' => 'A simple page with title and content.',
            'component'   => 'Cms/Templates/Default',
            'fields'      => [
                'content' => ['type' => 'textarea', 'label' => 'Content', 'required' => true],
            ],
        ],

        // ─── Home ──────────────────────────────────────────────────────

        'home' => [
            'label'       => 'Home Page',
            'description' => 'Landing page with hero, features, and call-to-action.',
            'component'   => 'Cms/Templates/Default',
            'fields'      => [
                'hero_heading'   => ['type' => 'text',     'label' => 'Hero Heading',        'required' => true],
                'hero_subheading' => ['type' => 'text',     'label' => 'Hero Subheading'],
                'hero_image'     => ['type' => 'file',     'label' => 'Hero Background',     'accept' => 'image/*'],
                'hero_cta_text'  => ['type' => 'text',     'label' => 'CTA Button Text'],
                'hero_cta_link'  => ['type' => 'text',     'label' => 'CTA Button Link'],
                'features'       => [
                    'type'   => 'repeater',
                    'label'  => 'Features',
                    'fields' => [
                        'icon'        => ['type' => 'text',     'label' => 'Icon Name'],
                        'title'       => ['type' => 'text',     'label' => 'Title',       'required' => true],
                        'description' => ['type' => 'textarea',  'label' => 'Description'],
                    ],
                ],
                'about_summary'  => ['type' => 'textarea', 'label' => 'About Summary'],
            ],
        ],

        // ─── About ─────────────────────────────────────────────────────

        'about' => [
            'label'       => 'About Us',
            'description' => 'Company information page.',
            'component'   => 'Cms/Templates/About',
            'fields'      => [
                'hero_heading' => ['type' => 'text',     'label' => 'Hero Heading',  'required' => true],
                'hero_image'   => ['type' => 'file',     'label' => 'Hero Image',    'required' => true, 'accept' => 'image/*'],
                'body'         => ['type' => 'textarea',  'label' => 'Body Content',  'required' => true],
                'mission'      => ['type' => 'text',     'label' => 'Mission Statement'],
                'vision'       => ['type' => 'text',     'label' => 'Vision Statement'],
                'team'         => [
                    'type'   => 'repeater',
                    'label'  => 'Team Members',
                    'fields' => [
                        'name'   => ['type' => 'text', 'label' => 'Name',     'required' => true],
                        'role'   => ['type' => 'text', 'label' => 'Role'],
                        'photo'  => ['type' => 'file', 'label' => 'Photo',    'accept' => 'image/*'],
                        'bio'    => ['type' => 'text', 'label' => 'Bio'],
                    ],
                ],
            ],
        ],

        // ─── Contact ───────────────────────────────────────────────────

        'contact' => [
            'label'       => 'Contact Us',
            'description' => 'Contact information and location.',
            'component'   => 'Cms/Templates/Location',
            'fields'      => [
                'heading'     => ['type' => 'text',     'label' => 'Heading',        'required' => true],
                'address'     => ['type' => 'textarea',  'label' => 'Address',        'required' => true],
                'phone'       => ['type' => 'text',     'label' => 'Phone Number'],
                'email'       => ['type' => 'text',     'label' => 'Email Address'],
                'map_url'     => ['type' => 'text',     'label' => 'Map Embed URL',  'placeholder' => 'https://maps.google.com/...'],
                'image'       => ['type' => 'file',     'label' => 'Location Photo', 'accept' => 'image/*'],
                'business_hours' => [
                    'type'   => 'repeater',
                    'label'  => 'Business Hours',
                    'fields' => [
                        'day'     => ['type' => 'text', 'label' => 'Day',   'required' => true],
                        'hours'   => ['type' => 'text', 'label' => 'Hours', 'required' => true],
                    ],
                ],
            ],
        ],

        // ─── FAQ ───────────────────────────────────────────────────────

        'faq' => [
            'label'       => 'FAQ',
            'description' => 'Frequently asked questions.',
            'component'   => 'Cms/Templates/Default',
            'fields'      => [
                'intro' => ['type' => 'textarea', 'label' => 'Intro Text'],
                'items' => [
                    'type'   => 'repeater',
                    'label'  => 'FAQ Items',
                    'fields' => [
                        'question' => ['type' => 'text',     'label' => 'Question', 'required' => true],
                        'answer'   => ['type' => 'textarea',  'label' => 'Answer',   'required' => true],
                    ],
                ],
            ],
        ],

        // ─── Careers ───────────────────────────────────────────────────

        'careers' => [
            'label'       => 'Careers',
            'description' => 'Job openings and company culture.',
            'component'   => 'Cms/Templates/Default',
            'fields'      => [
                'hero_heading' => ['type' => 'text',    'label' => 'Hero Heading',     'required' => true],
                'hero_text'    => ['type' => 'textarea', 'label' => 'Hero Text'],
                'perks'        => [
                    'type'   => 'repeater',
                    'label'  => 'Perks & Benefits',
                    'fields' => [
                        'title'       => ['type' => 'text',     'label' => 'Title',       'required' => true],
                        'description' => ['type' => 'textarea',  'label' => 'Description'],
                        'icon'        => ['type' => 'text',     'label' => 'Icon Name'],
                    ],
                ],
                'culture_text' => ['type' => 'textarea', 'label' => 'Culture & Values'],
            ],
        ],

        // ─── Header (global) ───────────────────────────────────────────

        'header' => [
            'label'       => 'Header',
            'description' => 'Global site header — logo, nav links, CTAs.',
            'component'   => 'Cms/Templates/Default',
            'fields'      => [
                'logo_text'    => ['type' => 'text',     'label' => 'Logo Text'],
                'logo_image'   => ['type' => 'file',     'label' => 'Logo Image',      'accept' => 'image/*'],
                'nav_links'    => [
                    'type'   => 'repeater',
                    'label'  => 'Navigation Links',
                    'fields' => [
                        'label' => ['type' => 'text', 'label' => 'Label', 'required' => true],
                        'url'   => ['type' => 'text', 'label' => 'URL',   'required' => true],
                    ],
                ],
                'cta_text'     => ['type' => 'text', 'label' => 'CTA Button Text'],
                'cta_link'     => ['type' => 'text', 'label' => 'CTA Button Link'],
            ],
        ],

        // ─── Footer (global) ───────────────────────────────────────────

        'footer' => [
            'label'       => 'Footer',
            'description' => 'Global footer — links, socials, copyright.',
            'component'   => 'Cms/Templates/Footer',
            'fields'      => [
                'company_name'    => ['type' => 'text',     'label' => 'Company Name',      'required' => true],
                'tagline'         => ['type' => 'text',     'label' => 'Tagline'],
                'address'         => ['type' => 'textarea',  'label' => 'Address'],
                'email'           => ['type' => 'text',     'label' => 'Email'],
                'phone'           => ['type' => 'text',     'label' => 'Phone'],
                'quick_links'     => [
                    'type'   => 'repeater',
                    'label'  => 'Quick Links',
                    'fields' => [
                        'label' => ['type' => 'text', 'label' => 'Label', 'required' => true],
                        'url'   => ['type' => 'text', 'label' => 'URL',   'required' => true],
                    ],
                ],
                'social_links'    => [
                    'type'   => 'repeater',
                    'label'  => 'Social Media Links',
                    'fields' => [
                        'platform' => ['type' => 'text', 'label' => 'Platform', 'required' => true],
                        'url'      => ['type' => 'text', 'label' => 'URL',      'required' => true],
                        'icon'     => ['type' => 'text', 'label' => 'Icon Name'],
                    ],
                ],
                'copyright_text'  => ['type' => 'text',     'label' => 'Copyright Text',    'placeholder' => '© 2026 Company Name. All rights reserved.'],
            ],
        ],

    ],
];
