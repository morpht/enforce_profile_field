<?php

namespace Drupal\enforce_profile_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
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
   * {@inheritdoc}
   */
  public function prepareView(array $entities_items) {
    /** @var \Drupal\Core\Field\FieldItemList */
    /** @var \Drupal\Core\Field\FieldItemListInterface $entity_item */
    foreach ($entities_items as $entity_item) {
      $field = $entity_item->getFieldDefinition();
      $storage = $field->getFieldStorageDefinition();
      $form_mode = $storage->getSetting('form_mode');

      // Form mode must be selected.
      if (!empty($form_mode)) {
        $user = \Drupal::currentUser();

        /** @var \Drupal\user\UserInterface $user_account */
        $user_account = \Drupal::entityTypeManager()
          ->getStorage('user')
          ->load($user->id());

        // Get all required field values.
        $values = $entity_item->getValue();

        $valid = TRUE;

        foreach ($values as $value) {
          $machine_name = $value['value'];

          // Validate that the field still exists.
          $field_definition = $user_account->getFieldDefinition($machine_name);
          if (isset($field_definition)) {
            // Get user field item.
            $user_field = $user_account->get($machine_name);

            // Validate that the field item is filled in.
            if ($user_field->isEmpty()) {
              $valid = FALSE;
              break;
            }
          }
        }

        if (!$valid) {
          // Get the selected entity form display.
          /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $entity_form_display */
          $entity_form_display = \Drupal::entityTypeManager()
            ->getStorage('entity_form_display')
            ->load($form_mode);

          // Redirect the user to fill in missing fields.
          $mode = $entity_form_display->get('mode');
          $path = URL::fromRoute('entity.user.edit_form', ['user' => $user->id()])
            ->toString();

          // TODO: Look for a better way how to get an entity form display url.
          $entity_url = $entity_item->getEntity()->toUrl()->toString();
          $options = ['query' => ['destination' => $entity_url]];
          $url = Url::fromUserInput($path . '/' . $mode, $options)->toString();

          // Inform user about missing fields that needs to be filled in in order
          // to access the page.
          // TODO: Find out a way how to preserve/display the message after the redirect.
          drupal_set_message(t('You have been redirected because...'), 'status', TRUE);

          $response = new RedirectResponse($url);
          $response->send();
        }
      }
    }
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
