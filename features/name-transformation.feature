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

  Scenario: Converting a use statement
    Given the code:
      """
      use A\B\D;
      use A\B\E;
      """
    And the transformation 'A\B\D' -> 'C\E'
    When I apply the foregoing
    Then the code should be transformed to:
    """
    use C\E;
    use A\B\E;
    """

  Scenario: Converting a use statement
    Given the code:
    """
    use A\B\D;
    D::foo();
    """
    And the transformation:
    | from    | to       |
    | A\B\D   | C\E      |
    | D       | E        |
    When I apply the foregoing
    Then the code should be transformed to:
    """
    use C\E;
    \E::foo();
    """
