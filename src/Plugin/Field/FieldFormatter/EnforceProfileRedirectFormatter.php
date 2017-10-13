<?php

namespace Drupal\enforce_profile_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\enforce_profile_field\EnforceProfile;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
   * The enforce_profile.
   *
   * @var \Drupal\enforce_profile_field\EnforceProfile
   */
  protected $enforceProfile;

  /**
   * Constructs a FormatterBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $form_mode = $field_definition->getFieldStorageDefinition()->getSetting('form_mode');
    $this->enforceProfile = new EnforceProfile($form_mode);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareView(array $entities_items) {
    // Process all entity items.
    /** @var \Drupal\Core\Field\FieldItemListInterface $entity_items */
    foreach ($entities_items as $entity_items) {
      $valid = $this->enforceProfile->validate($entity_items);

      // Some fields are missing value.
      if (!$valid) {
        $this->redirect($entity_items);
      }
    }
  }

  /**
   * Redirect user to fill in missing fields.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   List of required field items.
   */
  private function redirect(FieldItemListInterface $items) {
    // Inform user about missing fields that needs to be filled in in order
    // to access the page.
    // TODO: Find out a way how to preserve/display the message after the
    // redirect.
    drupal_set_message($this->t('You have been redirected because...'), 'status', TRUE);

    // Get form mode path.
    $destination = $items->getEntity()->toUrl()->toString();
    $url = $this->enforceProfile->getFormModeUrl($destination);

    $response = new RedirectResponse($url);
    $response->send();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    // Not to cache this field formatter.
    $elements['#cache']['max-age'] = 0;

    return $elements;
  }

}
