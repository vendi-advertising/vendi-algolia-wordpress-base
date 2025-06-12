<?php

namespace Vendi\VendiAlgoliaWordpressBase\Enum;

enum CommandRunModeEnum: string
{
    case DRY_RUN = 'Dry-run';
    case LIVE = 'Live';
}
