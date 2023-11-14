<?php

namespace Drupal\migrate_city;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the City entity.
 *
 * @see \Drupal\migrate_city\Entity\City.
 */
class CityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\migrate_city\Entity\CityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished city entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published city entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit city entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete city entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add city entities');
  }


}
