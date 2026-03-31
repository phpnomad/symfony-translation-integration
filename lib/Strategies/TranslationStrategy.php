<?php

namespace PHPNomad\Symfony\Translation\Strategies;

use PHPNomad\Translations\Interfaces\HasLanguage;
use PHPNomad\Translations\Interfaces\HasTextDomain;
use PHPNomad\Translations\Interfaces\TranslationStrategy as TranslationStrategyInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Symfony Translator implementation of the translation strategy.
 *
 * Delegates to Symfony's TranslatorInterface. Context is encoded using
 * gettext's msgctxt convention (EOT separator \x04) for compatibility
 * with gettext-based catalogue loaders.
 *
 * Pluralization uses Symfony's %count% parameter convention.
 */
class TranslationStrategy implements TranslationStrategyInterface
{
    protected TranslatorInterface $translator;
    protected HasTextDomain $textDomainProvider;
    protected HasLanguage $languageProvider;

    public function __construct(
        TranslatorInterface $translator,
        HasTextDomain $textDomainProvider,
        HasLanguage $languageProvider
    ) {
        $this->translator = $translator;
        $this->textDomainProvider = $textDomainProvider;
        $this->languageProvider = $languageProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function translate(string $text, ?string $context = null): string
    {
        $id = $context !== null ? "{$context}\x04{$text}" : $text;

        return $this->translator->trans(
            $id,
            [],
            $this->textDomainProvider->getTextDomain(),
            $this->languageProvider->getLanguage()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function translatePlural(string $singular, string $plural, int $count, ?string $context = null): string
    {
        $id = $context !== null ? "{$context}\x04{$singular}" : $singular;

        return $this->translator->trans(
            $id,
            ['%count%' => $count],
            $this->textDomainProvider->getTextDomain(),
            $this->languageProvider->getLanguage()
        );
    }
}
