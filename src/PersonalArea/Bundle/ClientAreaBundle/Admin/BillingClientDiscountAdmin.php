<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class BillingClientDiscountAdmin extends Admin {
	protected $baseRouteName = 'client-disc';
    protected $baseRoutePattern = 'client-disc';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('discount', null, ['label' => 'Скидочный план'])
            ->add('beginDate', 'datetime', ['label' => 'Дата начала действия'])
            ->add('endDate', 'datetime', [
                'label' => 'Дата окончания действия',
                'required' => false
                ]);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('discount', null, ['label' => 'Скидочный план'])
            ->add('beginDate', null, ['label' => 'Дата начала действия'])
            ->add('endDate', null, ['label' => 'Дата окончания действия']);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('discount', null, ['label' => 'Скидочный план'])
            ->add('beginDate', null, ['label' => 'Дата начала действия'])
            ->add('endDate', null, ['label' => 'Дата окончания действия']);
    }
}