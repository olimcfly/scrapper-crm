<?php

declare(strict_types=1);

return [
    'host'         => getenv('MAIL_HOST')         ?: 'gazelle.o2switch.net',
    'port'         => (int) (getenv('MAIL_PORT')  ?: 465),
    'username'     => getenv('MAIL_USERNAME')      ?: '',
    'password'     => getenv('MAIL_PASSWORD')      ?: '',
    'from_address' => getenv('MAIL_FROM_ADDRESS')  ?: '',
    'from_name'    => getenv('MAIL_FROM_NAME')     ?: 'Coralie Montreuil',
    'encryption'   => getenv('MAIL_ENCRYPTION')    ?: 'ssl',
];
