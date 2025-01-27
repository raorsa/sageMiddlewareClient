<?php

namespace Raorsa\SageMiddlewareClient;

class DeliveryNotesClients extends DeliveryNotes
{
    protected function basePath(): string{
        return 'delivery';
    }

}