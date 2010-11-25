<?php
/**
 * Ja Zend Framework Auth Adapters
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Ja/Zend
 * @package    Zend_Auth
 * @subpackage Zend_Auth_Adapter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * @namespace
 */
namespace Ja\Authentication\Adapter;

use Zend\Authentication\Adapter as AuthenticationAdapter,
    Zend\Authentication\Result as AuthenticationResult,
    Zend\Session\Container as SessionContainer;

/**
 * @category   Ja/Zend
 * @package    Zend_Auth
 * @subpackage Zend_Auth_Adapter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Oauth implements AuthenticationAdapter
{
    /**
     * OAuth consumer object
     * 
     * @var null|Zend\Oauth\Consumer
     */
    protected $_consumer = null;
    
    /**
     * OAuth query data
     * 
     * @var null|array
     */
    protected $_queryData = null;
    
    /**
     * OAuth access token after successful authentication
     * 
     * @var null|Zend\Oauth\Token\Access
     */
    protected $_accessToken = null;    
    
    /**
     * Array of options for this adapter.  Options include:
     *   - sessionNamespace: session namespace override
     *   
     * @var null|array
     */
    protected $_options = null;
    
    /**
     * Default session container to store session credentials
     * 
     * @var string
     */
    const DEFAULT_SESSION_CONTAINER = 'Ja_Authentication_Adapter_Oauth';
    
    /**
     * Constructor
     * 
     * @param array  $options An array of options for this adapter
     * @param Zend_Oauth_Consumer $consumer Consumer object
     */
    public function __construct(array $options = array(), \Zend\Oauth\Consumer $consumer = null)
    {
        $this->setOptions($options);
        
        if ($consumer !== null) {
            $this->setConsumer($consumer);
        } 
    }
    
    /**
     * Sets the consumer object for authentication
     * 
     * @param Zend\Oauth\Consumer $consumer
     * @return Ja\Authentication\Adapter\Oauth Fluent interface
     */
    public function setConsumer(\Zend\Oauth\Consumer $consumer)
    {
        $this->_consumer = $consumer;
        return $this;
    }
    
    /**
     * Gets the consumer
     * 
     * @return Zend\Oauth\Consumer|null
     */
    public function getConsumer()
    {
        return $this->_consumer;
    }
    
    /**
     * Sets the query data for generation of the access token.  Data
     * is typically passed back to the application from the remote
     * OAuth authentication source.
     * 
     * @param array $queryData array of query data
     * @return Ja\Authentication\Adapter\Oauth Fluent interface
     */
    public function setQueryData(array $queryData)
    {
        $this->_queryData = $queryData;
        return $this;
    }
    
    /**
     * Gets the query data
     * 
     * @return array|null
     */
    public function getQueryData()
    {
        return $this->_queryData;
    }
    
    /**
     * Sets the access token after a successful authentication attempt
     * 
     * @param Zend\Oauth\Token\Access $token access token
     * @return Ja\Authentication\Adapter\Oauth Fluent interface
     */
    public function setAccessToken(\Zend\Oauth\Token\Access $token)
    {
        $this->_accessToken = $token;
        return $this;
    }
    
    /**
     * Gets the access token result
     * 
     * @return Zend\Oauth\Token\Access|null
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }    
        
    /**
     * Returns the array of arrays of options of this adapter.
     *
     * @return array|null
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets the array of arrays of options to be used by
     * this adapter.
     *
     * @param  array $options The array of arrays of options
     * @return Provides a fluent interface
     */
    public function setOptions($options)
    {
        $this->_options = is_array($options) ? $options : array();
        return $this;
    }
    
    /**
     * Authenticate the user
     * 
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        if (!$this->_consumer) {
            $code = AuthenticationResult::FAILURE;
            $message = array('A valid Zend\Oauth\Consumer key is required');
            return new AuthenticationResult($code, '', $message);
        }
        
        $sessionContainer = self::DEFAULT_SESSION_CONTAINER;
        
        if (isset($this->_options['sessionContainer']) && $this->_options['sessionContainer'] != '') {
            $sessionContainer = $this->_options['sessionContainer'];
        }
        
        $session = new SessionContainer($sessionContainer);
        
        try {
            if (!$session->requestToken) {
                
                $token = $this->_consumer->getRequestToken();
        
                $session->requestToken = serialize($token);
                
                $this->_consumer->redirect();
                
            } else {
                
                $accessToken = $this->_consumer->getAccessToken($this->_queryData, unserialize($session->requestToken));

                $this->setAccessToken($accessToken);
                
                unset($session->requestToken);
                
                $body = $accessToken->getResponse()->getBody();
                
                $returnParams = array();
       
                $parts = explode('&', $body);
                foreach ($parts as $kvpair) {
                    $pair = explode('=', $kvpair);
                    $returnParams[rawurldecode($pair[0])] = rawurldecode($pair[1]);
                }               
            }
        } catch (Zend\Oauth\Exception $e) { 
            $session->unsetAll();
            
            $code = AuthenticationResult::FAILURE;
            $message = array('Access denied by OAuth source');
            return new AuthenticationResult($code, '', $message); 
        } catch (Exception $e) {
            $session->unsetAll();
            
            $code = AuthenticationResult::FAILURE;
            $message = array($e->getMessage());
            return new AuthenticationResult($code, '', $message);  
        }
        
        return new AuthenticationResult(AuthenticationResult::SUCCESS, $returnParams, array());        
    }
}