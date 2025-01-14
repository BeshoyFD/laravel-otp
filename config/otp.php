<?php

/*
 * @copyright 2018 Hilmi Erdem KEREN
 * @license MIT
 */

return [
    /*
     * The password generator option allows you to decide
     * which generator implementation to be used when
     * generating new passwords.
     *
     * Here are the options:
     *  - string
     *  - numeric
     *  - numeric-no-0
     */

    'password_generator' => 'string',

    /*
     * The name of the table to be used to store
     * the otp tokens.
     */

    'table'   => 'otp_tokens',

    /*
     * The expiry time of the tokens in minutes.
     */

    'expires' => 15, // in minutes.

    /*
     * The expiry time of the otp token in cookie, to request otp again
     */

    'token_expire' => 129600, // in minutes. , 90 day

    
    /*
     * The default notification channels of the
     * token notification.
     *
     * Accepts:
     * array
     * comma separated string
     */

    'default_channels' => 'mail',
    
     /*
    * custom notification class
    */

    //'notification' =>  fn($token) => new \App\Notifications\OtpNotification($token),
];
