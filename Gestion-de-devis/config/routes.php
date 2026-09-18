<?php
return static function (Biscaphone\Router $router): void {
    $router->add('GET',  '/',                'CatalogueController', 'index');
    $router->add('GET',  '/quote',           'CatalogueController', 'instantQuote');
    $router->add('GET',  '/instant-quote',   'CatalogueController', 'instantQuote');

    $router->add('GET',  '/api/types',       'ApiController', 'types');
    $router->add('GET',  '/api/marques',     'ApiController', 'marques');
    $router->add('GET',  '/api/modeles',     'ApiController', 'modeles');
    $router->add('GET',  '/api/produits',    'ApiController', 'produits');

    $router->add('POST', '/api/devis',       'DevisController', 'create');
    $router->add('GET',  '/devis/{id}',      'DevisController', 'show');

    $router->add('GET',  '/admin',                   'AdminController', 'dashboard');
    $router->add('GET',  '/admin/login',             'AdminController', 'loginForm');
    $router->add('POST', '/admin/login',             'AdminController', 'login');
    $router->add('GET',  '/admin/logout',            'AdminController', 'logout');

    $router->add('GET',  '/admin/import',            'AdminImportController', 'importForm');
    $router->add('POST', '/admin/import',            'AdminImportController', 'import');
    $router->add('GET',  '/admin/backup',            'AdminMaintenanceController', 'backup');
    $router->add('GET',  '/admin/export-csv',        'AdminMaintenanceController', 'exportCsv');
    $router->add('POST', '/admin/change-password',   'AdminMaintenanceController', 'changePassword');

    $router->add('GET',  '/admin/devis',                     'AdminDevisController', 'devisList');
    $router->add('GET',  '/admin/devis/{id}',                'AdminDevisController', 'devisShow');
    $router->add('GET',  '/admin/devis/{id}/export-csv',     'AdminDevisController', 'devisExportSingle');
    $router->add('GET',  '/admin/devis-export-csv',          'AdminDevisController', 'devisExportCsv');
};