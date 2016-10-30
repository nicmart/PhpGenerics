Feature: Node-Mapping
  In order to make tree traversal easier
  As a Developer
  I want to treat nodes as self-recursive functors

  Background:
    Given the default name manipulator
    And nodes of type 'Stmt\UseUse' do not map on subnodes 'name'
    And nodes of type 'Stmt\Namespace_' do not map on subnodes 'name'
    And we recurse 'top-down'

  Scenario: Mapping a node
    Given the code:
    """
    Foo::bar();
    """
    And the name transformation that appends 'Transformed' to names
    When I build the node transformation
    And I apply it to the code
    Then the code should be transformed to:
    """
    FooTransformed::bar();
    """

  Scenario: Mapping skips certain subnodes
    Given the code:
    """
    namespace MyNamespace;
    use A\B\C as Foo;
    """
    And the name transformation that appends 'Transformed' to names
    When I build the node transformation
    And I apply it to the code
    Then the code should remain unchanged


