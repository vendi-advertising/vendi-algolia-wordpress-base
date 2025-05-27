<?php

namespace Vendi\VendiAlgoliaWordpressBase\Entity;

use DateTimeImmutable;
use Vendi\VendiAlgoliaWordpressBase\Enum\ObjectTypeEnum;
use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeAttribute as Serialize;

class CommonWpPost extends CommonObject
{
    #[Serialize]
    public ?string $content = null;

    #[Serialize]
    public ?string $title = null;

    #[Serialize]
    public ?string $entityUrl = null;

    #[Serialize]
    public ?string $imageUrl = null;

    #[Serialize]
    public ?string $imageAlt = null;

    #[Serialize]
    public ?DateTimeImmutable $dateCreated = null;

    #[Serialize]
    public ?DateTimeImmutable $dateModified = null;

    #[Serialize]
    public ?array $taxonomies = [];

    public function __construct(
        int|string $id,
        #[Serialize]
        public string $postType
    ) {
        parent::__construct($id, ObjectTypeEnum::WP_Post);
    }

    public function jsonSerialize(): array
    {
        $ret = parent::jsonSerialize();

        $this->maybeConvertDateTimeFieldToParts($ret, 'datetime-created');
        $this->maybeConvertDateTimeFieldToParts($ret, 'datetime-modified');

        return $ret;
    }

    private function maybeConvertDateTimeFieldToParts(array &$ret, string $fieldName, bool $includeYear = true, bool $includeMonth = true, bool $includeDay = true): void
    {
        if (isset($ret[$fieldName])) {
            $date = (new DateTimeImmutable)->setTimestamp($ret[$fieldName]);
            if ($includeYear) {
                $ret[$fieldName.'-year'] = $date->format('Y');
            }
            if ($includeMonth) {
                $ret[$fieldName.'-month'] = $date->format('m');
            }
            if ($includeDay) {
                $ret[$fieldName.'-day'] = $date->format('d');
            }
        }
    }
}
