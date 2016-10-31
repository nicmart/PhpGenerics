Feature: Name Transformation
  In order to transform types in the code
  As a developer
  I need to be able to transform names in the code

  Background:
    Given the default name manipulator
    And nodes of type 'Stmt\UseUse' do not map on subnodes 'name'
    And nodes of type 'Stmt\Namespace_' do not map on subnodes 'name'

  Scenario: Converting a namespace
    Given the code:
      """
      namespace A\B\D;
      """
    And the name transformation 'A\B\D' -> 'C\E'
    When I build the non-recursive node transformer from the name transformer
    And I make the transformer 'bottom-up'-recursive
    And I apply it to the code
    Then the code should remain unchanged

  Scenario: Converting a use statement
    Given the code:
      """
      use A\B\D;
      use A\B\E;
      """
    And the name transformation 'A\B\D' -> 'C\E'
    When I build the non-recursive node transformer from the name transformer
    And I make the transformer 'bottom-up'-recursive
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
    And the name transformation:
    | from    | to       |
    | A\B\D   | C\E      |
    | D       | E        |
    When I build the non-recursive node transformer from the name transformer
    And I make the transformer 'bottom-up'-recursive
    And I apply it to the code
    Then the code should be transformed to:
    """
    use C\E as Blablabla;
    \E::foo();
    """

  Scenario: Converting a class declaration
    Given the code:
    """
    class MyClass extends BaseClass implements Interface1, Interface2 {}
    """
    And the name transformation that appends 'Transformed' to names
    When I build the non-recursive node transformer from the name transformer
    And I make the transformer 'bottom-up'-recursive
    And I apply it to the code
    Then the code should be transformed to:
    """
    class MyClassTransformed extends BaseClassTransformed
      implements Interface1Transformed, Interface2Transformed {}
    """
