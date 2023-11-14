<?php

namespace Drupal\migrate_city\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for City entities.
 */
class CityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
