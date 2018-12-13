<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class SharingContext implements Context, \ArrayAccess, KernelAwareContext
{
    /** @var array */
    private $values = [];

    /** @var Environment */
    private $twig;

    public function setKernel(KernelInterface $kernel): void
    {
        /** @var ContainerInterface $container */
        $container = $kernel->getContainer();

        if (!$container->has('twig')) {
            throw new \LogicException(
                "Could not find 'twig' service. Try running 'composer req --dev twig/twig symfony/twig-bundle'."
            );
        }

        /** @var Environment $twig */
        $twig = $container->get('twig');
        $this->twig = $twig;
    }

    public function renderTwigTemplate(string &$string): void
    {
        $template = $this->twig->createTemplate($string);

        $string = $this->twig->render($template, $this->values);
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->values);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->values[$offset]);
    }

    public function merge(array $array): void
    {
        $this->values = array_merge($this->values, $array);
    }
}
