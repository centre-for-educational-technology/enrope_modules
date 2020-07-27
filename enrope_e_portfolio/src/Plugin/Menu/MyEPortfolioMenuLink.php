<?php


namespace Drupal\enrope_e_portfolio\Plugin\Menu;


use Drupal\Core\Database\Connection;
use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MyEPortfolioMenuLink extends MenuLinkDefault
{
  protected $currentUser;
  protected $database;
  protected $portfolio;


  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StaticMenuLinkOverridesInterface $static_override, AccountInterface $current_user, Connection $database)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $static_override);

    $this->currentUser = $current_user;
    $this->database = $database;

    $query_items = $this->database->select('node_field_data', 'n');
    $query_items->fields('n', array('nid', 'status'));
    $query_items->condition('n.type', 'e_portfolio');
    $query_items->condition('n.uid', $this->currentUser->id());
    $result_items = $query_items->execute()->fetchAll();

    $this->portfolio = $result_items;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu_link.static.overrides'),
      $container->get('current_user'),
      $container->get('database')
    );
  }


  public function getTitle()
  {

    if (!empty($this->portfolio)) {
      if($this->portfolio[0]->status == 1){
        return \Drupal\Core\Render\Markup::create($this->t('View portfolio').' <span class="badge badge-success">Published</span>');
      }else{
        return \Drupal\Core\Render\Markup::create($this->t('View portfolio').' <span class="badge badge-danger">Draft</span>');
      }

    } else {
      return $this->t('Create portfolio');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteName()
  {


    if (!empty($this->portfolio)) {
      return 'entity.node.canonical';
    } else {
      return 'node.add';
    }
  }

  public function getRouteParameters()
  {


    if (!empty($this->portfolio)) {
      return ['node' => $this->portfolio[0]->nid];
    } else {
      return ['node_type' => 'e_portfolio'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts()
  {
    return ['user.roles:authenticated'];
  }

  public function getCacheMaxAge() {
    return 0;
  }
}
