<?php

namespace Vendi\VendiAlgoliaWordpressBase\Tests\Service;

use Algolia\AlgoliaSearch\Model\Search\SaveObjectResponse;
use Algolia\AlgoliaSearch\Response\BatchIndexingResponse;
use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use PHPUnit\Framework\TestCase;
use Vendi\VendiAlgoliaWordpressBase\Utilities\AlgoliaUtility;

class AlgoliaServiceTest extends TestCase
{
    public function testSendSomethingToAlgolia()
    {
        $indexName    = AlgoliaUtility::getInstance()->getAlgoliaIndexName();
        $randomString = bin2hex(random_bytes(10));
        $result       = AlgoliaUtility::getInstance()->getAlgoliaClient()->saveObject($indexName, ['objectID' => '1234', 'test' => 'potato', 'random' => $randomString]);
        $this->assertInstanceOf(SaveObjectResponse::class, $result);
        sleep(1);
        $objectFromAlgolia = AlgoliaUtility::getInstance()->getAlgoliaClient()->getObject($indexName, '1234');
        $this->assertIsArray($objectFromAlgolia);
        $this->assertArrayHasKey('random', $objectFromAlgolia);
        $this->assertSame($randomString, $objectFromAlgolia['random']);
    }
}
