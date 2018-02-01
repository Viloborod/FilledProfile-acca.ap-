<?php namespace app\controllers;
 
use app\components\DistribTourbook;
use app\components\LdapController;
use app\models\acca\AgentTourindexTestCounter;
use app\models\crm\Client;
use app\models\dict\DepartureCity;
use app\models\dict\Subway;
use app\models\tourindex\TestSearchCounter;
use yii\web\Controller;
use app\components\TravelPassport;
use app\models\acca\AgentService;
use app\models\acca\CrmAddress;
use app\models\acca\UserRoutesLog;
use app\models\ClientSiteLog;
use app\models\dict\VwDictCity;
use app\models\finders\ProAgentCitiesSearch;
use app\models\finders\ProAgentLogSearch;
use app\models\finders\ProAgentSearch;
use app\models\finders\FilledProfileSearch;
use app\models\finders\TourindexSearch;
use app\models\finders\TpCrmIdLogEditorsSearch;
use app\models\finders\TpCrmIdLogSearch;
use app\models\finders\TpCrmIdLogStatSearch;
use app\models\forms\AgentAccessForm;
use app\models\forms\CrmTpassForm;
use app\models\th\AgentAccess;
use app\models\travelpassport\User;
use app\tools\Helper;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Connection;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\AccessControl;


class LogsController extends Controller {

    /**
         * @inheritdoc
         */
    public function behaviors() {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['readAccaAP'],
                        ],
                    ],
                ],
            ];
        }

     /**
     * @return string
     */
    public function actionLogsFilledProfile() {
        $filledModel = new FilledProfileSearch();
        $filledDataProvider = $filledModel->search(\Yii::$app->request->get());

        $cities = VwDictCity::find()
            ->select(['id', 'name'])
            ->andWhere([
                'exists',
                AgentAccess::find()
                    ->leftJoin('th.vw_th_user', 'vw_th_user.id = tp_user_id')
                    ->andWhere('vw_th_user.city = vw_dict_city.id')
            ])
            ->orderBy('name')
            ->asArray()
            ->all(Yii::$app->get('db_th'));

        return $this->render('filled-profile', [
            'filledModel' => $filledModel,
            'filledDataProvider' => $filledDataProvider,
            'cities' => ArrayHelper::map($cities, 'id', 'name'),
        ]);
    }
}