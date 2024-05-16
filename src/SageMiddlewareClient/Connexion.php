<?php

namespace Raorsa\SageMiddlewareClient;


use Illuminate\Support\Facades\Http;

/**
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 */
class Connexion
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */

    const URL_LOGIN = 'login';

    private static $instances = [];
    private $url = null;
    private $login = null;
    private $verify = true;
    private $loginValues = [];


    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    {
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static field. On subsequent runs, it returns the client existing
     * object stored in the static field.
     *
     * This implementation lets you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     */
    public static function getInstance(): Connexion
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public static function connect(string $url, string $email, string $password, bool $verify = true, string $name = null)
    {
        $instance = self::getInstance();
        $instance->url = $url;
        $instance->verify = $verify;
        $instance->loginValues['email'] = $email;
        $instance->loginValues['password'] = $password;
        $instance->loginValues['name'] = $name;

        return $instance;
    }

    public static function open(string $url, string $token, bool $verify = true)
    {
        $instance = self::getInstance();
        $instance->url = $url;
        $instance->login = $token;
        $instance->verify = $verify;

        return $instance;
    }

    private function login(): void
    {
        if (isset($this->loginValues['email'], $this->loginValues['password']) && is_null($this->login)) {
            if ($this->verify) {
                $this->login = Http::withoutVerifying()->post($this->url . self::URL_LOGIN, $this->loginValues)->json('token');
            } else {
                $this->login = Http::post($this->url . self::URL_LOGIN, $this->loginValues)->json('token');
            }
        }
    }

    public function call($method, &$token = '')
    {
        $this->login();
        $token = $this->login;
        if ($this->verify) {
            $response = Http::withoutVerifying()->withToken($this->login)->get($this->url . $method);
        } else {
            $response = Http::withToken($this->login)->get($this->url . $method);
        }
        return $response;
    }

}