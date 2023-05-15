<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

use Core\Config;

class Token
{
    // current secret
    protected $secret = 'secret';

    public function __construct(protected $algorithm = 'SHA256', protected $type = "JWT")
    {
    }

    public function generateToken($headers = array(), $payload = array(), $ttl = "access_ttl")
    {
        if (!empty($headers) && !empty($payload)) {

            $payload = array_merge($payload, ["exp" => time() + Config::getApiGlobal('remember')[$ttl]]);

            $encoded_headers = $this->_base64url_encode(json_encode($headers));

            $encoded_payload = $this->_base64url_encode(json_encode($payload));

            $signature = hash_hmac($this->algorithm, "$encoded_headers.$encoded_payload", $this->secret, true);
            $encoded_signature = $this->_base64url_encode($signature);

            $json_web_token = "$encoded_headers.$encoded_payload.$encoded_signature";

            return $json_web_token;
        } else {
            return null;
        }
    }

    protected function validateToken($json_web_token)
    {
        // break the token into parts
        if ($json_web_token) {
            $parts_of_token = explode('.', $json_web_token);
            if (count($parts_of_token) >= 3) {
                $headers = base64_decode($parts_of_token[0]);
                $payload = base64_decode($parts_of_token[1]);
                $signature_sent = base64_decode($parts_of_token[2]);

                // THE TOKENS GENERATED MUST HAVE A EXPIRATION TIME
                // OTHERWISE THIS WILL FAIL
                $is_expired = $this->isTokenExpired($payload);

                $base64url_encoded_headers = $this->_base64url_encode($headers);
                $base64url_encoded_payload = $this->_base64url_encode($payload);
                $signature = hash_hmac(
                    $this->algorithm,
                    $base64url_encoded_headers . "." . $base64url_encoded_payload,
                    $this->secret,
                    true
                );

                $valid_signature = ($signature === $signature_sent);

                // if ttl is not met and the signature is not changed
                // then the token is valid
                if ($is_expired || !$valid_signature) {
                    return true;
                }
                return false;
            }
        }
        return false;
    }

    protected function _base64url_encode($string)
    {
        return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
    }

    private function isTokenExpired($payload)
    {
        $expiration = json_decode($payload)->exp;
        return ($expiration - time()) < 0;
    }

    public function getBearerToken($token_type = "access")
    {
        return (Cookie::cookieExists(Config::getApiGlobal('remember')[$token_type]))
            ? json_decode(Cookie::getCookieData(Config::getApiGlobal('remember')[$token_type]), true)['Token']
            : null;
    }

    protected function setBearerTokenInCookie($headers = array(), $payload = array(), $token_type = "access", $ttl = "access_ttl")
    {
        return Cookie::setCookie(
            name: Config::getApiGlobal('remember')[$token_type],
            value: json_encode(array("Token" => $this->generateToken($headers, $payload, $ttl))),
            expiry: Config::getApiGlobal('remember')[$ttl]
        );
    }

    protected function unsetBearerTokenCookie()
    {
        if (Cookie::cookieExists(Config::getApiGlobal('remember')['access'])) {
            Cookie::deleteCookie(
                Cookie::cookieExists(Config::getApiGlobal('remember')['access'])
            );
        }
        if (Cookie::cookieExists(Config::getApiGlobal('remember')['refresh'])) {
            Cookie::deleteCookie(
                Cookie::cookieExists(Config::getApiGlobal('remember')['refresh'])
            );
        }
        return true;
    }

    public function getTokenPayload($json_web_token)
    {
        // break the token into parts
        $parts_of_token = explode('.', $json_web_token);
        if (count($parts_of_token) == 3) {
            // $_headers = base64_decode($parts_of_token[0]);
            $payload = base64_decode($parts_of_token[1]);
            // $_signature_sent = base64_decode($parts_of_token[2]);
            return json_decode($payload);
        }
        return null;
    }
}