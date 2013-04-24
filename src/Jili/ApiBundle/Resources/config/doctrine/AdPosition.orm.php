<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
$metadata->mapField(array(
   'fieldName' => 'id',
   'type' => 'integer',
   'id' => true,
   'columnName' => 'id',
  ));
$metadata->mapField(array(
   'fieldName' => 'ad_id',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'ad_id',
  ));
$metadata->mapField(array(
   'fieldName' => 'type',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'type',
  ));
$metadata->mapField(array(
   'fieldName' => 'position',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'position',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);