<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure\Scenario;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use Symfony\Component\HttpClient\Exception\RedirectionException;

final readonly class LogInPlayer
{
    public static function run(Player $player): string
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        try {
            $response = $httpClient->request('POST', '/redirect.php', [
                'body' => [
                    'pseudo' => $player->username,
                    'mdp' => $player->password,
                    'connexion' => 'Se connecter',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'max_redirects' => 0,
            ]);
        } catch (RedirectionException $e) { // @phpstan-ignore catch.neverThrown
            // With max_redirects=0, HttpClient throws an exception when we get a 302
            // This is expected on successful login
            $response = $e->getResponse();
        }

        $headers = $response->getHeaders(false);
        $cookies = $headers['set-cookie'] ?? $headers['Set-Cookie'] ?? [];
        foreach ($cookies as $cookie) {
            if (str_starts_with($cookie, 'PHPSESSID=')) {
                return $cookie;
            }
        }

        $content = $response->getContent(false);
        $allCookies = implode(', ', $cookies);

        throw new \RuntimeException("Login failed: PHPSESSID cookie not found. Cookies: [{$allCookies}], Content: {$content}");
    }
}
