# phpnomad/symfony-translation-integration

PHPNomad translation strategy backed by Symfony's `TranslatorInterface`.

## Installation

```bash
composer require phpnomad/symfony-translation-integration
```

## Usage

Bind `PHPNomad\Symfony\Translation\Strategies\TranslationStrategy` as the concrete for
`PHPNomad\Translations\Interfaces\TranslationStrategy` in your DI container. The strategy
requires three constructor dependencies:

- `Symfony\Contracts\Translation\TranslatorInterface` -- your configured Symfony translator
- `PHPNomad\Translations\Interfaces\HasTextDomain` -- provides the translation domain
- `PHPNomad\Translations\Interfaces\HasLanguage` -- provides the target locale (or null for default)

Context is encoded using gettext's msgctxt convention (`\x04` separator) for compatibility with
gettext-based catalogue loaders.

Pluralization uses Symfony's `%count%` parameter convention.

## License

MIT
