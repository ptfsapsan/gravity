<?php

namespace app\services;

use Lcobucci\JWT\Token;
use Yii;

class Jwt
{
    private $jwt;

    public function __construct()
    {
        $this->jwt = Yii::$app->jwt;

    }

    public function getToken(string $userName)
    {
        $jwtParams = Yii::$app->params;
        $time = time();
        $signer = $this->jwt->getSigner($jwtParams['jwtMethod']);
        $key = $this->jwt->getKey();

        $token = $this->jwt->getBuilder()
            ->identifiedBy($jwtParams['jwtSecretKey'], true)
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['jwtTtl'])
            ->withClaim('un', $userName)
            ->getToken($signer, $key);

        return $token;
    }

    public function verifyToken(string $token): ?string
    {
        /** @var Token $token */
        $token = $this->jwt->getParser()->parse((string) $token);
        $data = $this->jwt->getValidationData();
        if ($token->validate($data)) {
            return $token->getClaim('un');
        }

        return null;
    }

}