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
   'fieldName' => 'type',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'type',
  ));
$metadata->mapField(array(
   'fieldName' => 'title',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'title',
  ));
$metadata->mapField(array(
   'fieldName' => 'created_time',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'created_time',
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
   'fieldName' => 'update_time',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'update_time',
  ));
$metadata->mapField(array(
   'fieldName' => 'content',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'content',
  ));
$metadata->mapField(array(
   'fieldName' => 'imageurl',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'imageurl',
  ));
$metadata->mapField(array(
   'fieldName' => 'incentive',
   'type' => 'string',
   'length' => NULL,
   'columnName' => 'incentive',
  ));
$metadata->mapField(array(
   'fieldName' => '_type',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => '_type',
  ));
$metadata->mapField(array(
   'fieldName' => 'info',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'info',
  ));
$metadata->mapField(array(
   'fieldName' => 'income',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'income',
  ));
$metadata->mapField(array(
   'fieldName' => 'delete_flag',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'delete_flag',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);