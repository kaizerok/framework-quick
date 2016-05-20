<?php

namespace quick;

/**
 * This class is base controller and all controllers have to extend this class
 *
 * Class Controller
 * @package quick
 */
class Controller
{
    protected $pathToModule;

    protected $layout;

    protected $pathToViews;

    /**
     * @param $view
     * @param array $data
     * @return string
     * @throws FileNotExistException
     */
    public function renderPartial($view, array $data = [])
    {
        if (!strpos($view, '.php')) {
            $viewFile = $this->getPathToViews() . $view . '.php';
            if (!is_readable($viewFile)) {
                throw new FileNotExistException("File '{$viewFile}' is not exist");
            }
        } else {
            $viewFile = $view;
        }

        extract($data);

        ob_start();
        ob_implicit_flush(false);
        require($viewFile);
        return ob_get_clean();
    }

    /**
     * This method renders your view
     *
     * @param $view - view name
     * @param array $data - this data will passed to view
     * @return string
     * @throws FileNotExistException
     */
    public function render($view, array $data = [])
    {
        $layoutFile = $this->getPathToModule() . $this->getLayout() . '.php';
        if (!is_readable($layoutFile)) {
            throw new FileNotExistException("File '{$layoutFile}' is not exist");
        }

        $output = $this->renderPartial($view, $data);
        return $this->renderPartial($layoutFile, ['content'=>$output]);
    }

    /**
     * @return mixed
     */
    public function getPathToModule() {
        return $this->pathToModule;
    }

    /**
     * @param $pathToModule
     * @return $this
     */
    public function setPathToModule($pathToModule)
    {
        $this->pathToModule = $pathToModule;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPathToViews()
    {
        return $this->pathToViews;
    }

    /**
     * @param $pathToViews
     * @return $this
     */
    public function setPathToViews($pathToViews)
    {
        $this->pathToViews = $pathToViews;
        return $this;
    }

}
