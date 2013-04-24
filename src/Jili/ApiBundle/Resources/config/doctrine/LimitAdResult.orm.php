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
   'fieldName' => 'limit_ad_id',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'limit_ad_id',
  ));
$metadata->mapField(array(
   'fieldName' => 'result_point',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'result_point',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);