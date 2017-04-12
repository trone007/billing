<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace PersonalArea\Bundle\ClientAreaBundle\Soap;

use ReflectionObject;
use Zend\Soap\Exception\BadMethodCallException;
use Zend\Soap\Exception\UnexpectedValueException;

/**
 * Class DocumentLiteralWrapper
 * @package PersonalArea\Bundle\ClientAreaBundle\Soap
 * This Class realize intermediate communication between Client and Server.
 *
 * Some magic methods could help you to filter client access to your billing service.
 */

class DocumentLiteralWrapper
{
    const SOAP_FAULT_CODE = 'exitCode';

    /**
     * @var object
     */
    protected $object;

    /**
     * @var ReflectionObject
     */
    protected $reflection;

    /**
     * Pass Service object to the constructor
     *
     * @param object $object
     */
    public function __construct($object, $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->object = $object;
        $this->reflection = new ReflectionObject($this->object);
    }

    /**
     * Proxy method that does the heavy document/literal decomposing.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        try {
            $token      = $args ? $args[0] : null;
            $ip         = $_SERVER['REMOTE_ADDR'];

            /**
             * TODO Realize Logging of Clients methods call.
             * TODO Validate Client Access to Methods.
             */
            // $subscriber = $this->getSubscriberId($token)['subscriber'];
           //  if(!$subscriber) {
           // //      throw new \SoapFault(static::SOAP_FAULT_CODE, 'Данный подписчик не зарегистрирован');
           //  }

           //  if (!($tkn = $this->validateAccess($subscriber, $method))) {
           // //     throw new \SoapFault(static::SOAP_FAULT_CODE, 'Отсутствует подписка на сервис');
           //  }

            $ret = call_user_func_array(array($this->object, $method), $args);

          //  $this->logMethodCall($subscriber, $method, $ret, $args, $ip);
        } catch (\Exception $ex) {

          //  $this->logMethodCall($subscriber, $method, $ex->getMessage(), $args, $ip);
            throw $ex;
        }

        return ($token != 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx') ? $this->_getResultMessage($method, $ret) :
         $ret;
    }

    private function logMethodCall($subscriber, $method, $response, $request, $ip) {
        $manager = $this->doctrine->getManager();

        $subscriber = $manager->getRepository('ClientAreaBundle:BillingSubscriber')->findOneById($subscriber);
        $method = $manager->getRepository('ClientAreaBundle:BillingMethod')->findOneByCallName($method);

        $log = new BillingLogs();
        $log->setSubscriber($subscriber);
        $log->setMethod($method);
        $log->setResponse(json_encode($response, JSON_UNESCAPED_UNICODE));
        $log->setRequest(json_encode($request));
        $log->setDateTime(new \DateTime());
        $log->setIp($ip);

        $manager->persist($log);
        $manager->flush();
    }

    private function getSubscriberId($token) {
        $query = "
        SELECT
         s.id as subscriber
        FROM billing.subscribers AS s
        WHERE
            ENCODE(token, 'hex') = :token";

        $params = array('token' => $token);

        $em = $this->doctrine->getManager();
        $stmt = $em->getConnection()->prepare($query);

        $stmt->execute($params);
        $subscriber = $stmt->fetchAll();

        return $subscriber[0];
    }

    private function validateAccess($subscriber, $method) {
        $query = "
        SELECT
         s.id as subscriber,
         m.id as method
        FROM billing.subscriber_access AS sa
        LEFT JOIN billing.subscribers AS s ON sa.subscriber_id = s.id
        LEFT JOIN billing.methods AS m ON sa.method_id = m.id
        WHERE
            subscriber_id = :subscriber
        AND
            m.call_name = :method

         ";

        $params = array('subscriber' => $subscriber, 'method' => $method);

        $em = $this->doctrine->getManager();
        $stmt = $em->getConnection()->prepare($query);

        $stmt->execute($params);
        $subscriber = $stmt->fetchAll();

        return $subscriber;
    }

    /**
     * Parse the document/literal wrapper into arguments to call the real
     * service.
     *
     * @param string $method
     * @param object $document
     * @throws UnexpectedValueException
     * @return array
     */
    protected function _parseArguments($method, $document)
    {
        $reflMethod = $this->reflection->getMethod($method);
        $params = array();
        foreach ($reflMethod->getParameters() as $param) {
            $params[$param->getName()] = $param;
        }

        $delegateArgs = array();
        foreach (get_object_vars($document) as $argName => $argValue) {
            if (!isset($params[$argName])) {
                throw new UnexpectedValueException(sprintf(
                    "Received unknown argument %s which is not an argument to %s::%s",
                    get_class($this->object), $method
                ));
            }
            $delegateArgs[$params[$argName]->getPosition()] = $argValue;
        }
        return $delegateArgs;
    }

    protected function _getResultMessage($method, $ret)
    {
        return array($method . 'Result' => $ret);
    }

    protected function _assertServiceDelegateHasMethod($method)
    {
        if (!$this->reflection->hasMethod($method)) {
            throw new BadMethodCallException(sprintf(
                "Method %s does not exist on delegate object %s",
                $method, get_class($this->object)
            ));
        }
    }

    protected function _assertOnlyOneArgument($args)
    {
        if (count($args) != 1) {
            throw new UnexpectedValueException(sprintf(
                "Expecting exactly one argument that is the document/literal wrapper, got %d",
                count($args)));
        }
    }
}
