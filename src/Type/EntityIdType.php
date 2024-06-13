<?php

namespace Gregwar\FormBundle\Type;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Gregwar\FormBundle\DataTransformer\EntityToIdTransformer;

/**
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityIdType extends AbstractType
{
    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new EntityToIdTransformer(
            $this->registry->getManager($options['em']),
            $options['class'],
            $options['property'],
            $options['query_builder'],
            $options['multiple']
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['class']);

        $resolver->setDefaults(['em'            => null, 'property'      => null, 'query_builder' => null, 'hidden'        => true, 'multiple'      => false]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (true === $options['hidden']) {
            $view->vars['type'] = 'hidden';
        }
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
