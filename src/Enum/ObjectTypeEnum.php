<?php

namespace Vendi\VendiAlgoliaWordpressBase\Enum;

enum ObjectTypeEnum: string
{
    case WP_Post = 'post';
    case WP_Term = 'term';
    case Pdf = 'pdf';
}
