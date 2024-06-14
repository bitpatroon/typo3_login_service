<?php

namespace BPN\Typo3LoginService\Traits;

use TYPO3\CMS\Core\Utility\GeneralUtility;

trait RequiresRequestHandler
{
    use HasVersion;

    protected function getCodeRequestHandler()
    {
        $handlerName = $this->loadMajorDependentClasses($this->getCodeRequestHandlerBaseName());

        return GeneralUtility::makeInstance($handlerName);
    }

    protected function getEidLoginRequestHandler()
    {
        $handlerName = $this->loadMajorDependentClasses($this->getEidLoginRequestHandlerBaseName());

        return GeneralUtility::makeInstance($handlerName);
    }

    protected function loadMajorDependentClasses(string $baseClassName = null)
    {
        $file = $this->extensionClassesPath(
            '/RequestHandler/v' . $this->getMajor() . '/' . $baseClassName . '.php'
        );
        if (file_exists($file)) {
            require_once $file;
        }

        return $this->getRequestHandlerFQN($baseClassName);
    }

    protected function extensionClassesPath(string $append)
    {
        return dirname(__DIR__, 1) . $append;
    }

    protected function getRequestHandlerFQN(string $classBaseName = null)
    {
        return 'BPN\Typo3LoginService\RequestHandler\v' . $this->getMajor() .
            '\\' . $classBaseName;
    }


    protected function getCodeRequestHandlerName()
    {
        return 'BPN\Typo3LoginService\RequestHandler\v' . $this->getMajor() .
            '\\' . $this->getCodeRequestHandlerBaseName();
    }

    protected function getCodeRequestHandlerBaseName()
    {
        return 'CodeLoginRequestHandler';
    }

    protected function getEidLoginRequestHandlerBaseName()
    {
        return 'EidLoginRequestHandler';
    }

    protected function getEidLoginRequestHandlerName(bool $checkClassExists = true)
    {
        return 'BPN\Typo3LoginService\RequestHandler\v' . $this->getMajor() .
            '\\' . $this->getEidLoginRequestHandlerBaseName();
    }

}