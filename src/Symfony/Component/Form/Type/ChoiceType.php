<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\EventListener\FixRadioInputListener;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\DataTransformer\ScalarToChoiceTransformer;
use Symfony\Component\Form\DataTransformer\ScalarToBooleanChoicesTransformer;
use Symfony\Component\Form\DataTransformer\ArrayToChoicesTransformer;
use Symfony\Component\Form\DataTransformer\ArrayToBooleanChoicesTransformer;

class ChoiceType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if (!$options['choices'] && !$options['choice_list']) {
            throw new FormException('Either the option "choices" or "choice_list" is required');
        }

        if (!$options['choice_list']) {
            $options['choice_list'] = new ArrayChoiceList($options['choices']);
        }

        if ($options['expanded']) {
            // Load choices already if expanded
            $options['choices'] = $options['choice_list']->getChoices();

            foreach ($options['choices'] as $choice => $value) {
                if ($options['multiple']) {
                    $builder->add((string)$choice, 'checkbox', array('value' => $choice));
                } else {
                    $builder->add((string)$choice, 'radio', array('value' => $choice));
                }
            }
        }

        $builder->setAttribute('choice_list', $options['choice_list'])
            ->setAttribute('preferred_choices', $options['preferred_choices'])
            ->setAttribute('multiple', $options['multiple'])
            ->setAttribute('expanded', $options['expanded']);

        if ($options['expanded']) {
            if ($options['multiple']) {
                $builder->appendClientTransformer(new ArrayToBooleanChoicesTransformer($options['choice_list']));
            } else {
                $builder->appendClientTransformer(new ScalarToBooleanChoicesTransformer($options['choice_list']));
                $builder->addEventSubscriber(new FixRadioInputListener(), 10);
            }
        } else {
            if ($options['multiple']) {
                $builder->appendClientTransformer(new ArrayToChoicesTransformer());
            } else {
                $builder->appendClientTransformer(new ScalarToChoiceTransformer());
            }
        }

    }

    public function buildView(FormView $view, FormInterface $form)
    {
        $choices = $form->getAttribute('choice_list')->getChoices();
        $preferred = array_flip($form->getAttribute('preferred_choices'));

        $view->setVar('multiple', $form->getAttribute('multiple'));
        $view->setVar('expanded', $form->getAttribute('expanded'));
        $view->setVar('preferred_choices', array_intersect_key($choices, $preferred));
        $view->setVar('choices', array_diff_key($choices, $preferred));
        $view->setVar('separator', '-------------------');
        $view->setVar('empty_value', '');

        if ($view->getVar('multiple') && !$view->getVar('expanded')) {
            // Add "[]" to the name in case a select tag with multiple options is
            // displayed. Otherwise only one of the selected options is sent in the
            // POST request.
            $view->setVar('name', $view->getVar('name').'[]');
        }
    }

    public function getDefaultOptions(array $options)
    {
        $multiple = isset($options['multiple']) && $options['multiple'];
        $expanded = isset($options['expanded']) && $options['expanded'];

        return array(
            'multiple' => false,
            'expanded' => false,
            'choice_list' => null,
            'choices' => array(),
            'preferred_choices' => array(),
            'csrf_protection' => false,
            'empty_data' => $multiple || $expanded ? array() : '',
        );
    }

    public function getParent(array $options)
    {
        return $options['expanded'] ? 'form' : 'field';
    }

    public function getName()
    {
        return 'choice';
    }
}