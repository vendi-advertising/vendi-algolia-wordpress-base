<?php

namespace Vendi\VendiAlgoliaWordpressBase\Service;

use Algolia\AlgoliaSearch\Response\BatchIndexingResponse;
use Algolia\AlgoliaSearch\Response\NullResponse;
use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;

class AlgoliaService
{
    public function getSearchClient(): SearchClient
    {
        return SearchClient::create(
            'VVW3I46W8Q',
            '7a812c2b057dd9919c851604b9763766',
        );
    }

    public function getAlgoliaIndex(): SearchIndex
    {
        return $this->getSearchClient()->initIndex(
          'triston_testing'
        );
    }

    public function addObjectToIndex($obj): NullResponse|BatchIndexingResponse
    {
        return $this->getAlgoliaIndex()->saveObject($obj);

    }
}