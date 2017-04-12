<?php
namespace PersonalArea\Bundle\ClientAreaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Knp\Menu\ItemInterface as MenuItemInterface;

class BillingServiceAdmin extends Admin {
	protected $baseRouteName = 'service';
    protected $baseRoutePattern = 'service';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Наименование'])
            ->add('code', 'text', ['label' => 'Код',
                                   'attr'  => [
                                           'placeholder' => 'Наведите мышь и прочитайте инструкцию',
                                           'title' => 'ИСПОЛЬЗУЙТЕ ТОЛЬКО ЗАГЛАВНЫЕ БУКВЫ. РАЗДЕЛЯЙТЕ МЕТОДЫ ЧЕРЕЗ -.
                                           ДЛЯ ТИПОВ ОТЧЕТОВ ПРЕДУСМОТРЕНО СЛЕДУЮЩЕЕ. 0 - ПЕРВОНАЧАЛЬНЫЙ, 1 - УТОЧНЕННЫЙ,
                                           2 - ДОПОЛНИТЕЛЬНЫЙ, 3 - ЛИКВИДАЦИОННЫЙ. ПРИМЕР (RSVI-STI-FORM017-0) ОЗНАЧАЕТ ЧТО
                                           УСЛУГА ПРЕНАДЛЕЖИТ СОЧИ, РАЗДЕЛ НАЛОГОВАЯ, ФОРМА 017, ПЕРВОНАЧАЛЬНАЯ.',
                                           'pattern' => '[A-Z0-9-]+'
                                           ]])
            ->add('subscriber', null, ['label' => 'Поставщик']);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Наименование'])
            ->add('code', null, ['label' => 'Код'])
            ->add('subscriber', null, ['label' => 'Поставщик']);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name', null, ['label' => 'Наименование'])
            ->add('code', null, ['label' => 'Код'])
            ->add('subscriber', null, ['label' => 'Поставщик']);
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

    public function getExportFields()
    {
        $fieldsArray = $this->getModelManager()->getExportFields($this->getClass());

        //here we add some magic :)
        unset($fieldsArray[0]);
        unset($fieldsArray[1]);
        unset($fieldsArray[2]);
        $fieldsArray['Идентификатор'] = 'id';
        $fieldsArray['Код'] = 'code';
        $fieldsArray['Наименование'] = 'name';
        $fieldsArray['Поставщик'] = 'subscriber.name';

        return $fieldsArray;
    }
}