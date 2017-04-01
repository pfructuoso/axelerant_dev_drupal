<?php

namespace Drupal\Tests\axelerant_dev_drupal\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that Site API key can be saved and the URL to obtain
 * a node in JSON format is working properly.
 *
 * @group axelerant_dev_drupal
 */
class EasyFunctionalTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['axelerant_dev_drupal'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * Site API key to be used during the tests.
   *
   * @var string
   */
  protected $siteApiKey = 'PANETE1234';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create Basic page and Article node types.
    $this->drupalCreateContentType(array(
      'type' => 'page',
      'name' => 'Basic page',
      'display_submitted' => FALSE,
    ));
    $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));

    // Create user and login.
    $this->user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($this->user);
  }

  /**
   * Tests Site API key functionality.
   */
  public function testSiteApiKey() {
    // Saves site API key.
    $edit = [
      'site_api_key' => $this->siteApiKey,
      // We need site_frontpage to avoid validation issues.
      'site_frontpage' => '/user'
    ];
    $this->drupalPostForm('admin/config/system/site-information', $edit, t('Update Configuration'));
    $this->assertSession()->pageTextContains(t('Site API Key has been saved with ' . $this->siteApiKey . ' value.'), 'Site API key was saved');

    // Create a node.
    $edit = [
      'title' => $this->randomMachineName(8),
      'body' => $this->randomMachineName(16)
    ];
    $node = $this->drupalCreateNode($edit);

    // Get JSON for the node.
    $path = 'page_json/' . $this->siteApiKey . '/' . $node->id();
    $this->drupalGet($path);
    $this->assertSession()->statusCodeEquals(200);

    $response_node = json_decode($this->getSession()->getPage()->getContent());
    $this->assertEquals($response_node->nid[0]->value, $node->id());

    // Access denied for wrong API Key.
    $path = 'page_json/DUMMY1234/' . $node->id();
    $this->drupalGet($path);
    $this->assertSession()->statusCodeEquals(403);
  }

}
