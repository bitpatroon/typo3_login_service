<?php


namespace BPN\Typo3LoginService\Controller\Login;


class AbstractLoginController
{
    /**
     * Gets the authenticated userid
     * @return bool|int
     */
    public function getAuthenticatedUserId(){
        if (empty($GLOBALS['TSFE'] || empty($GLOBALS['TSFE']->fe_user) || empty($GLOBALS['TSFE']->fe_user->user))){
            return false;
        }

        return (int)$GLOBALS['TSFE']->fe_user->user['uid'];
    }

    /**
     * Tests if the current user is logged in.
     * @param bool $checkStrict
     * @return bool
     */
    public function isCurrentFrontendUserLoggedIn($checkStrict = false)
    {
        if (empty($GLOBALS['TSFE']) || empty($GLOBALS['TSFE']->fe_user) || empty($GLOBALS['TSFE']->fe_user->user)) {
            return false;
        }

        if (!isset($GLOBALS['TSFE']->fe_user->user['uid']) || empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
            return false;
        }

        if (!isset($GLOBALS['TSFE']->fe_user->user['username']) || empty($GLOBALS['TSFE']->fe_user->user['username'])) {
            return false;
        }

        if ($checkStrict) {
            return !empty($_COOKIE['fe_typo_user']);
        }

        return true;
    }

}