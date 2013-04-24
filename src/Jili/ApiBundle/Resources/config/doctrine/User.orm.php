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
   'fieldName' => 'nick',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'nick',
  ));
$metadata->mapField(array(
   'fieldName' => 'pwd',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'pwd',
  ));
$metadata->mapField(array(
   'fieldName' => 'sex',
   'type' => 'int',
   'length' => '1',
   'columnName' => 'sex',
  ));
$metadata->mapField(array(
   'fieldName' => 'birthday',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'birthday',
  ));
$metadata->mapField(array(
   'fieldName' => 'email',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'email',
  ));
$metadata->mapField(array(
   'fieldName' => 'is_email_confirmed',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'is_email_confirmed',
  ));
$metadata->mapField(array(
   'fieldName' => 'tel',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'tel',
  ));
$metadata->mapField(array(
   'fieldName' => 'is_tel_confirmed',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'is_tel_confirmed',
  ));
$metadata->mapField(array(
   'fieldName' => 'city',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'city',
  ));
$metadata->mapField(array(
   'fieldName' => 'identity_num',
   'type' => 'varchar',
   'length' => '40',
   'columnName' => 'identity_num',
  ));
$metadata->mapField(array(
   'fieldName' => 'register_date',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'register_date',
  ));
$metadata->mapField(array(
   'fieldName' => 'last_login_date',
   'type' => 'datetime',
   'length' => NULL,
   'columnName' => 'last_login_date',
  ));
$metadata->mapField(array(
   'fieldName' => 'last_login_ip',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'last_login_ip',
  ));
$metadata->mapField(array(
   'fieldName' => 'points',
   'type' => 'varchar',
   'length' => '45',
   'columnName' => 'points',
  ));
$metadata->mapField(array(
   'fieldName' => 'delete_flag',
   'type' => 'int',
   'length' => '11',
   'columnName' => 'delete_flag',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);