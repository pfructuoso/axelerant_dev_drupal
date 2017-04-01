<?php

namespace Drupal\axelerant_dev_drupal\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\node\NodeInterface;
use Zend\Diactoros\Response\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class JsonRequestController.
 * Manage route responses for Axelerant dev Drupal module.
 *
 * @package Drupal\axelerant_dev_drupal\Controller
 */
class JsonRequestController extends ControllerBase implements ContainerInjectionInterface {
  /**
   * Serialization service.
   * @var /Symfony/Component/Serializer/SerializerInterface
   */
  protected $serializer;

  function __construct(SerializerInterface $serializer) {
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('serializer')
    );
  }

  /**
   * Return a node in JSON format.
   *
   * @return string
   *   Return Hello string.
   */
  public function get_page($siteapikey, NodeInterface $node) {
    if ($node->getType() != 'page') {
      throw new AccessDeniedHttpException();
    }
    return new JsonResponse($this->serializer->normalize($node));
  }

}
