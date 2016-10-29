Feature: Name Transformation
  In order to transform types in the code
  As a developer
  I need to be able to transform names in the code

  Scenario: Converting a namespace
    Given the code:
      """
      namespace A\B\D;
      """
    And the transformation 'A\B\D' -> 'C\E'
    When I apply the foregoing
    Then the code should remain unchanged
