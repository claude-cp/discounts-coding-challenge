<?php

declare(strict_types=1);

namespace App\Discount\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

class AllDiscountsAppliedEvent extends GenericEvent
{
    // not used for the scope of the app, but to showcase some potential domain events
}
