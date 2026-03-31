<?php

namespace PHPNomad\Symfony\Translation\Tests\Unit\Strategies;

use Mockery;
use PHPNomad\Symfony\Translation\Strategies\TranslationStrategy;
use PHPNomad\Symfony\Translation\Tests\TestCase;
use PHPNomad\Translations\Interfaces\HasLanguage;
use PHPNomad\Translations\Interfaces\HasTextDomain;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationStrategyTest extends TestCase
{
    protected TranslatorInterface|Mockery\MockInterface $translator;
    protected HasTextDomain|Mockery\MockInterface $textDomainProvider;
    protected HasLanguage|Mockery\MockInterface $languageProvider;
    protected TranslationStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = Mockery::mock(TranslatorInterface::class);
        $this->textDomainProvider = Mockery::mock(HasTextDomain::class);
        $this->languageProvider = Mockery::mock(HasLanguage::class);

        $this->textDomainProvider->shouldReceive('getTextDomain')->andReturn('test-domain')->byDefault();
        $this->languageProvider->shouldReceive('getLanguage')->andReturn('fr')->byDefault();

        $this->strategy = new TranslationStrategy(
            $this->translator,
            $this->textDomainProvider,
            $this->languageProvider
        );
    }

    /**
     * @test
     */
    public function testTranslateWithoutContext(): void
    {
        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with('Hello', [], 'test-domain', 'fr')
            ->andReturn('Bonjour');

        $result = $this->strategy->translate('Hello');

        $this->assertEquals('Bonjour', $result);
    }

    /**
     * @test
     */
    public function testTranslateWithContext(): void
    {
        $expectedId = "greeting\x04Hello";

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with($expectedId, [], 'test-domain', 'fr')
            ->andReturn('Bonjour');

        $result = $this->strategy->translate('Hello', 'greeting');

        $this->assertEquals('Bonjour', $result);
    }

    /**
     * @test
     */
    public function testTranslatePluralWithoutContext(): void
    {
        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with('1 item', ['%count%' => 5], 'test-domain', 'fr')
            ->andReturn('5 articles');

        $result = $this->strategy->translatePlural('1 item', '%count% items', 5);

        $this->assertEquals('5 articles', $result);
    }

    /**
     * @test
     */
    public function testTranslatePluralWithContext(): void
    {
        $expectedId = "cart\x041 item";

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with($expectedId, ['%count%' => 3], 'test-domain', 'fr')
            ->andReturn('3 articles');

        $result = $this->strategy->translatePlural('1 item', '%count% items', 3, 'cart');

        $this->assertEquals('3 articles', $result);
    }

    /**
     * @test
     */
    public function testUsesDomainFromTextDomainProvider(): void
    {
        $this->textDomainProvider = Mockery::mock(HasTextDomain::class);
        $this->textDomainProvider->shouldReceive('getTextDomain')->once()->andReturn('my-plugin');
        $this->languageProvider->shouldReceive('getLanguage')->andReturn('en');

        $strategy = new TranslationStrategy(
            $this->translator,
            $this->textDomainProvider,
            $this->languageProvider
        );

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with('Hello', [], 'my-plugin', 'en')
            ->andReturn('Hello');

        $strategy->translate('Hello');
    }

    /**
     * @test
     */
    public function testUsesLocaleFromLanguageProvider(): void
    {
        $this->languageProvider = Mockery::mock(HasLanguage::class);
        $this->languageProvider->shouldReceive('getLanguage')->once()->andReturn('de');
        $this->textDomainProvider->shouldReceive('getTextDomain')->andReturn('test-domain');

        $strategy = new TranslationStrategy(
            $this->translator,
            $this->textDomainProvider,
            $this->languageProvider
        );

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with('Hello', [], 'test-domain', 'de')
            ->andReturn('Hallo');

        $result = $strategy->translate('Hello');

        $this->assertEquals('Hallo', $result);
    }

    /**
     * @test
     */
    public function testPassesNullLocaleWhenProviderReturnsNull(): void
    {
        $this->languageProvider = Mockery::mock(HasLanguage::class);
        $this->languageProvider->shouldReceive('getLanguage')->once()->andReturn(null);
        $this->textDomainProvider->shouldReceive('getTextDomain')->andReturn('test-domain');

        $strategy = new TranslationStrategy(
            $this->translator,
            $this->textDomainProvider,
            $this->languageProvider
        );

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with('Hello', [], 'test-domain', null)
            ->andReturn('Hello');

        $result = $strategy->translate('Hello');

        $this->assertEquals('Hello', $result);
    }
}
