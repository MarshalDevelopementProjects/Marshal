<?php

namespace Tests\Core;

use Core\Token;
use Tests\Tester;

require __DIR__ . '/../../vendor/autoload.php';

class TestToken extends Tester
{

    private Token $token;

    public function __construct()
    {
        parent::__construct(Token::class);
    }

    public function setup(): void
    {
        $this->token = new Token();
    }

     function testGenerateTokenWithEmptyArgs(): void
    {
        // with all three arguments empty
        $this->assertNull($this->token->generateToken(headers: [], payload: [], ttl: ""));

        // with empty header
        $this->assertNull($this->token->generateToken(
            headers: [],
            payload: ["id" => 12, "name" => "kylo_ren", "primary_role" => "project_member"],
            ttl: "access_ttl")
        );

        // with empty payload
        $this->assertNull($this->token->generateToken(
            headers: ["alg" => "HS256", "typ" => "JWT"],
            payload: [],
            ttl: "refresh_ttl")
        );
    }

    function testGenerateTokenWithInvalidArgs(): void
    {
        // only valid values are "refresh_ttl" and "access_ttl"

        // with non-existing ttl
        $this->assertNull($this->token->generateToken(
            headers: ["alg" => "HS256", "typ" => "JWT"],
            payload: ["id" => 12, "name" => "kylo_ren", "primary_role" => "project_member"],
            ttl: "toke_ttl")
        );
    }

    function testGenerateTokenWithValidArgs(): void
    {
        // using the refresh token ttl
        $this->assertNull($this->token->generateToken(
            headers: ["alg" => "HS256", "typ" => "JWT"],
            payload: ["id" => 12, "name" => "kylo_ren", "primary_role" => "project_member"],
            ttl: "refresh_ttl")
        );

        // using the access token ttl
        $this->assertNull($this->token->generateToken(
            headers: ["alg" => "HS256", "typ" => "JWT"],
            payload: ["id" => 12, "name" => "kylo_ren", "primary_role" => "project_member"],
            ttl: "access_ttl")
        );
    }

    function testGetBearerTokenWithEmptyArgs(): void
    {
        $this->assertNull($this->token->getBearerToken(token_type: ""));
    }

    function testGetBearerTokenWithInvalidArgs(): void
    {
        // using a non-existing token type
        $this->assertNull($this->token->getBearerToken(token_type: "invalid"));
    }

    function testGetBearerTokenWithValidArgs(): void
    {
        // before running this test make sure you have valid tokens in the browser
        // because a mock class is not created

        // with access token type
        $this->assertTrue(!empty($this->token->getBearerToken(token_type: "access")));

        // with refresh token type
        $this->assertTrue(!empty($this->token->getBearerToken(token_type: "refresh")));
    }


    function testValidateTokenWithEmptyArgs(): void
    {
        // with an empty string
        $this->assertFalse($this->token->validateToken(json_web_token: ""));
    }

    function testValidateTokenWithInvalidArgs(): void
    {

        // MAKE SURE YOU HAVE TWO INVALID TOKENS BEFORE CALLING THIS TEST IN THE BROWSER
        // ACCESS => expire in 5 minutes
        // REFRESH => expires in 24 hours

        // with invalid input format
        $this->assertFalse($this->token->validateToken(json_web_token: "invalid_input_token"));

        // wrong format
        $this->assertFalse($this->token->validateToken(json_web_token: "headers.payload"));

        // invalid token strings => expired refresh token
        $this->assertFalse(
            $this->token->validateToken(
            json_web_token: $this->token->getBearerToken("refresh")
            )
        );

        // invalid token strings => expired refresh token
        $this->assertFalse(
            $this->token->validateToken(
                json_web_token: $this->token->getBearerToken("refresh")
            )
        );
    }

    function testValidateTokenWithValidArgs(): void
    {
        // MAKE SURE YOU HAVE TWO INVALID TOKENS BEFORE CALLING THIS TEST IN THE BROWSER
        // ACCESS => expire in 5 minutes
        // REFRESH => expires in 24 hours

        // obtain two valid tokens => refresh and access tokens before running this test
        $this->assertFalse(
            $this->token->validateToken(
                json_web_token: $this->token->getBearerToken("refresh")
            )
        );

        // invalid token strings => expired refresh token
        $this->assertFalse(
            $this->token->validateToken(
                json_web_token: $this->token->getBearerToken("access")
            )
        );
    }

    public function run(): void
    {
        $this->setup();

        $this->testGenerateTokenWithEmptyArgs();
        $this->testGenerateTokenWithInvalidArgs();
        $this->testGenerateTokenWithValidArgs();

        $this->testGetBearerTokenWithEmptyArgs();
        $this->testGetBearerTokenWithInvalidArgs();
        $this->testGetBearerTokenWithValidArgs();

        $this->testValidateTokenWithEmptyArgs();
        $this->testValidateTokenWithInvalidArgs();
        $this->testValidateTokenWithValidArgs();

        $this->summary();
    }
}