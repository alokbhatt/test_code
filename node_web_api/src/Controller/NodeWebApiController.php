<?php

namespace Drupal\node_web_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Enables an API to consume page content type node data.
 */
class NodeWebApiController {
  /**
   * Private variable to store API Key.
   *
   * @var string
   *   Stores API Key.
   */
  private $apiKey;

  /**
   * Constructor will set API key value when class will instantiate.
   */
  public function __construct() {
    $this->apiKey = \Drupal::config('api_key.siteapikey')->get('siteapikey');
  }

  /**
   * If API key and Node ID is valid then return JSON object.
   *
   * It will throw an error if API key or Node ID is invalid.
   */
  public function export($api_key, $nid) {
    $properties = [
      'type' => 'page',
      'status' => 1,
      'nid' => $nid,
    ];
    $node = \Drupal::entityTypeManager()->getStorage('node')
      ->loadByProperties($properties);

    if ($node && $api_key == $this->apiKey) {
      return new JsonResponse([
        'data' => $this->getResults($node[$nid]),
      ]);
    }
    else {
      throw new AccessDeniedHttpException();
    }
  }

  /**
   * Return an array containing field list with values.
   */
  private function getResults($node) {
    $fields = [];
    foreach ($node->getFieldDefinitions() as $field => $fieldObject) {
      $fields[$field] = $node->$field->value;
    }
    return $fields;
  }

}
