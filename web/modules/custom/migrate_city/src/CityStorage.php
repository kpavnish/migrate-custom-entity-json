<?php

namespace Drupal\migrate_city;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\migrate_city\Entity\CityInterface;

/**
 * Defines the storage handler class for City entities.
 *
 * This extends the base storage class, adding required special handling for
 * City entities.
 *
 * @ingroup migrate_city
 */
class CityStorage extends SqlContentEntityStorage implements CityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {city_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {city_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {city_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('city_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
