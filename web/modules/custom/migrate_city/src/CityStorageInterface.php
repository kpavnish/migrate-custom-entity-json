<?php

namespace Drupal\migrate_city;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface CityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of City revision IDs for a specific City.
   *
   * @param \Drupal\migrate_city\Entity\CityInterface $entity
   *   The City entity.
   *
   * @return int[]
   *   City revision IDs (in ascending order).
   */
  public function revisionIds(CityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as City author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   City revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\migrate_city\Entity\CityInterface $entity
   *   The City entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CityInterface $entity);

  /**
   * Unsets the language for all City with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
