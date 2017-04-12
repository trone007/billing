<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class BillingBalanceStorageAdmin extends Admin {
	// Fields to be shown on create/edit forms
	protected $baseRouteName = 'BalanceStorage';
    protected $baseRoutePattern = 'BalanceStorage';


    public function createQuery($context = 'list')
    {
        $sql =<<<SQL
        SELECT c.inn client, (SUM(a.amount) - SUM(b.amount)) amount
        FROM client c
        INNER JOIN accrual a ON a.client_id = c.id
        INNER JOIN bill b ON b.client_id = c.id
        GROUP BY c.inn
SQL;

        $query = $this->getDoctrine()->getManager()->createQuery($sql);
        return $query;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->addIdentifier('id')
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('amount', 'number', ['label' => 'Сумма']);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('amount', null, ['label' => 'Сумма']);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('amount', null, ['label' => 'Сумма']);
    }

    public function getExportFields()
    {
        $fieldsArray = $this->getModelManager()->getExportFields($this->getClass());
        unset($fieldsArray[0]);
        unset($fieldsArray[1]);
        $fieldsArray['ИНН'] = 'client.inn';
        $fieldsArray['Наименование'] = 'client.name';
        $fieldsArray['Сумма']  = 'amount';

        return $fieldsArray;
    }
}