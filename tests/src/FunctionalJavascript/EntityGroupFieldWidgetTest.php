<?php

namespace Drupal\Tests\entitygroupfield\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\entitygroupfield\Traits\GroupCreationTrait;
use Drupal\Tests\entitygroupfield\Traits\TestGroupsTrait;

/**
 * Tests the JavaScript AJAX functionality of the entitygroupfield widgets.
 *
 * @group entitygroupfield
 */
class EntityGroupFieldWidgetTest extends WebDriverTestBase {

  use GroupCreationTrait;
  use TestGroupsTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'field_ui',
    'group',
    'gnode',
    'entitygroupfield',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Regular authenticated User for tests.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $testUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->drupalLogin($this->drupalCreateUser([
      'administer content types',
      'administer node fields',
      'administer node display',
      'bypass node access',
      // @todo Don't use this perm, be more careful with Group memberships.
      'bypass group access',
    ]));

    // Setup the group types and test groups from the TestGroupsTrait.
    $this->initializeTestGroups();

    // Create node types.
    $this->drupalCreateContentType(['type' => 'article', 'name' => t('Article')]);
    $this->drupalCreateContentType(['type' => 'page', 'name' => t('Basic page')]);

    // Enable article nodes to be assigned to only 'A' group type.
    $this->entityTypeManager->getStorage('group_content_type')
      ->createFromPlugin($this->groupTypeA, 'group_node:article')->save();
    // Let page nodes be assigned to both 'A' and 'B' groups.
    $this->entityTypeManager->getStorage('group_content_type')
      ->createFromPlugin($this->groupTypeA, 'group_node:page')->save();
    $this->entityTypeManager->getStorage('group_content_type')
      ->createFromPlugin($this->groupTypeB, 'group_node:page')->save();
  }

  /**
   * Test group field widgets.
   */
  public function testFieldWidgets() {

    // Before we configure anything make sure we don't see our widgets.
    // Verify article nodes.
    $this->drupalGet('/node/add/article');
    $page = $this->getSession()->getPage();
    $groups_widget = $page->findAll('css', '#edit-group-content');
    $this->assertEmpty($groups_widget);
    $groups_element = $page->findField('group_content[add_more][add_relation]');
    $this->assertEmpty($groups_element);
    $add_group_button = $page->findButton('Add to Group');
    $this->assertEmpty($add_group_button);
    // Verify page nodes.
    $this->drupalGet('/node/add/page');
    $page = $this->getSession()->getPage();
    $groups_widget = $page->findAll('css', '#edit-group-content');
    $this->assertEmpty($groups_widget);
    $groups_element = $page->findField('group_content[add_more][add_relation]');
    $this->assertEmpty($groups_element);
    $add_group_button = $page->findButton('Add to Group');
    $this->assertEmpty($add_group_button);

    // Now, try both of the widgets on each of the node types. We use protected
    // helper methods, not entirely new test* methods, to avoid the (intense)
    // startup costs of FunctionalJavascript tests.
    $this->checkArticleAutocompleteWidget();
    $this->checkArticleSelectWidget();
    $this->checkPageAutocompleteWidget();
    $this->checkPageSelectWidget();
  }

  /**
   * Test the 'autocomplete' group field widget on article nodes.
   */
  protected function checkArticleAutocompleteWidget() {
    // Configure articles to use the autocomplete widget.
    $this->configureFormDisplay('article', [
      'type' => 'entitygroupfield_autocomplete_widget',
      'settings' => [
        'multiple' => TRUE,
        'required' => FALSE,
      ],
    ]);

    // Now we should see the widget.
    $this->drupalGet('/node/add/article');
    $page = $this->getSession()->getPage();
    $groups_widget = $page->findAll('css', '#edit-group-content');
    $this->assertNotEmpty($groups_widget);
    $groups_autocomplete = $page->findField('group_content[add_more][add_relation]');
    $this->assertNotEmpty($groups_autocomplete);

    // Actually test the autocomplete.
    $groups_autocomplete->setValue('group');
    $this->getSession()->getDriver()->keyDown($groups_autocomplete->getXpath(), ' ');
    $this->assertSession()->waitOnAutocomplete();

    // Check the autocomplete results.
    $results = $page->findAll('css', '.ui-autocomplete li');
    $this->assertCount(2, $results);
    $this->assertEquals($this->groupA1->label(), $results[0]->getText());
    $this->assertEquals($this->groupA2->label(), $results[1]->getText());

    // Click on the first result and make sure it works.
    $page->find('css', '.ui-autocomplete li:first-child a')->click();
    $this->assertSession()->fieldValueEquals('group_content[add_more][add_relation]', $this->groupA1->label() . ' (' . $this->groupA1->id() . ')');

    // @todo: Actually try to save the new article and make sure it worked.
  }

  /**
   * Test the 'select' group field widget on article nodes.
   */
  protected function checkArticleSelectWidget() {
    $assert_session = $this->assertSession();

    // Configure articles to use the select widget.
    $this->configureFormDisplay('article', [
      'type' => 'entitygroupfield_select_widget',
      'settings' => [
        'multiple' => TRUE,
        'required' => FALSE,
      ],
    ]);

    // Now we should see the widget.
    $this->drupalGet('/node/add/article');
    $page = $this->getSession()->getPage();
    $groups_widget = $page->findAll('css', '#edit-group-content');
    $this->assertNotEmpty($groups_widget);
    $groups_select = $page->findField('group_content[add_more][add_relation]');
    $this->assertNotEmpty($groups_select);
    // Since this is an article, only 'A' type groups should be options.
    $this->assertNotEmpty($groups_select->find('named', ['option', 1]));
    $this->assertNotEmpty($groups_select->find('named', ['option', 2]));
    $this->assertEmpty($groups_select->find('named', ['option', 3]));
    $this->assertEmpty($groups_select->find('named', ['option', 4]));
    $groups_select->setValue('1');
    $add_group_button = $page->findButton('Add to Group');
    $this->assertNotEmpty($add_group_button);
    $add_group_button->click();
    $groups_table = $assert_session->waitForElementVisible('css', '#edit-group-content-wrapper table');
    $this->assertNotEmpty($groups_table);
    // @todo Assert that the table looks right.
    $groups_select = $page->findField('group_content[add_more][add_relation]');
    $this->assertNotEmpty($groups_select);
    // Make sure the group we added is no longer an option in the select list.
    // @todo We'll have to be more careful with this once we correctly handle
    //   group cardinality settings.
    // @see https://www.drupal.org/project/entitygroupfield/issues/3152719
    $this->assertEmpty($groups_select->find('named', ['option', 1]));
    $this->assertNotEmpty($groups_select->find('named', ['option', 2]));
    $this->assertEmpty($groups_select->find('named', ['option', 3]));
    $this->assertEmpty($groups_select->find('named', ['option', 4]));

    // Add the 2nd group.
    $groups_select->setValue('2');
    $add_group_button = $page->findButton('Add to Group');
    $this->assertNotEmpty($add_group_button);
    $add_group_button->click();
    // Wait for a row with 'group-A2' to appear in the 'Groups' table.
    $group_a2_cell = $assert_session->waitForElementVisible('xpath', '//div[@id="edit-group-content-wrapper"]//table/tbody/tr/td//div[contains(text(), "group-A2")]');
    $this->assertNotEmpty($group_a2_cell);

    // Now that we used both groups, there shouldn't be an add button anymore.
    $groups_select = $page->findField('group_content[add_more][add_relation]');
    $this->assertEmpty($groups_select);

    // @todo Test trying to remove a group from the table.
  }

  /**
   * Test the 'autocomplete' group field widget on page nodes.
   */
  protected function checkPageAutocompleteWidget() {
    // Configure pages to use the autocomplete widget.
    $this->configureFormDisplay('page', [
      'type' => 'entitygroupfield_autocomplete_widget',
      'settings' => [
        'multiple' => TRUE,
        'required' => FALSE,
      ],
    ]);

    // Now we should see the widget.
    $this->drupalGet('/node/add/page');
    $page = $this->getSession()->getPage();
    $groups_widget = $page->findAll('css', '#edit-group-content');
    $this->assertNotEmpty($groups_widget);
    $groups_autocomplete = $page->findField('group_content[add_more][add_relation]');
    $this->assertNotEmpty($groups_autocomplete);

    // Actually test the autocomplete.
    $groups_autocomplete->setValue('group');
    $this->getSession()->getDriver()->keyDown($groups_autocomplete->getXpath(), ' ');
    $this->assertSession()->waitOnAutocomplete();

    // Check the autocomplete results.
    $results = $page->findAll('css', '.ui-autocomplete li');
    $this->assertCount(4, $results);
    $this->assertEquals($this->groupA1->label(), $results[0]->getText());
    $this->assertEquals($this->groupA2->label(), $results[1]->getText());
    $this->assertEquals($this->groupB1->label(), $results[2]->getText());
    $this->assertEquals($this->groupB2->label(), $results[3]->getText());

    // Click on the last result and make sure it works.
    $page->find('css', '.ui-autocomplete li:last-child a')->click();
    $this->assertSession()->fieldValueEquals('group_content[add_more][add_relation]', $this->groupB2->label() . ' (' . $this->groupB2->id() . ')');
  }

  /**
   * Test the 'select' group field widget on page nodes.
   */
  protected function checkPageSelectWidget() {
    $assert_session = $this->assertSession();

    // Configure pages to use the select widget.
    $this->configureFormDisplay('page', [
      'type' => 'entitygroupfield_select_widget',
      'settings' => [
        'multiple' => TRUE,
        'required' => FALSE,
      ],
    ]);
    $this->drupalGet('/node/add/page');
    $page = $this->getSession()->getPage();
    $groups_widget = $page->findAll('css', '#edit-group-content');
    $this->assertNotEmpty($groups_widget);
    $groups_select = $page->findField('group_content[add_more][add_relation]');
    $this->assertNotEmpty($groups_select);
    // Since this is a page node, all 4 groups should be options.
    $this->assertNotEmpty($groups_select->find('named', ['option', 1]));
    $this->assertNotEmpty($groups_select->find('named', ['option', 2]));
    $this->assertNotEmpty($groups_select->find('named', ['option', 3]));
    $this->assertNotEmpty($groups_select->find('named', ['option', 4]));

    // @todo: Anything else we should test with both A and B groups that we
    // didn't already cover with articles?
  }

  /**
   * Configures the form display mode for the 'group_content' field.
   *
   * @param string $bundle
   *   The node type to configure.
   * @param array $config
   *   The configuration array to use for the 'group_content' field.
   */
  protected function configureFormDisplay($bundle, array $config) {
    \Drupal::service('entity_display.repository')
      ->getFormDisplay('node', $bundle)
      ->setComponent('group_content', $config)
      ->save();
  }

}
