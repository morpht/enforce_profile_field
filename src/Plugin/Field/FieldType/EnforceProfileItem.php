<?php

namespace Drupal\enforce_profile_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\options\Plugin\Field\FieldType\ListItemBase;

/**
 * Defines the 'enforce_profile_field' entity field type.
 *
 * @FieldType(
 *   id = "enforce_profile_field",
 *   label = @Translation("Enforce profile"),
 *   description = @Translation("An entity field enforcing additional profile data."),
 *   category = @Translation("User"),
 *   default_widget = "options_select",
 *   default_formatter = "enforce_profile_field_redirect",
 * )
 */
class EnforceProfileItem extends ListItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'form_mode' => '',
      'allowed_values_function' => 'enforce_profile_field_allowed_values',
    ] + parent::defaultStorageSettings();
  }
  
  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Machine name value'))
      ->addConstraint('Length', ['max' => FieldStorageConfig::NAME_MAX_LENGTH])
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => FieldStorageConfig::NAME_MAX_LENGTH,
        ],
      ],
      'indexes' => [
        'value' => ['value'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function allowedValuesDescription() {
    // Allowed values are provided programatically, so there is no need
    // to provide any description for a user.
    $description = '';

    return $description;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];
    $options = $this->getFormModes();

    $element['form_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('User\'s form mode'),
      '#options' => $options,
      '#default_value' => $this->getSetting('form_mode'),
      '#required' => TRUE,
    ];

    return $element;
  }

  /**
   * Get form modes by id.
   *
   * @param string $entity_type_id
   *   Entity type id.
   *
   * @return array
   *   An array of entity type's from modes by id.
   */
  private function getFormModes($entity_type_id = 'user') {
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository */
    $entity_display_repository = \Drupal::service('entity_display.repository');
    // Get form modes of the user entity type.
    $form_modes = $entity_display_repository->getFormModes($entity_type_id);

    // Init options variable.
    $modes_by_id = [];

    // Extract key/value pairs.
    foreach ($form_modes as $mode) {
      $id = $mode['id'];
      $target_entity_type = $mode['targetEntityType'];
      $label = $mode['label'];

      // Prepare unique key.
      $key = $target_entity_type . '.' . $id;

      $modes_by_id[$key] = $label;
    }

    return $modes_by_id;
  }

}
