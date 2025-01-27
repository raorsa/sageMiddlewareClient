<?php

namespace Raorsa\SageMiddlewareClient;

class DeliveryNotesProviders extends DeliveryNotes
{
    protected function basePath(): string
    {
        return 'delivery-provider';
    }
}