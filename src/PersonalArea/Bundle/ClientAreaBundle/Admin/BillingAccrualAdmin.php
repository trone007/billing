<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class BillingAccrualAdmin extends Admin {
	protected $baseRouteName = 'accrual';
    protected $baseRoutePattern = 'accrual';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('subscriber', null, ['label' => 'Поставщик'])
            ->add('dateTime', 'datetime', ['label' => 'Дата'])
            ->add('amount', 'number', ['label' => 'Сумма']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('subscriber', null, ['label' => 'Поставщик'])
            ->add('dateTime','doctrine_orm_date_range',[
                'label' => 'Дата'
                ])
            ->add('amount', 'doctrine_orm_number', ['label' => 'Сумма']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('subscriber', null, ['label' => 'Поставщик'])
            ->add('dateTime', null, ['label' => 'Дата'])
            ->add('amount', null, ['label' => 'Сумма']);
    }

    protected function getValue($value)
    {
        if (is_array($value) or $value instanceof \Traversable) {
            $result = [];
            foreach ($value as $item) {
               $result[] = $this->getValue($item);
            }
            $value = implode(',', $result);

        //formated datetime output
        } elseif ($value instanceof \DateTime) {
            $value = $this->dateFormater->format('d.m.Y h:i:s', $value);
        }elseif ($value instanceof \String) {
            $value = '\'' . $value;
        } elseif (is_object($value)) {
            $value = (string) $value;
        }

        return $value;
    }

    public function getExportFields()
    {
        $fieldsArray = $this->getModelManager()->getExportFields($this->getClass());

        unset($fieldsArray[0]);
        unset($fieldsArray[1]);
        unset($fieldsArray[2]);

        $fieldsArray['Идентификатор'] = 'id';
        $fieldsArray['ИНН'] = 'client.inn';
        $fieldsArray['Наименование'] = 'client.name';
        $fieldsArray['Поставщик'] = 'subscriber.name';
        $fieldsArray['Дата'] = 'date_time';
        $fieldsArray['Сумма']  = 'amount';

        return $fieldsArray;
    }
}