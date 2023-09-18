<?php

$trackers = [
    TrackerUser::class => [
        'events' => [
            \losthost\DB\DBEvent::INTRAN_INSERT,
        ],
        'objects' => DBUser::class,
    ],
    TrackerConnection::class => [
        'events' => [
            losthost\DB\DBEvent::AFTER_INSERT,
            losthost\DB\DBEvent::AFTER_UPDATE,
            losthost\DB\DBEvent::INTRAN_DELETE,
            losthost\DB\DBEvent::AFTER_DELETE,
        ],
        'objects' => DBConnection::class
    ],
];