<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroEncodedPlaceholderPasswordType;
use Oro\Bundle\InfinitePayBundle\Entity\InfinitePaySettings;
use Oro\Bundle\InfinitePayBundle\Form\Type\InfinitePaySettingsType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Oro\Bundle\LocaleBundle\Tests\Unit\Form\Type\Stub\LocalizedFallbackValueCollectionTypeStub;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;
use Oro\Component\Testing\Unit\FormIntegrationTestCase;
use Oro\Component\Testing\Unit\PreloadedExtension;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfinitePaySettingsTypeTest extends FormIntegrationTestCase
{
    private SymmetricCrypterInterface&MockObject $crypter;

    #[\Override]
    protected function setUp(): void
    {
        $this->crypter = $this->createMock(SymmetricCrypterInterface::class);
        parent::setUp();
    }

    /**
     * The parent implementation resolves the validation.yml path by searching for "Bundle" in the entity file path.
     * Since InfinitePayBundle classes reside in "package/infinitepay/" (without "Bundle" in the directory structure),
     * the automatic resolution fails. This override provides the correct path explicitly.
     */
    #[\Override]
    protected function getConfigFile(string $class): ?string
    {
        if ($class === InfinitePaySettings::class) {
            return dirname(__DIR__, 4) . '/Resources/config/validation.yml';
        }

        return parent::getConfigFile($class);
    }

    #[\Override]
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension(
                [
                    LocalizedFallbackValueCollectionType::class => new LocalizedFallbackValueCollectionTypeStub(),
                    new OroEncodedPlaceholderPasswordType($this->crypter),
                ],
                []
            ),
            $this->getValidatorExtension(true),
        ];
    }

    public function testSubmitValid(): void
    {
        $this->crypter->expects(self::any())
            ->method('encryptData')
            ->willReturnArgument(0);

        $submitData = [
            'infinitePayLabels' => [['string' => 'label']],
            'infinitePayShortLabels' => [['string' => 'short']],
            'infinitePayClientRef' => 'clientRef',
            'infinitePayUsername' => 'username',
            'infinitePayPassword' => 'password',
            'infinitePaySecret' => 'secret',
            'infinitePayTestMode' => true,
            'infinitePayInvoiceDuePeriod' => 30,
            'infinitePayInvoiceShippingDuration' => 21,
        ];

        $form = $this->factory->create(InfinitePaySettingsType::class, new InfinitePaySettings());
        $form->submit($submitData);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid());
    }

    /**
     * @dataProvider submitWithLongValuesProvider
     */
    public function testSubmitWithTooLongValues(array $override): void
    {
        $this->crypter->expects(self::any())
            ->method('encryptData')
            ->willReturnArgument(0);

        $submitData = array_replace_recursive([
            'infinitePayLabels' => [['string' => 'label']],
            'infinitePayShortLabels' => [['string' => 'short']],
            'infinitePayClientRef' => 'clientRef',
            'infinitePayUsername' => 'username',
            'infinitePayPassword' => 'password',
            'infinitePaySecret' => 'secret',
            'infinitePayTestMode' => true,
            'infinitePayInvoiceDuePeriod' => 30,
            'infinitePayInvoiceShippingDuration' => 21,
        ], $override);

        $form = $this->factory->create(InfinitePaySettingsType::class, new InfinitePaySettings());
        $form->submit($submitData);

        self::assertTrue($form->isSynchronized());
        self::assertFalse($form->isValid());
    }

    public function submitWithLongValuesProvider(): array
    {
        return [
            'infinitePayClientRef too long' => [['infinitePayClientRef' => str_repeat('a', 256)]],
            'infinitePayUsername too long' => [['infinitePayUsername' => str_repeat('a', 256)]],
            'infinitePayPassword too long' => [['infinitePayPassword' => str_repeat('a', 256)]],
            'infinitePaySecret too long' => [['infinitePaySecret' => str_repeat('a', 256)]],
        ];
    }

    public function testGetBlockPrefix(): void
    {
        $formType = new InfinitePaySettingsType();
        self::assertSame(InfinitePaySettingsType::BLOCK_PREFIX, $formType->getBlockPrefix());
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => InfinitePaySettings::class]);

        $formType = new InfinitePaySettingsType();
        $formType->configureOptions($resolver);
    }
}
