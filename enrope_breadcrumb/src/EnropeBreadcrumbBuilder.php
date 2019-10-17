<?php

namespace Drupal\enrope_breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\group\Entity\GroupContent;

class EnropeBreadcrumbBuilder implements BreadcrumbBuilderInterface
{
  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $attributes)
  {
    $parameters = $attributes->getParameters()->all();
    if (!empty($parameters['node'])) {
      return $this->getRouteByContentType($parameters['node']->getType());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match)
  {

    $parameters = $route_match->getParameters()->all();

    $group_id = null;
    $group_title = null;
    $group_content_type = null;


    if (!empty($parameters['node'])) {
      $group_content_type = $parameters['node']->getType();


      foreach (GroupContent::loadByEntity($parameters['node']) as $group_content) {

        $group_id = $group_content->getGroup()->id();
        $group_title = $group_content->getGroup()->label();

      }

    }

    $breadcrumb = new Breadcrumb();

    $breadcrumb->addLink(Link::createFromRoute($group_title, 'entity.group.canonical', ['group' => $group_id]));


    if ($group_content_type) {
      $breadcrumb_values = $this->getRouteByContentType($group_content_type);
      $breadcrumb->addLink(Link::createFromRoute($breadcrumb_values['name'], $breadcrumb_values['route'], ['group' => $group_id]));
    }

    return $breadcrumb;
  }


  private function getRouteByContentType($content_type)
  {

    if ($content_type == 'group_article') {
      return ['name' => "Articles", 'route' => "view.group_articles_.page_1"];
    } elseif ($content_type == 'file') {
      return ["name" => "Files", 'route' => "view.files.page_1"];
    } elseif ($content_type == 'group_page') {
      return ["name" => 'Pages', 'route' => "view.group_pages.page_1"];
    } elseif ($content_type == 'resources') {
      return ["name" => 'Resources', 'route' => "view.group_resources.page_1"];
    } elseif ($content_type == 'discussion_board') {
      return ['name' => 'Discussions', 'route' => "view.discussion_board.page_1"];
    } elseif ($content_type == 'group_task') {
      return ['name' => 'Tasks', 'route' => "view.group_ta.page_1"];
    } else {
      return null;
    }

  }
}
