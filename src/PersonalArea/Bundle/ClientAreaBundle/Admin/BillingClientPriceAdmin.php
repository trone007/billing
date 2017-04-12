<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class BillingClientPriceAdmin extends Admin {
	// Fields to be shown on create/edit forms
    protected $baseRoutePattern = 'client-price';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('price', null, ['label' => 'Ценовой план'])
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
            ->add('price', null, ['label' => 'Ценовой план'])
            ->add('beginDate', null, ['label' => 'Дата начала действия'])
            ->add('endDate', null, ['label' => 'Дата окончания действия'])
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('client', null, ['label' => 'Плательщик'])
            ->add('price', null, ['label' => 'Ценовой план'])
            ->add('beginDate', null, ['label' => 'Дата начала действия'])
            ->add('endDate', null, ['label' => 'Дата окончания действия'])
        ;
    }
    protected function getValue($value)
        {
            //if value is array or collection, creates string
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
        public function getExportFields() {
            $fieldsArray = $this->getModelManager()->getExportFields($this->getClass());

            //here we add some magic :)
            unset($fieldsArray[0]);
            unset($fieldsArray[1]);
            unset($fieldsArray[2]);
            $fieldsArray['ИНН'] = 'client.inn';
            $fieldsArray['Наименование'] = 'client.name';
            $fieldsArray['Ценовой план'] = 'price.name';
            $fieldsArray['Дата начала'] = 'begin_date';
            $fieldsArray['Дата окончания'] = 'end_date';

            return $fieldsArray;
        }
  //   protected function configureSideMenu(Knp\Menu\ItemInterface $menu, $action, Sonata\AdminBundle\Admin\AdminInterface $childAdmin = NULL)
  //   {
		// $menu->addChild(
		//             $action == 'edit' ? 'Просмотр новости' : 'Редактирование новости',
		//             ['uri' => $this->generateUrl(
		//                 $action == 'edit' ? 'show' : 'edit', ['id' => $this->getRequest()->get('id'])])
		//         );
  //   }
}