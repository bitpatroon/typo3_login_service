<?php


namespace BPN\Typo3LoginService\Controller;


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
}