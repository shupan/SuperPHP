<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Support\Structure;

use PHPUnit\Framework\TestCase;
use Super\Support\Structure\LinkedTable;
use Super\Support\Structure\Node;


class LinkedTableTest extends TestCase
{


    public function setUp()
    {

    }

    public function testCRUD()
    {

        $linkedTable = new LinkedTable(1,'head');
        $node = new Node(2,'n2');
        $linkedTable->addNode($node);

        $node = new Node(3,'n3');
        $linkedTable->addNode($node);

        $node = new Node(4,'n4');
        $linkedTable->addNode($node);
        $linkedTable->printNode();

        $linkedTable->deleteNode(2);
        $linkedTable->deleteNode(3);
        $linkedTable->deleteNode(4);
        $linkedTable->printNode();

        $linkedTable->deleteNode(1);
        $linkedTable->printNode();

    }
}