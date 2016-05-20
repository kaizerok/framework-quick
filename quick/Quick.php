<?php

namespace quick;

use quick\helpers\ArrayHelper;

/**
 * Class Application
 *
 * @property \quick\request\Request $request
 * @property \quick\urlManager\UrlManagerInterface $urlManager
 * @method \quick\request\Request getRequest()
 * @method \quick\urlManager\UrlManagerInterface getUrlManager()
 * @method \quick\Controller setPathToViews()
 * @package quick
 */
class Quick
{
    public static $app;

    protected $config;

    protected $components = [];

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = ArrayHelper::merge($this->getDefaultConfig(), $config);
    }

    /**
     * @param $property
     * @return mixed
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function __get($property)
    {
        if (!isset($this->$property)) {
            throw new InvalidConfigException("This class '{$property}' not exist
            in config file and components array.");
        }

        if ($this->isComponentRegistered($property)) {
            return $this->components[$property];
        }

        $component = $this->config['components'][$property];
        return $this->registerComponent($property, $component);
    }

    /**
     * @param $property
     * @return bool
     * @throws InvalidConfigException
     */
    public function __isset($property)
    {
        return $this->isComponentRegistered($property) || isset($this->config['components'][$property]);
    }

    /**
     * @param $name
     * @param $arg
     * @return mixed
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function __call($name, $arg)
    {
        $methodName = lcfirst(substr($name, 3));

        if (!isset($this->$methodName)) {
            throw new InvalidConfigException("This class '{$methodName}' not exist
            in config file and components array.");
        }

        if ($this->isComponentRegistered($methodName)) {
            return $this->components[$methodName];
        }

        $component = $this->config['components'][$methodName];
        return $this->registerComponent($methodName, $component);
    }

    /**
     * This method runs a web application
     * @param array $config
     */
    public static function runWebApplication(array $config)
    {
        try {
            self::$app = new self($config);

            $uri = self::$app->getRequest()->getRequestUri();
            $path = self::$app->getUrlManager()->resolve($uri);

            list($moduleName, $controllerName, $actionName) = explode('/', $path);

            if (($moduleName == null || $controllerName == null || $actionName == null)) {
                throw new \Exception("Routes in config file have to be for example 'site/site/index'");
            }

            $controllerClass = 'modules\\'
                . $moduleName . 'Module\\'
                . 'controllers\\'
                . ucfirst($controllerName) . 'Controller';

            if (!class_exists($controllerClass)) {
                throw new FileNotExistException("Class '{$controllerClass}' not exist.");
            }

            $controllerClassParent = 'quick\Controller';

            if (!class_parents($controllerClass, $controllerClassParent)) {
                throw new InvalidExtendsException("Class '{$controllerClass}'
                have to extends '{$controllerClassParent}'");
            }

            $controllerObj = new $controllerClass();

            $pathToModule = __DIR__ . '/../'
                . 'modules/'
                . $moduleName . 'Module/';
            $controllerObj->setPathToModule($pathToModule);

            $pathToViews = $pathToModule
                . 'views/site/';
            $controllerObj->setPathToViews($pathToViews);

            $action = 'action' . ucfirst($actionName);
            $controllerObj->$action();

        } catch (FileNotExistException $e) {
            echo $e->getMessage();
        } catch (InvalidExtendsException $e) {
            echo $e->getMessage();
        } catch (RouteNotFoundException $e) {
            echo $e->getMessage();
        } catch (InvalidConfigException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function isComponentRegistered($name)
    {
        return isset($this->components[$name]);
    }

    /**
     * @param $name
     * @param $component
     * @return mixed
     * @throws \Exception
     */
    public function registerComponent($name, $component)
    {
        if (!isset($component['class'])) {
            throw  new \Exception('Component doesn\'t have class property');
        }

        $className = $component['class'];
        $data = $component;
        unset($data['class']);
        unset($data['dependency']);
        $dependency = isset($component['dependency']) ? $component['dependency'] : null;

        self::$app->components[$name] = self::createComponent($className, $data, $dependency);
        return self::$app->components[$name];
    }

    /**
     * @param $className
     * @param array|null $data
     * @param null $dependency
     * @return mixed
     * @throws \Exception
     */
    public function createComponent($className, array $data = null, $dependency = null)
    {
        $component = new $className();

        if (!($component instanceof ComponentAbstract)) {
            throw new \Exception('ComponentAbstract');
        }

        if (($dependency !== null) and !($component instanceof $dependency)) {
            throw  new \Exception('dependency errors');
        }

        foreach ($data as $property => $value) {
            $methodName = 'set' . ucfirst($property);
            if (method_exists($component, $methodName)) {
                $component->$methodName($value);
            } elseif (isset($component->$property)) {
                $component->$property = $value;
            } else {
                throw new \Exception('Cant set value');
            }
        }

        $component->init();
        return $component;
    }

    /**
     * Method getDefaultConfig get to default config.
     * @return array
     */
    public function getDefaultConfig()
    {
        return [
            'components' => [
                'urlManager' => [
                    'class' => '\quick\urlManager\UrlManager',
                    'dependency' => '\quick\urlManager\UrlManagerInterface',
                ],
                'request' => [
                    'class' => '\quick\request\Request'
                ]
            ]
        ];
    }
}
