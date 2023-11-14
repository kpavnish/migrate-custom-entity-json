<?php

namespace Drupal\migrate_city\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\migrate_city\Entity\CityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CityController.
 *
 *  Returns responses for City routes.
 */
class CityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a City revision.
   *
   * @param int $city_revision
   *   The City revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($city_revision) {
    $city = $this->entityTypeManager()->getStorage('city')
      ->loadRevision($city_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('city');

    return $view_builder->view($city);
  }

  /**
   * Page title callback for a City revision.
   *
   * @param int $city_revision
   *   The City revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($city_revision) {
    $city = $this->entityTypeManager()->getStorage('city')
      ->loadRevision($city_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $city->label(),
      '%date' => $this->dateFormatter->format($city->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a City.
   *
   * @param \Drupal\migrate_city\Entity\CityInterface $city
   *   A City object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CityInterface $city) {
    $account = $this->currentUser();
    $city_storage = $this->entityTypeManager()->getStorage('city');

    $langcode = $city->language()->getId();
    $langname = $city->language()->getName();
    $languages = $city->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $city->label()]) : $this->t('Revisions for %title', ['%title' => $city->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all city revisions") || $account->hasPermission('administer city entities')));
    $delete_permission = (($account->hasPermission("delete all city revisions") || $account->hasPermission('administer city entities')));

    $rows = [];

    $vids = $city_storage->revisionIds($city);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\migrate_city\Entity\CityInterface $revision */
      $revision = $city_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $city->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.city.revision', [
            'city' => $city->id(),
            'city_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $city->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.city.translation_revert', [
                'city' => $city->id(),
                'city_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.city.revision_revert', [
                'city' => $city->id(),
                'city_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.city.revision_delete', [
                'city' => $city->id(),
                'city_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['city_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
