<?php

ini_set('xdebug.max_nesting_level', 3000);

require 'vendor/autoload.php';

use PhpParser\Node;

class MyNodeVisitor extends PhpParser\NodeVisitorAbstract
{
    public $filename = null;
    public $data = array();
    public function leaveNode(Node $node) {
        if (($node instanceof Node\Scalar\String_) || ($node instanceof Node\Stmt\InlineHTML)) {
            $new_data = array(
                'filename' => $this->filename,
                'line' => $node->getLine(),
                'value' => (string)$node->value,
            );
            array_push($this->data, $new_data);
        }
    }
}

if(count($argv) < 2) {
  exit(1);
}

$code = file_get_contents($argv[1]);
if($code === false) {
  exit(1);
}

$parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
$traverser = new PhpParser\NodeTraverser;

$visitor = new MyNodeVisitor;
$visitor->filename = $argv[1];
$traverser->addVisitor($visitor);


try {
    $stmts = $parser->parse($code);
    // traverse
    $stmts = $traverser->traverse($stmts);

    // $stmts is an array of statement nodes
} catch (PhpParser\Error $e) {
    fwrite(STDERR, 'Parser Error: ' . $e->getMessage());
    exit(1);
}

echo json_encode($visitor->data);
