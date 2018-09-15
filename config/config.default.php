<?php

// To change settings create a config.php

return [
    // MySQL-Connection Settings
    'database'                => [
        'host'     => env('MYSQL_HOST', (env('CI', false) ? 'mariadb' : 'localhost')),
        'database' => env('MYSQL_DATABASE', 'engelsystem'),
        'username' => env('MYSQL_USER', 'root'),
        'password' => env('MYSQL_PASSWORD', ''),
    ],

    // For accessing stats
    'api_key'                 => '',

    // Enable maintenance mode (show a static page)
    'maintenance'             => (bool)env('MAINTENANCE', false),

    // Set to development to enable debugging messages
    'environment'             => env('ENVIRONMENT', 'production'),

    // URL to the angel faq and job description
    'faq_url'                 => env('FAQ_URL', 'https://events.ccc.de/congress/2013/wiki/Static:Volunteers'),

    // Contact email address, linked on every page
    'contact_email'           => env('CONTACT_EMAIL', 'mailto:ticket@c3heaven.de'),

    // From address of all emails
    'no_reply_email'          => env('NO_REPLY_EMAIL', 'noreply@engelsystem.de'),

    // Default theme, 1=style1.css
    'theme'                   => env('THEME', 1),

    // Available themes
    'available_themes'        => [
        '6' => 'Engelsystem 34c3 dark (2017)',
        '5' => 'Engelsystem 34c3 light (2017)',
        '4' => 'Engelsystem 33c3 (2016)',
        '3' => 'Engelsystem 32c3 (2015)',
        '2' => 'Engelsystem cccamp15',
        '0' => 'Engelsystem light',
        '1' => 'Engelsystem dark'
    ],

    // Rewrite URLs with mod_rewrite
    'rewrite_urls'            => true,

    // Number of News shown on one site
    'display_news'            => 6,

    // Users are able to sign up
    'registration_enabled'    => (bool)env('REGISTRATION_ENABLED', true),

    // Only arrived angels can sign up for shifts
    'signup_requires_arrival' => false,

    // Anzahl Stunden bis zum Austragen eigener Schichten
    'last_unsubscribe'        => 3,

    // Setzt den zu verwendenden Crypto-Algorithmus (entsprechend der Dokumentation von crypt()).
    // Falls ein Benutzerpasswort in einem anderen Format gespeichert ist,
    // wird es bei der ersten Benutzung des Klartext-Passworts in das neue Format
    // konvertiert.
    //  MD5         '$1'
    //  Blowfish    '$2y$13'
    //  SHA-256     '$5$rounds=5000'
    //  SHA-512     '$6$rounds=5000'
    'crypt_alg'               => '$6$rounds=5000',

    'min_password_length'     => 8,

    // Wenn Engel beim Registrieren oder in ihrem Profil eine T-Shirt Größe angeben sollen, auf true setzen:
    'enable_tshirt_size'      => true,

    // Number of shifts to freeload until angel is locked for shift signup.
    'max_freeloadable_shifts' => 2,

    // local timezone
    'timezone'                => env('TIMEZONE', 'Europe/Berlin'),

    // weigh every shift the same
    //'shift_sum_formula'       => 'SUM(`end` - `start`)',

    // Multiply 'night shifts' and freeloaded shifts (start or end between 2 and 6 exclusive) by 2
    'shift_sum_formula'       => '
        SUM(
            (1 +
                (
                  (HOUR(FROM_UNIXTIME(`Shifts`.`end`)) > 2 AND HOUR(FROM_UNIXTIME(`Shifts`.`end`)) < 6)
                  OR (HOUR(FROM_UNIXTIME(`Shifts`.`start`)) > 2 AND HOUR(FROM_UNIXTIME(`Shifts`.`start`)) < 6)
                  OR (HOUR(FROM_UNIXTIME(`Shifts`.`start`)) <= 2 AND HOUR(FROM_UNIXTIME(`Shifts`.`end`)) >= 6)
                )
            )
            * (`Shifts`.`end` - `Shifts`.`start`)
            * (1 - 3 * `ShiftEntry`.`freeloaded`)
        )
    ',

    // Voucher calculation
    'voucher_settings'        => [
        'initial_vouchers'   => 0,
        'shifts_per_voucher' => 1,
    ],

    // Available locales in /locale/
    'locales'                 => [
        'de_DE.UTF-8' => 'Deutsch',
        'en_US.UTF-8' => 'English',
    ],

    'default_locale' => env('DEFAULT_LOCALE', 'en_US.UTF-8'),

    // Available T-Shirt sizes, set value to null if not available
    'tshirt_sizes'   => [
        'S'    => 'S',
        'S-G'  => 'S Girl',
        'M'    => 'M',
        'M-G'  => 'M Girl',
        'L'    => 'L',
        'L-G'  => 'L Girl',
        'XL'   => 'XL',
        'XL-G' => 'XL Girl',
        '2XL'  => '2XL',
        '3XL'  => '3XL',
        '4XL'  => '4XL'
    ],

    // IP addresses of reverse proxies that are trusted, can be an array or a comma separated list
    'trusted_proxies' => env('TRUSTED_PROXIES', ['127.0.0.0/8', '::ffff:127.0.0.0/8', '::1/128']),
];
