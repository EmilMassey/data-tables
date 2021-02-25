<?php

namespace App;

final class Events
{
    const USER_CREATED = 'user.created';
    const USER_ACCESS_TO_TABLE_GRANTED = 'user.access_to_table_granted';

    private function __construct()
    {
        // noop
    }
}
