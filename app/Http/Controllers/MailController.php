<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Denouncement;
use App\Models\Informer;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;        
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailController extends Controller
{
    public static function baseSendAdminMail($denouncementId, $placeholders) {
        $denouncement = Denouncement::find($denouncementId);
        $user = User::where([
                'company_id' => $denouncement->company_id,
                'rol' => User::ROL_ADMIN,
                'enabled' => true
            ])
            ->first();

        if (is_null($user)) {
            return;
        }

        self::baseSendMail($denouncement, $placeholders, $user, '', false);
    }

    public static function baseSendInvestigatorMail($denouncementId, $placeholders) {
        $denouncement = Denouncement::find($denouncementId);
        $user = User::find($denouncement->investigator_id);

        self::baseSendMail($denouncement, $placeholders, $user, '', false);
    }

    public static function baseSendInformerMail($denouncementId, $placeholders) {
        $denouncement = Denouncement::find($denouncementId);
        $informer = Informer::find($denouncement->informer_id);
        $company = Company::find($denouncement->company_id);

        $url = 'https://www.ethosperu.com/' . $company->slug;

        self::baseSendMail($denouncement, $placeholders, $informer, $url, true);
    }

    private static function baseSendMail($denouncement, $placeholders, $user, $url, $isIdHashed) {
        $fullName = ! is_null($user->first_name) ? $user->first_name . ' ' . $user->last_name : '';

        $message = str_replace("%id%", $isIdHashed ? $denouncement->id_in_hash : $denouncement->id, $placeholders['message']);
        $message = str_replace("%url%", $url, $message);

        $data = [
            'title' => 'Hola ' . $fullName . ',',
            'body' => $message 
        ];

        $emailTo = $user->email;
        $subject = str_replace("%id%", $denouncement->id, $placeholders['subject']);

        self::setNotificationEmailFromConfig();

        try {
            Mail::send('emails.welcome', $data, function ($message) use ($emailTo, $subject) {
            
                $message->to($emailTo)->subject($subject);
            });
        } catch (Throwable $e) {
            Log::error('MAIL:' . $e);
        }
    }

    public static function simpleSendMail($data, $toEmail, $subject, $isNotificationEmail = true) {
        
        if ($isNotificationEmail) {
            self::setNotificationEmailFromConfig();
        } else {
            self::setRestoreEmailFromConfig();
        }

        try {
            Mail::send('emails.welcome', $data, function ($message) use ($toEmail, $subject) {
                    
                        $message->to($toEmail)->subject($subject);
                    });
        } catch (Throwable $e) {
            Log::error('MAIL:' . $e);
        }
    }

    private static function setNotificationEmailFromConfig() {
        config([
            'mail.username' => self::getConfig('notification_email_address'),
            'mail.password' => self::getConfig('notification_email_password'),
            'mail.from.address' => self::getConfig('notification_email_address'),
            'mail.from.name' => self::getConfig('notification_email_name'),
        ]);
    }

    private static function setRestoreEmailFromConfig() {
        config([
            'mail.username' => self::getConfig('restore_email_address'),
            'mail.password' => self::getConfig('restore_email_password'),
            'mail.from.address' => self::getConfig('restore_email_address'),
            'mail.from.name' => self::getConfig('restore_email_name'),
        ]);
    }

    private static function getConfig($key) {
        return Setting::where('key', $key)->first()->value;
    }
}
