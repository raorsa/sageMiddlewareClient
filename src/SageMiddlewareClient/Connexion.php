<?php

namespace Raorsa\SageMiddlewareClient;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Connexion
{
    const URL_LOGIN = 'login';
    private static $instances = [];
    private $url = null;
    private $login = null;
    private $verify = true;
    private $loginValues = [];
    private HttpClientInterface $httpClient;


    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(HttpClientInterface $httpClient): Connexion
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($httpClient);
        }

        return self::$instances[$cls];
    }

    public function getUrl(): null|string
    {
        return $this->url;
    }

    public function connect(string $url, string $email, string $password, bool $verify = true, string $name = 'SageClient'): void
    {
        $this->url = $url;
        $this->verify = $verify;
        $this->loginValues['email'] = $email;
        $this->loginValues['password'] = $password;
        $this->loginValues['name'] = $name;
    }

    public function open(string $token): void
    {
        $this->login = $token;
    }

    private function login(): void
    {
        if (isset($this->loginValues['email'], $this->loginValues['password']) && is_null($this->login)) {
            $request = $this->httpClient->request(
                'POST',
                $this->url . self::URL_LOGIN,
                [
                    "verify_peer" => $this->verify,
                    "verify_host" => $this->verify,
                    "body" => $this->loginValues,
                ]);

            if ($request->getStatusCode() >= 200 && $request->getStatusCode() < 300) {
                $data = json_decode($request->getContent());
                if (isset($data->token)) {
                    $this->login = $data->token;
                }
            }
        }
    }

    public function call(string $method, string &$token = ''): ResponseInterface
    {
        do {
            $this->login();

            $response = $this->httpClient->request('GET', $this->url . $method, ['auth_bearer' => $this->login]);
            if ($response->getStatusCode() === 405) {
                $this->login = null;
                $block = true;
            } else {
                $block = false;
            }
        } while ($block);

        $token = $this->login;
        return $response;
    }

    public function setVerify(bool $verify): void
    {
        $this->verify = $verify;
    }

    public static function mount(string $url, string $email, string $password, bool $verify = true, string $name = 'SageClient'): Connexion
    {
        $connection = Connexion::getInstance(HttpClient::create());
        $connection->url = $url;
        $connection->verify = $verify;
        $connection->loginValues['email'] = $email;
        $connection->loginValues['password'] = $password;
        $connection->loginValues['name'] = $name;

        return $connection;
    }

    public static function link(string $token): Connexion
    {
        $connection = Connexion::getInstance(HttpClient::create());
        $connection->login = $token;
        return $connection;
    }

}