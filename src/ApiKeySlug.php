<?php

namespace Drupal\axelerant_dev_drupal;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ApiKeySlug. Manages Site API Key in URL as parameter.
 *
 * @package Drupal\axelerant_dev_drupal
 */
class ApiKeySlug implements ParamConverterInterface {

  /**
   * Drupal\Core\Config\ConfigManager definition.
   *
   * @var \Drupal\Core\Config\ConfigManager
   */
  protected $configManager;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactoryInterface $config_manager) {
    $this->configManager = $config_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    // Deny access if Site API Key is not set or different form parameter.
    $conf_siteapikey = $this->configManager->get('axelerant_dev_drupal.site')->get('siteapikey');
    if ($conf_siteapikey != $value) {
      throw new AccessDeniedHttpException();
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return (!empty($definition['type']) && $definition['type'] == 'api_key_slug');
  }

}
