# typo3_login_service
Technical login service

Service is used to technical setup a FE user in an eID or login a user using the users uid or username.

Usage: 

## eID FE User init
When (re)starting a user session inside an API, add the following code to your API.
After the call your TSFE is filled as if the user was loading a FE page.

    /** @var \BPN\Typo3LoginService\Controller\Login\EidLoginController $loginController */
    $loginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\BPN\Typo3LoginService\Controller\Login\EidLoginController::class);
    $authenticaticated = $loginController->initFeUser();

Notice that this method does not login a person other than the person already logged in. 

## Logging in a user from code
When you need to login a user from code, and you know the users uid, call the following code:CodeLoginController

    /** @var \BPN\Typo3LoginService\Controller\Login\CodeLoginController $eidLoginController */
    $loginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\BPN\Typo3LoginService\Controller\Login\CodeLoginController::class);
    $authenticaticated = $loginController->loginUser($userid);

## Whats new in this version

2020-04-08
* This version is updated to work with TYPO3 v10.3.

2023-09-11
* This version is potentially unssafe in relation to other services. Use at own risk
