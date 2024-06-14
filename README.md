# typo3_login_service
Technical login service

Service is used to technical setup a FE user in an eID or login a user using the users uid or username.

Usage: 


## eID FE User init
When (re)starting a user session inside an API, add the following code to your API.
After the call your TSFE is filled as if the user was loading a FE page.

 /** @var \BPN\Typo3LoginService\Controller\EidLoginController $loginController */
$loginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\BPN\Typo3LoginService\Controller\EidLoginController::class);
$authenticaticated = $loginController->initFeUser();


## Logging in a user from code

When you need to login a user from code, and you know the users uid, call the following code:

/** @var \BPN\Typo3LoginService\Controller\CodeLoginController $eidLoginController */
$loginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\BPN\Typo3LoginService\Controller\CodeLoginController::class);
$authenticaticated = $loginController->loginUser($userid);
