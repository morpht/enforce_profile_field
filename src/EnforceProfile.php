<?php

namespace Drupal\enforce_profile_field;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Class EnforceProfile
 *
 * @package Drupal\enforce_profile_field
 */
class EnforceProfile {

  /**
   * A form mode identifier.
   *
   * @var string
   */
  protected $formMode;

  /**
   * Constructs an EnforceProfile object.
   *
   * @param $form_mode_id string
   *   A form mode identifier.
   */
  public function __contruct($form_mode) {
    $this->formMode = $form_mode;
  }

  /**
   * Validate field items.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   List of required field items.
   *
   * @return boolean
   *   Returns a TRUE if all required fields are filled in, FALSE otherwise.
   */
  public function validate(FieldItemListInterface $items) {
    $user = \Drupal::currentUser();
    /** @var \Drupal\user\UserInterface $user_account */
    $user_account = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->load($user->id());

    // Get all required field values.
    $values = $items->getValue();
    // Process all values.
    foreach ($values as $value) {
      $machine_name = $value['value'];

      // Proceed only if the field exists.
      $field_exists = isset($user_account->getFieldDefinition($machine_name));
      if ($field_exists) {
        // Get user field item.
        $field = $user_account->get($machine_name);

        // Validate that the field item is filled in.
        if ($field->isEmpty()) {
          return FALSE;
        }
      }
    }

    // All fields are filled or not present anymore (the saved required values,
    // may be outdated.
    return TRUE;
  }

  /**
   * Get from mode url.
   *
   * @param strin $destination
   *   Destination query of the url.
   */
  public function getFormModeUrl($destination = '') {
    // Get the selected entity form display.
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $entity_form_display */
    $entity_form_display = \Drupal::entityTypeManager()
      ->getStorage('entity_form_display')
      ->load($this->formMode);

    // Redirect the user to fill in missing fields.
    $mode = $entity_form_display->get('mode');
    $user = \Drupal::currentUser();
    $base_url = URL::fromRoute('entity.user.edit_form', ['user' => $user->id()])
      ->toString();

    // Prepare destination query if present.
    $options = [];
    if (!empty($destination)) {
      $options['query'] = ['destination' => $destination];
    }
    // TODO: Look for a better way how to get an entity form display url.
    $url = Url::fromUserInput($base_url . '/' . $mode, $options)->toString();

    return $url;
  }

}