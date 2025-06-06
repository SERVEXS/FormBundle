<?php

namespace Gregwar\FormBundle\Type;

use Doctrine\Persistence\ManagerRegistry;
use Gregwar\FormBundle\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityIdType extends AbstractType
{
    public function __construct(protected ManagerRegistry $registry)
    {
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

        $resolver->setDefaults(['em' => null, 'property' => null, 'query_builder' => null, 'hidden' => true, 'multiple' => false]);
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
