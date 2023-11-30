<?php

namespace Vendi\VendiAlgoliaWordpressBase\Tests\Service;

use Algolia\AlgoliaSearch\Response\BatchIndexingResponse;
use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Vendi\VendiAlgoliaWordpressBase\Service\AlgoliaService;
use PHPUnit\Framework\TestCase;

class AlgoliaServiceTest extends TestCase
{
    public function testSearchClientReturned(){
        $algoliaService = new AlgoliaService();
        $this->assertInstanceOf(SearchClient::class, $algoliaService->getSearchClient());
    }

    public function testSearchIndexReturned(){
        $algoliaService = new AlgoliaService();
        $this->assertInstanceOf(SearchIndex::class, $algoliaService->getAlgoliaIndex());
    }

    public function testSendSomethingToAlgolia(){
        $randomString = bin2hex(random_bytes(10));
        $algoliaService = new AlgoliaService();
        $result = $algoliaService->addObjectToIndex(['objectID' => '1234', 'test' => 'potato', 'random' => $randomString]);
        $this->assertInstanceOf(BatchIndexingResponse::class, $result);
        sleep(1);
        $objectFromAlgolia = $algoliaService->getAlgoliaIndex()->getObject('1234');
        $this->assertIsArray($objectFromAlgolia);
        $this->assertArrayHasKey('random', $objectFromAlgolia);
        $this->assertSame($randomString, $objectFromAlgolia['random']);
    }

}
