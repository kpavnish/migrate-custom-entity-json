<?php

namespace Drupal\migrate_city\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the City entity.
 *
 * @ingroup migrate_city
 *
 * @ContentEntityType(
 *   id = "city",
 *   label = @Translation("City"),
 *   handlers = {
 *     "storage" = "Drupal\migrate_city\CityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\migrate_city\CityListBuilder",
 *     "views_data" = "Drupal\migrate_city\Entity\CityViewsData",
 *     "translation" = "Drupal\migrate_city\CityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\migrate_city\Form\CityForm",
 *       "add" = "Drupal\migrate_city\Form\CityForm",
 *       "edit" = "Drupal\migrate_city\Form\CityForm",
 *       "delete" = "Drupal\migrate_city\Form\CityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\migrate_city\CityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\migrate_city\CityAccessControlHandler",
 *   },
 *   base_table = "city",
 *   data_table = "city_field_data",
 *   revision_table = "city_revision",
 *   revision_data_table = "city_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer city entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
*   revision_metadata_keys = {
*     "revision_user" = "revision_uid",
*     "revision_created" = "revision_timestamp",
*     "revision_log_message" = "revision_log"
*   },
 *   links = {
 *     "canonical" = "/city/city/{city}",
 *     "add-form" = "/city/city/add",
 *     "edit-form" = "/city/city/{city}/edit",
 *     "delete-form" = "/city/city/{city}/delete",
 *     "version-history" = "/city/city/{city}/revisions",
 *     "revision" = "/city/city/{city}/revisions/{city_revision}/view",
 *     "revision_revert" = "/city/city/{city}/revisions/{city_revision}/revert",
 *     "revision_delete" = "/city/city/{city}/revisions/{city_revision}/delete",
 *     "translation_revert" = "/city/city/{city}/revisions/{city_revision}/revert/{langcode}",
 *     "collection" = "/city/city",
 *   },
 *   field_ui_base_route = "city.settings"
 * )
 */
class City extends EditorialContentEntityBase implements CityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the city owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the City entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the City entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);
      $fields['location'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('Location'))
      ->setDescription(t('The GPS Coordinates of this Destination.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'geolocation_map',
        'weight' => 1,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'geolocation_googlegeocoder',
        'weight' => 1,
        'title' => t('title thing'),
        'set_marker' => '1',
        'info_text' => t('dome info text'),
        'use_overridden_map_settings' => 0,
        'google_map_settings' => array(
          'type' => 'TERRAIN',
          'zoom' => 5,
          'mapTypeControl' => 1,
          'streetViewControl' => 1,
          'zoomControl' => 1,
          'scrollwheel' => 1,
          'disableDoubleClickZoom' => 0,
          'draggable' => 1,
          'height' => '400px',
          'width' => '100%',
          'info_auto_display' => 1,
          'disableAutoPan' => 1,
          'style' => '',
          'preferScrollingToZooming' => 0,
          'gestureHandling' => 'auto'
        ),
        'auto_client_location' => 0,
        'auto_client_location_marker' => 0,
        'populate_address_field' => 0,
        'target_address_field' => '',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['state'] = BaseFieldDefinition::create('string')
    ->setLabel(t('State'))
    ->setDescription(t('The name of the state .'))
    ->setRevisionable(TRUE)
    ->setSettings([
      'max_length' => 50,
      'text_processing' => 0,
    ])
    ->setDefaultValue('')
    ->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE)
    ->setRequired(TRUE);

    $fields['pop'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Pop'))
    ->setDescription(t('The name of the pop .'))
    ->setRevisionable(TRUE)
    ->setSettings([
      'max_length' => 50,
      'text_processing' => 0,
    ])
    ->setDefaultValue('')
    ->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE)
    ->setRequired(TRUE);

   

    $fields['status']->setDescription(t('A boolean indicating whether the City is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
