<?php

use Doctrine\ORM\Tools\Setup;

$c = $container;

// Parameters
$c['isDevMode'] = true;

$c['isTestMode'] = false;

$c['isProductionMode'] = false;

$c['database.connection.production'] = array();


// Services
$c['database.connection'] = function($c) {

    if ($c['isProductionMode']) {
        return $c['database.connection.production'];
    }

    if ($c['isTestMode']) {
        return array(
            'driver' => 'pdo_sqlite',
            'memory' => true
        );
    }

    return array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/db.sqlite'
    );
};

$c['doctrine.entityManager'] = function($c) {

    return \Doctrine\ORM\EntityManager::create(
        $c['database.connection'], $c['doctrine.config']
    );
};

$c['doctrine.config'] = function($c) {
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        array(__DIR__), $c['isDevMode']
    );
    if (isset($c['doctrine.proxyDir'])) {
        $config->setProxyDir($c['doctrine.proxyDir']);
        $config->setProxyNamespace('Asar\Blog\Proxies');
    }

    return $config;
};