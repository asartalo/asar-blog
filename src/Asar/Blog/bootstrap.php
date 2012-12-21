<?php

use Asar\Blog\Manager;

$manager = Manager::createManager();

return $entityManager = $manager->getEntityManager();