<?php

namespace app\modules\api\controllers;

use app\modules\metric\models\Metric;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;


class MetricController extends \yii\rest\Controller
{
    /**
     * GET request on input
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Metric::find()
        ]);

        return $dataProvider;
    }

    /**
     * POST request on input metric
     * @return Metric
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->getBodyParams();

        $model = new Metric();

        if ($model->load($data, '') && $model->validate()) {
            if ($model->add()) {
                Yii::$app->response->setStatusCode(201);
            }
        }

        return $model;
    }

    /** @inheritDoc */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
//
//        $behaviors['authenticatior'] = [
//            'class' => HttpBearerAuth::class,
//        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true, //разрешить всех
                    'roles' => ['@'],//всех, кто авторизирован
                ],
            ],
        ];

        return $behaviors;
    }
}
