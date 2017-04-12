<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class BillingServicePriceAdmin extends Admin {
	protected $baseRouteName = 'service-price';
    protected $baseRoutePattern = 'service-price';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('service', null, ['label' => 'Услуга'])
            ->add('price', null, ['label' => 'Ценовой план'])
            ->add('count', null, ['label' => 'Количество'])
            ->add('amount', null, ['label' => 'Цена']);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('service', null, ['label' => 'Услуга'])
            ->add('price', null, ['label' => 'Ценовой план'])
            ->add('count', null, ['label' => 'Количество'])
            ->add('amount', null, ['label' => 'Цена']);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('service', null, ['label' => 'Услуга'])
            ->add('price', null, ['label' => 'Ценовой план'])
            ->add('count', null, ['label' => 'Количество'])
            ->add('amount', null, ['label' => 'Цена']);
    }
}