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
   'fieldName' => 'title',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'title',
  ));
$metadata->mapField(array(
   'fieldName' => 'content',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'content',
  ));
$metadata->mapField(array(
   'fieldName' => 'start_time',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'start_time',
  ));
$metadata->mapField(array(
   'fieldName' => 'end_time',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'end_time',
  ));
$metadata->mapField(array(
   'fieldName' => 'url',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'url',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);