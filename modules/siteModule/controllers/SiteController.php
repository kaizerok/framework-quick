<?php

namespace modules\siteModule\controllers;

use quick\Controller;

class SiteController extends Controller
{
    protected $layout = "/views/layout/index";

    public function actionIndex()
    {
        $name = 'Quick';            // дані

        echo $this->render('view_name',  // <-- ім’я view
            [
                'name' => $name     // <-- передаємо у view дані на відображення
            ]);

    }

    public function actionAbout()
    {

    }
}
