Feature:
  As a user
  I should be able to navigate the different pages of this site

  Scenario: Navigate the homepage
    Given I am on the homepage
    Then the response should contain "Hello World"
    Then I take a screenshot named "homepage"
