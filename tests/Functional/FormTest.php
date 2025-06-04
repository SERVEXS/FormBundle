<?php

namespace Gregwar\FormBundle\Tests\Functional;

use Gregwar\FormBundle\Type\EntityIdType;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpKernel\KernelInterface;

class FormTest extends WebTestCase
{
    protected static function createKernel(array $options = []): KernelInterface
    {
        return self::$kernel = new AppKernel('test', true);
    }

    #[DataProvider('getTestFormData')]
    public function testForm($hidden, $type): void
    {
        $kernel = self::createKernel();
        $kernel->boot();

        $formBuilder = $kernel->getContainer()->get('form.factory')->createBuilder(FormType::class);
        $formBuilder->add(
            'user',
            EntityIdType::class,
            ['class' => 'Gregwar\FormBundle\Tests\Functional\User', 'hidden' => $hidden]
        );
        $form = $formBuilder->getForm();

        $html = $kernel->getContainer()->get('twig')->render('::view.html.twig', ['form' => $form->createView()]);

        $this->assertEquals(
            '<input type="' . $type . '" id="form_user" name="form[user]" required="required" />',
            trim((string) $html)
        );
    }

    public function testFormWithNoClass(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "class" is missing.');
        $kernel = self::createKernel();
        $kernel->boot();

        $formBuilder = $kernel->getContainer()->get('form.factory')->createBuilder(FormType::class);
        $formBuilder->add('user', EntityIdType::class);
        $form = $formBuilder->getForm();
    }

    public static function getTestFormData(): array
    {
        return [[true, 'hidden'], [false, 'text']];
    }
}
