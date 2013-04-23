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
   'fieldName' => 'user_id',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'user_id',
  ));
$metadata->mapField(array(
   'fieldName' => 'blacked_date',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'blacked_date',
  ));
$metadata->mapField(array(
   'fieldName' => 'status',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'status',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);