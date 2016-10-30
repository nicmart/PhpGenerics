Feature: Name Transformation
  In order to transform types in the code
  As a developer
  I need to be able to transform names in the code

  Background:
    Given the default name manipulator
    And nodes of type 'Stmt\UseUse' do not map on subnodes 'name'
    And nodes of type 'Stmt\Namespace_' do not map on subnodes 'name'
    And we recurse 'bottom-up'

  Scenario: Converting a namespace
    Given the code:
      """
      namespace A\B\D;
      """
    And the transformation 'A\B\D' -> 'C\E'
    When I build the node transformation
    And I apply it to the code
    Then the code should remain unchanged

  Scenario: Converting a use statement
    Given the code:
      """
      use A\B\D;
      use A\B\E;
      """
    And the transformation 'A\B\D' -> 'C\E'
    When I build the node transformation
    And I apply it to the code
    Then the code should be transformed to:
    """
    use C\E;
    use A\B\E;
    """

  Scenario: Converting a use statement
    Given the code:
    """
    use A\B\D as Blablabla;
    D::foo();
    """
    And the transformation:
    | from    | to       |
    | A\B\D   | C\E      |
    | D       | E        |
    When I build the node transformation
    And I apply it to the code
    Then the code should be transformed to:
    """
    use C\E as Blablabla;
    \E::foo();
    """
