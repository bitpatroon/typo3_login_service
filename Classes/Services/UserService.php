<?php

namespace BPN\Typo3LoginService\Services;

class UserService
{
    public function displayName(string $content = '', array $configuration = null)
    {
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = $GLOBALS['TSFE'];

        if ($this->isLoggedIn()) {
            $nameParts = $this->getNameParts($tsfe->fe_user->user);

            $name = $configuration['format'];
            $name = str_replace(
                [
                    '%initial_or_firstletter',
                    '%firstname',
                    '%middlename',
                    '%lastname',
                ],
                [
                    $nameParts['initials'] ?: $nameParts['first_name'],
                    $nameParts['first_name'] ?: '',
                    $nameParts['middle_name'] ?: '',
                    $nameParts['last_name'] ?: '',
                ],
                $name
            );

            $name = trim($name);

            if (!$name) {
                $name = $nameParts['username'];
            }

            return $name;
        }

        return '';
    }

    private function getNameParts(?array $user): array
    {
        return [
            'first_name'  => $user['first_name'] ?: '',
            'middle_name' => $user['middle_name'] ?: '',
            'last_name'   => $user['last_name'] ?: '',
            'initials'    => $user['initials'] ?: '',
            'email'       => $user['email'],
            'username'    => $user['username'],
        ];
    }

    public function isAuthenticated(string $content = '', array $configuration = null)
    {
        return $this->isLoggedIn() ? '1' : '';
    }

    public function isLoggedIn()
    {
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = $GLOBALS['TSFE'];

        return $tsfe && $tsfe->fe_user && $tsfe->fe_user->user && !(int) $tsfe->fe_user->user['ses_anonymous'];
    }

    public function getLoggedInUser(): array
    {
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = $GLOBALS['TSFE'];

        if ($this->isLoggedIn()) {
            return $tsfe->fe_user->user;
        }

        return [];
    }
}
