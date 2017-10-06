<?php

namespace Drupal\enforce_profile_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'enforce_profile_field_redirect' formatter.
 *
 * @FieldFormatter(
 *   id = "enforce_profile_field_redirect",
 *   label = @Translation("Enforce profile redirect"),
 *   field_types = {
 *     "enforce_profile_field",
 *   }
 * )
 */
class EnforceProfileRedirectFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function prepareView(array $entities_items) {

  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    return $elements;
  }

}
