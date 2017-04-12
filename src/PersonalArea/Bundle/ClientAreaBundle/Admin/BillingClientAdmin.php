<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class BillingClientAdmin extends Admin {
	protected $baseRouteName = 'client';
    protected $baseRoutePattern = 'client';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Наименование'])
            ->add('inn', null, ['label' => 'Идентификационный номер'])
            ->add('balance', null, ['label' => 'Баланс']);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Наименование'])
            ->add('inn', null, ['label' => 'Идентификационный номер']);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->addIdentifier('inn')
            ->add('balance', null, ['label' => 'Баланс'])
            ->add('_action', 'actions', array(
                'label' => 'Действие',
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }
}
