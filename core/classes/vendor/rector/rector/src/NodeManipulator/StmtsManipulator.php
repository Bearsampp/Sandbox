<?php

declare (strict_types=1);
namespace Rector\NodeManipulator;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\Contract\PhpParser\Node\StmtsAwareInterface;
use Rector\DeadCode\NodeAnalyzer\ExprUsedInNodeAnalyzer;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
use Rector\PhpParser\Comparing\NodeComparator;
use Rector\PhpParser\Node\BetterNodeFinder;
final class StmtsManipulator
{
    /**
     * @readonly
     */
    private SimpleCallableNodeTraverser $simpleCallableNodeTraverser;
    /**
     * @readonly
     */
    private BetterNodeFinder $betterNodeFinder;
    /**
     * @readonly
     */
    private NodeComparator $nodeComparator;
    /**
     * @readonly
     */
    private ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer;
    public function __construct(SimpleCallableNodeTraverser $simpleCallableNodeTraverser, BetterNodeFinder $betterNodeFinder, NodeComparator $nodeComparator, ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer)
    {
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
        $this->betterNodeFinder = $betterNodeFinder;
        $this->nodeComparator = $nodeComparator;
        $this->exprUsedInNodeAnalyzer = $exprUsedInNodeAnalyzer;
    }
    /**
     * @param Stmt[] $stmts
     * @return null|\PhpParser\Node\Expr|\PhpParser\Node\Stmt
     */
    public function getUnwrappedLastStmt(array $stmts)
    {
        if ($stmts === []) {
            return null;
        }
        $lastStmtKey = \array_key_last($stmts);
        $lastStmt = $stmts[$lastStmtKey];
        if ($lastStmt instanceof Expression) {
            $lastStmt->expr->setAttribute(AttributeKey::COMMENTS, $lastStmt->getAttribute(AttributeKey::COMMENTS));
            return $lastStmt->expr;
        }
        return $lastStmt;
    }
    /**
     * @param Stmt[] $stmts
     * @return Stmt[]
     */
    public function filterOutExistingStmts(ClassMethod $classMethod, array $stmts) : array
    {
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable((array) $classMethod->stmts, function (Node $node) use(&$stmts) {
            foreach ($stmts as $key => $assign) {
                if (!$this->nodeComparator->areNodesEqual($node, $assign)) {
                    continue;
                }
                unset($stmts[$key]);
            }
            return null;
        });
        return $stmts;
    }
    /**
     * @param StmtsAwareInterface|Stmt[] $stmtsAware
     */
    public function isVariableUsedInNextStmt($stmtsAware, int $jumpToKey, string $variableName) : bool
    {
        if ($stmtsAware instanceof StmtsAwareInterface && $stmtsAware->stmts === null) {
            return \false;
        }
        $stmts = \array_slice($stmtsAware instanceof StmtsAwareInterface ? $stmtsAware->stmts : $stmtsAware, $jumpToKey, null, \true);
        $variable = new Variable($variableName);
        return (bool) $this->betterNodeFinder->findFirst($stmts, fn(Node $subNode): bool => $this->exprUsedInNodeAnalyzer->isUsed($subNode, $variable));
    }
}
