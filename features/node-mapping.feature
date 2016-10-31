Feature: Node-Mapping
  In order to make tree traversal easier
  As a Developer
  I want to treat nodes as self-recursive functors

  Background:
    Given the default name manipulator
    And nodes of type 'Stmt\UseUse' do not map on subnodes 'name'
    And nodes of type 'Stmt\Namespace_' do not map on subnodes 'name'

  Scenario: Mapping a node
    Given the code:
    """
    Foo::bar();
    """
    And the name transformation that appends 'Transformed' to names
    When I build the non-recursive node transformer from the name transformer
    And I make the transformer 'top-down'-recursive
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
    When I build the non-recursive node transformer from the name transformer
    And I make the transformer 'top-down'-recursive
    And I apply it to the code
    Then the code should remain unchanged

  Scenario: Identity tranformation
    Given the code:
    """
    namespace MyNamespace;
    use A\B\C as Foo;
    class A extends B implements C, D {}
    """
    And the raw node transformation:
    """
    use \NicMart\Generics\AST\Transformer\ByCallableNodeTransformer;
    use \PhpParser\Node\Name;

    return new ByCallableNodeTransformer(function($n) {
      return $n;
    });
    """
    And I make the transformer 'top-down'-recursive
    And I apply it to the code
    Then the code should remain unchanged


