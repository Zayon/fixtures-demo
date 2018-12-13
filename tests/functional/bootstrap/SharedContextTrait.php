<?php

declare(strict_types=1);

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

trait SharedContextTrait
{
    /** @var SharingContext */
    private $sharingContext;

    /** @var bool */
    private $isShared = false;

    /** @BeforeScenario */
    public function gatherSharingContext(BeforeScenarioScope $scope): void
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        if ($environment->hasContextClass(SharingContext::class)) {
            $this->isShared = true;
            $this->sharingContext = $environment->getContext(SharingContext::class);
        }
    }
}
