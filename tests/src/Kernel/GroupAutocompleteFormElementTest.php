<?php

namespace Drupal\Tests\entitygroupfield\Kernel;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

/**
 * Tests the 'group_autocomplete' form element.
 *
 * @group entitygroupfield
 */
class GroupAutocompleteFormElementTest extends EntityGroupFieldKernelTestBase implements FormInterface {

  /**
   * Test groups.
   *
   * @var \Drupal\group\Entity\GroupInterface[]
   */
  protected $testGroups;

  /**
   * Form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->formBuilder = $this->container->get('form_builder');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entitygroupfield_group_autocomplete_form_element_test';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['group_autocomplete_all'] = [
      '#title' => 'Group (all)',
      '#type' => 'group_autocomplete',
      '#target_type' => 'group',
      '#selection_handler' => 'group:group_content',
      '#selection_settings' => ['allowed_groups' => []],
    ];

    $form['group_autocomplete_restricted'] = [
      '#title' => 'Group (restricted)',
      '#type' => 'group_autocomplete',
      '#target_type' => 'group',
      '#selection_handler' => 'group:group_content',
      '#selection_settings' => [
        'allowed_groups' => [],
        'excluded_groups' => [1, 2],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Tests the group_autocomplete form element when no groups exist.
   */
  public function testGroupAutocompleteNoGroups() {
    // Empty values.
    $form_state = (new FormState())
      ->setValues([
        'group_autocomplete_all' => '',
        'group_autocomplete_restricted' => '',
      ]);
    $this->formBuilder->submitForm($this, $form_state);
    $this->assertEmpty($form_state->getErrors());

    // Group not found.
    $form_state->setValues([
      'group_autocomplete_all' => 'missing',
    ]);
    $this->formBuilder->submitForm($this, $form_state);
    $form_errors = $form_state->getErrors();
    $this->assertCount(1, $form_errors);
    $this->assertEquals('There are no groups called "<em class="placeholder">missing</em>".', $form_errors['group_autocomplete_all']);

    // Invalid ID.
    $form_state->setValues([
      'group_autocomplete_all' => 'invalid (x)',
    ]);
    $this->formBuilder->submitForm($this, $form_state);
    $form_errors = $form_state->getErrors();
    $this->assertCount(1, $form_errors);
    $this->assertEquals('The referenced entity (<em class="placeholder">group</em>: <em class="placeholder">x</em>) does not exist.', $form_errors['group_autocomplete_all']);
  }

}
