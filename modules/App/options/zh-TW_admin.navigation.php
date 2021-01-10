<?php

/**
 * #參考 './modules/React/containers/admin/_nav.js'
 */
return [
    /** 儀錶板(數據圖表呈現) **/
    [
        'tag' => 'CSidebarNavItem',
        'name' => 'Dsahboard',
        'uri' => '/zh-TW/admin/dashboard',
        //'component' => 'views/admin/pages/Dashboard',
        'font-icon' => 'fas fa-tachometer-alt',
    ],

    /** 網頁文件管理 **/
    [
        'tag' => 'CSidebarNavDropdown',
        'name' => 'Documents & Resources',
        'font-icon' => 'fas fa-photo-video',
        'uri' => '#',
        'pages' => [
            [   //網頁管理
                'tag' => 'CSidebarNavItem',
                'name' => 'Documents',
                'uri' => '/zh-TW/admin/documents',
            ],
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'Carousel',
                'uri' => '/zh-TW/admin/banner',
            ],
            /*[
                'tag' => 'CSidebarNavItem',
                'name' => 'Document Header',
                'uri' => '/zh-TW/admin/documents_header',
            ],*/
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'Document Footer',
                'uri' => '/zh-TW/admin/documents_footer',
            ],
        ],
        
    ],

    /** 商品管理 **/
    [
        'tag' => 'CSidebarNavTitle',
        'name' => 'Goods management',
        'uri' => '#',
    ],
    [   /** 製造商，品牌 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Manufactures',
        'uri' => '/zh-TW/admin/manufactures',
        'font-icon' => 'fas fa-industry',
    ],
    [   /** 產品分類與屬性管理 **/
        'tag' => 'CSidebarNavDropdown',
        'name' => 'Product category',
        'font-icon' => 'fas fa-boxes',
        'uri' => '#',
        'pages' => [
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'Products category level1',
                'uri' => '/zh-TW/admin/fp_class',
            ],
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'Products category level2',
                'uri' => '/zh-TW/admin/mp_class',
            ],
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'Products category level3',
                'uri' => '/zh-TW/admin/np_class',
            ],
        ],
    ],
//     [   /** 產品屬性 **/
//         'font-icon' => 'fas fa-tasks',
//         'tag' => 'CSidebarNavItem',
//         'name' => 'Products attrs',
//         'uri' => '/zh-TW/admin/products_attrs',
//     ],
    [   /** 產品管理 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Products management',
        'uri' => '/zh-TW/admin/products',
        'font-icon' => 'fas fa-box',
    ],
    [   /** 產品規格群組 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Products Spec group',
        'uri' => '/zh-TW/admin/products_spec_group',
        'font-icon' => 'fas fa-object-group',
    ],
    
    [   /** 產品規格管理 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Products Spec&#46',
        'uri' => '/zh-TW/admin/products_spec',
        'font-icon' => 'fas fa-th',
    ],

    /** 銷售管理 **/
    [
        'tag' => 'CSidebarNavTitle',
        'name' => 'Sales management',
        'uri' => '#',
    ],
    [   /** 訂單管理 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Order',
        'uri' => '/zh-TW/admin/order',
        'font-icon' => 'fas fa-money-bill',
    ],
    [   /** 折價券管理 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Coupon',
        'uri' => '/zh-TW/admin/coupon',
        'font-icon' => 'fas fa-percent',
    ],

    /** 推廣與回饋 **/
    [
        'tag' => 'CSidebarNavTitle',
        'name' => 'Newsletter',
        'uri' => '#',
    ],
    [   /** 最新消息分類管理 **/
        'tag' => 'CSidebarNavDropdown',
        'name' => 'News category',
        'font-icon' => 'fas fa-book-open',
        'uri' => '#',
        'pages' => [
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'News category level1',
                'uri' => '/zh-TW/admin/fn_class',
            ],
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'News category level2',
                'uri' => '/zh-TW/admin/mn_class',
            ],
            [
                'tag' => 'CSidebarNavItem',
                'name' => 'News category level3',
                'uri' => '/zh-TW/admin/nn_class',
            ],
        ],
    ],
    [   /** 最新消息 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'News',
        'uri' => '/zh-TW/admin/news',
        'font-icon' => 'fas fa-newspaper',
    ],
    [   /** 聯絡我們 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Contact',
        'uri' => '/zh-TW/admin/contact',
        'font-icon' => 'fas fa-envelope-square',
    ],
    /** 會員與網站管理者 **/
    [
        'tag' => 'CSidebarNavTitle',
        'name' => 'Member & Admin',
        'uri' => '#',
    ],
    [   /** 網站(商店)會員 **/
        'tag' => 'CSidebarNavDropdown',
        'name' => 'Members',
        'font-icon' => 'fas fa-id-card',
        'uri' => '#',
        'pages' => [
            [   /** 會員群組 **/
                'tag' => 'CSidebarNavItem',
                'name' => 'Member role',
                'uri' => '/zh-TW/admin/member_roles',
            ],
            [   /** 會員 **/
                'tag' => 'CSidebarNavItem',
                'name' => 'Member list',
                'uri' => '/zh-TW/admin/member_list',
            ],
        ],
    ],
    [   /** 網站管理者 **/
        'tag' => 'CSidebarNavDropdown',
        'name' => 'Site managers',
        'font-icon' => 'fas fa-user-shield',
        'uri' => '#',
        'pages' => [
            [   /** 管理者群組 **/
                'tag' => 'CSidebarNavItem',
                'name' => 'Manager role',
                'uri' => '/zh-TW/admin/manager_roles',
            ],
            [   /** 管理者列表 **/
                'tag' => 'CSidebarNavItem',
                'name' => 'Manager list',
                'uri' => '/zh-TW/admin/manager_list',
            ],
            [   /** 管理者 **/
                'tag' => 'CSidebarNavItem',
                'name' => 'Manager profile',
                'uri' => '/zh-TW/admin/manager_profile',
            ],
        ],
    ],

    /** 系統設定 **/
    [
        'tag' => 'CSidebarNavTitle',
        'name' => 'System manage',
        'uri' => '#',
    ],
    [
        /** 全域物流設定 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Global logistics',
        'uri' => '/zh-TW/admin/logistics_global',
        'font-icon' => 'fas fa-truck-moving',
    ],[
        /** 語言 **/
        'tag' => 'CSidebarNavItem',
        'name' => 'Language',
        'font-icon' => 'fas fa-language',
        'uri' => '/zh-TW/admin/language',
        //'component' => 'views/admin/pages/language',
    ],
    [
        /* currency */
        'tag' => 'CSidebarNavItem',
        'name' => 'Currency',
        'font-icon' => 'fas fa-dollar-sign',
        'uri' => '/zh-TW/admin/currencies',
    ],
    [
        'tag' => 'CSidebarNavItem',
        'name' => 'System setting',
        'uri' => '/zh-TW/admin/system_setting',
        'font-icon' => 'fas fa-cogs',
    ],
];