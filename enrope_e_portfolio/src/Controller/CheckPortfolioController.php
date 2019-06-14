<?php
namespace Drupal\enrope_e_portfolio\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;

class CheckPortfolioController extends ControllerBase
{

  public function checkAccess($node)
  {
    $actualNode = Node::load($node);

    if($actualNode->bundle() === 'e_portfolio'){
      return AccessResult::allowed();
    }
    else{
      return AccessResult::forbidden();
    }

  }

}