<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;


class User extends Model
{
    use Notifiable;

    public const ID_GLEB = 1;
    public const ID_DIMA = 2;
    public const ID_DEVOCHKA = 4;

    public const NAME_GLEB = 'Глеб/Катя';
    public const NAME_DIMA = 'Дима';
    public const NAME_SOMEONE = 'Некто';
    public const NAME_DEVOCHKA = 'Настя';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname',
        // phone_call и phone_whatsapp совпадает с добавленным(и) последним по дате
        // number в UserPhone. phone_call Необходим для AuthController->login(Request)
        // phone_call и phone_whatsapp актуальные на данные момент номера для звонков и
        // вотсапа соответственно
        'phone_call',
        'phone_whatsapp',
        'pass_series_number',
        'birth_date',
        'pass_date',
        'pass_code',
        'inn',
        'address',
        'type',
        'RF_citizen',
        'foreign_pass_series_number',
        'approved',
        'verified_phone',
        'passport_image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    
    public function worker()
    {
        return $this->hasOne(Worker::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function posts()
    {
        return $this->hasMany(Application::class);
    }

    public function occupations()
    {
        return $this->belongsToMany(Occupation::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function phones()
    {
        return $this->belongsToMany(UserPhone::class);
    }

    /***************************************************/
    public static function can($privilege)
    {
        $privileges = self::getPrivileges();
        if (!$privileges)
        {
            return false;
        }
        return $privileges->get($privilege);
    }

    public static function getPrivileges()
    {
        if (!Session::exists('user'))
        {
            $guest = Role::find(Role::GUEST_ID);
            $user = array (
                'role' => $guest
            );
            session([
                'user' => $user
            ]);
        }

        $userIsLoggedIn = session('user_is_logged_in');
        $user = session('user');
        $role = null;

        if ($userIsLoggedIn)
        {
            $role = $user->role;
        }
        else
        {
            if (is_array($user) && key_exists('role', $user))
            {
                $role = $user['role'];
            }
        }

        if ($role === null)
        {
            return null;
        }

        $privileges = $role->privileges;

        return $privileges->keyBy('term');
    }

    public static function saveRedirectAddress($address)
    {
        session(['redirectAddress' => $address]);
    }

    /***************************************************/
    public function getImageAttribute()
    {
        return $this->passport_image;
    }

    public static function getGlebId () {
        return self::ID_GLEB;
    }

    public static function getDimaId () {
        return self::ID_DIMA;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    /*
    public function verification()
    {
        return $this->belongsTo(Verification::class);
    }*/
}