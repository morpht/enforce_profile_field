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
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['test'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Test'),
      '#default_value' => 'Test text.',
      '#required' => FALSE,
    ];

    return $element;
  }

}
