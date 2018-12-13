<?php

declare(strict_types=1);

use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext as BehatchRestContext;

class RestContext extends BehatchRestContext
{
    use SharedContextTrait;

    /**
     * @override Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody($method, $url, PyStringNode $body)
    {
        $rawBody = $body->getRaw();
        $this->sharingContext->renderTwigTemplate($rawBody);
        $newBody = new PyStringNode(explode("\n", $rawBody), $body->getLine());

        return parent::iSendARequestToWithBody($method, $url, $newBody);
    }
}
