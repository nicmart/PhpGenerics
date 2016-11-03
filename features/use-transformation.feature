Feature:
  In order to correctly transform use statements
  As a developer
  I need some special behaviours for use statements.

  Background:
    Given the default name manipulator
    And nodes of type 'Stmt\UseUse' do not map on subnodes 'name'
    And nodes of type 'Stmt\Namespace_' do not map on subnodes 'name'

  Scenario: Erasure of use statements of primitive types
    Given the code:
    """
    use Foo\Bar;
    echo "bye";
    """
    And the constant type transformation 'string'
    When I build the name transformer from the type transformer
    And I build the node transformer from the name transformer
    And I make the context dependent transformer 'top-down'-recursive
    And I apply it to the code
    Then the code should be transformed to:
    """
    echo "bye";
    """
