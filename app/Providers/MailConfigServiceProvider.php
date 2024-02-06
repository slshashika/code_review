<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\EmailSetting;

use Config;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Schema::hasTable('email_settings')) {
            $mail = EmailSetting::where('status',1)->get()->first();
            if ($mail)
            {
                $config = array(
                    'driver'     => $mail->mailer,
                    'host'       => $mail->host,
                    'port'       => $mail->port,
                    'from'       => [
                                    'address' => $mail->from_address, 
                                    'name' =>  $mail->from_name
                                ],
                    'encryption' => $mail->encryption,
                    'username'   => $mail->username,
                    'password'   => $mail->password,
                    'sendmail'   => '/usr/sbin/sendmail -bs',
                    'pretend'    => false,
                );
                
                Config::set('mail', $config);

            }
        }
    }
}
