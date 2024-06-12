<?php

namespace Raorsa\SageMiddlewareClient\components;


use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class connexion
{
    private const URL_LOGIN = 'login';
    private static array $instances = [];
    private string $url = "";
    private string $login = "";
    private bool $verify = true;
    private array $loginValues = [];
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
        throw new RuntimeException("Cannot unserialize a singleton.");
    }

    public static function getInstance(HttpClientInterface $httpClient): connexion
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
        if (isset($this->loginValues['email'], $this->loginValues['password']) && $this->login === '') {
            try {
                $request = $this->httpClient->request(
                    'POST',
                    $this->url . self::URL_LOGIN,
                    [
                        "verify_peer" => $this->verify,
                        "verify_host" => $this->verify,
                        "body" => $this->loginValues,
                    ]);

                if ($request->getStatusCode() >= 200 && $request->getStatusCode() < 300) {
                    $data = json_decode($request->getContent(false), false, 512, JSON_THROW_ON_ERROR);
                    if (isset($data->token)) {
                        $this->login = $data->token;
                    }
                }
            } catch (Throwable) {
            }
        }
    }

    public function call(string $method, string &$token = ''): ResponseInterface|null
    {
        $response = null;
        do {
            $this->login();
            try {
                $response = $this->httpClient->request('GET', $this->url . $method, ['auth_bearer' => $this->login]);
                if ($response->getStatusCode() === 405) {
                    $this->login = "";
                    $block = true;
                } else {
                    $block = false;
                }
            } catch (Throwable) {
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

    public static function mount(string $url, string $email, string $password, bool $verify = true, string $name = 'SageClient'): connexion
    {
        $connection = self::getInstance(HttpClient::create());
        $connection->url = $url;
        $connection->verify = $verify;
        $connection->loginValues['email'] = $email;
        $connection->loginValues['password'] = $password;
        $connection->loginValues['name'] = $name;

        return $connection;
    }

    public static function link(string $token): connexion
    {
        $connection = self::getInstance(HttpClient::create());
        $connection->login = $token;
        return $connection;
    }

}
