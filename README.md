# phpnomad/symfony-translation-integration

[![Latest Version](https://img.shields.io/packagist/v/phpnomad/symfony-translation-integration.svg)](https://packagist.org/packages/phpnomad/symfony-translation-integration)
[![Total Downloads](https://img.shields.io/packagist/dt/phpnomad/symfony-translation-integration.svg)](https://packagist.org/packages/phpnomad/symfony-translation-integration)
[![PHP Version](https://img.shields.io/packagist/php-v/phpnomad/symfony-translation-integration.svg)](https://packagist.org/packages/phpnomad/symfony-translation-integration)
[![License](https://img.shields.io/packagist/l/phpnomad/symfony-translation-integration.svg)](https://packagist.org/packages/phpnomad/symfony-translation-integration)

Integrates the Symfony Translation component with PHPNomad's `phpnomad/translate` abstraction. It supplies a single `TranslationStrategy` implementation backed by Symfony's `TranslatorInterface`, so applications that call the PHPNomad translation API can resolve strings through a configured Symfony translator without changing any call sites.

## Installation

```bash
composer require phpnomad/symfony-translation-integration
```

## What This Provides

- `PHPNomad\Symfony\Translation\Strategies\TranslationStrategy`, a concrete implementation of `PHPNomad\Translations\Interfaces\TranslationStrategy` that delegates `translate()` and `translatePlural()` calls to Symfony's `TranslatorInterface::trans()`.
- Disambiguation context is encoded using gettext's `msgctxt` convention (the `\x04` EOT separator), so catalogues loaded from gettext `.po`/`.mo` files resolve contextual strings correctly.
- Pluralization uses Symfony's `%count%` parameter convention, which lines up with Symfony's ICU and legacy plural format loaders.

## Requirements

- `phpnomad/translate` ^2.0, which defines the `TranslationStrategy` interface along with the `HasTextDomain` and `HasLanguage` providers
- `symfony/translation-contracts` ^2.5 or ^3.0, which defines `TranslatorInterface`
- A configured Symfony translator with your catalogues loaded (the full `symfony/translation` package or any implementation of `TranslatorInterface`)

## Usage

Bind the concrete strategy to the interface inside your PHPNomad bootstrapper. The strategy takes three constructor dependencies: the Symfony translator, a `HasTextDomain` provider that returns the active text domain, and a `HasLanguage` provider that returns the target locale (or `null` to fall back to the translator's default).

```php
<?php

use PHPNomad\Symfony\Translation\Strategies\TranslationStrategy;
use PHPNomad\Translations\Interfaces\HasLanguage;
use PHPNomad\Translations\Interfaces\HasTextDomain;
use PHPNomad\Translations\Interfaces\TranslationStrategy as TranslationStrategyInterface;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

$container->bind(TranslatorInterface::class, function () {
    $translator = new Translator('en_US');
    $translator->addLoader('array', new ArrayLoader());
    $translator->addResource('array', ['hello' => 'Hello'], 'en_US', 'messages');

    return $translator;
});

$container->bind(HasTextDomain::class, MyTextDomainProvider::class);
$container->bind(HasLanguage::class, MyLanguageProvider::class);
$container->bind(TranslationStrategyInterface::class, TranslationStrategy::class);
```

Once bound, any code that resolves `TranslationStrategy` from the container will route through Symfony.

## Documentation

- PHPNomad docs: [phpnomad.com](https://phpnomad.com)
- Symfony Translation component: [symfony.com/doc/current/translation.html](https://symfony.com/doc/current/translation.html)

## License

MIT. See [LICENSE](LICENSE).
