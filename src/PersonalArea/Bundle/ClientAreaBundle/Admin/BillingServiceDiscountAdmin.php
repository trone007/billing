<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class BillingServiceDiscountAdmin extends Admin {
	// Fields to be shown on create/edit forms
	protected $baseRouteName = 'service-disc';
    protected $baseRoutePattern = 'service-disc';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('discount', null, ['label' => 'Скидочный план'])
            ->add('service', null, ['label' => 'Услуга'])
            ->add('amount', null, ['label' => 'Скидка %']);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('discount', null, ['label' => 'Скидочный план'])
            ->add('service', null, ['label' => 'Услуга'])
            ->add('amount', null, ['label' => 'Скидка %']);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('discount', null, ['label' => 'Скидочный план'])
            ->add('service', null, ['label' => 'Услуга'])
            ->add('amount', null, ['label' => 'Скидка %']);
    }
}