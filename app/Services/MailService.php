<?php


namespace App\Services;


use App\User;
use Illuminate\Mail\Message;
use Mail;
use Password;

class MailService
{
    public static function SendNewUserMail($email, $first_name, $last_name, $link)
    {
        Mail::send('emails.new-account', ['name' => $first_name . ' ' . $last_name, 'link' => $link], static function (Message $message) use ($email) {
            $message->from('sagaprojectmail@au.dk');
            $message->to($email)
                ->subject('SAGA Project - New Account');
        });
    }

    public static function SendInviteEmail($email, $first_name, $last_name, $link)
    {
        Mail::send('emails.invitation', ['name' => $first_name . ' ' . $last_name,
            'link' => $link], static function (Message $message) use ($email) {
            $message->from('sagaprojectmail@au.dk');
            $message->to($email)
                ->subject('SAGA Project - Class Invitation');
        });
    }

    public static function SendResetPasswordMail($email)
    {
        try {

            $user = User::where('email', $email)->first();
            $token = Password::getRepository()->create($user);
            $link = route('password.reset', ['token' => $token]);

            Mail::send(['text' => 'emails.reset-password'], ['link' => $link, 'name' => $user->u_first_name . ' ' . $user->u_last_name, 'token' => $token], function (Message $message) use ($user) {
                $message->subject(config('app.name') . ' Password Reset Link');
                $message->setContentType('text/html');

                $message->to($user->email);
            });

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' in file: ' . $e->getFile() . ' at line: ' . $e->getLine()];

        }
        return ['status' => true, 'message' => 'success'];
    }
}
