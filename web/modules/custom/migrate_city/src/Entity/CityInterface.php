<?php

namespace Drupal\migrate_city\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining City entities.
 *
 * @ingroup migrate_city
 */
interface CityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the City name.
   *
   * @return string
   *   Name of the City.
   */
  public function getName();

  /**
   * Sets the City name.
   *
   * @param string $name
   *   The City name.
   *
   * @return \Drupal\migrate_city\Entity\CityInterface
   *   The called City entity.
   */
  public function setName($name);

  /**
   * Gets the City creation timestamp.
   *
   * @return int
   *   Creation timestamp of the City.
   */
  public function getCreatedTime();

  /**
   * Sets the City creation timestamp.
   *
   * @param int $timestamp
   *   The City creation timestamp.
   *
   * @return \Drupal\migrate_city\Entity\CityInterface
   *   The called City entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the City revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the City revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\migrate_city\Entity\CityInterface
   *   The called City entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the City revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the City revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\migrate_city\Entity\CityInterface
   *   The called City entity.
   */
  public function setRevisionUserId($uid);

}
