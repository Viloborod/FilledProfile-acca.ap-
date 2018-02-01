<?php
/**
 * Created by PhpStorm.
 * User: Viloborod
 * Date: 20.01.2018
 * Time: 23:33
 */

namespace app\models\finders;
use yii\data\BaseDataProvider;
use yii\data\ActiveDataProvider;
use app\models\travelpassport\User;
use app\models\th\User as thUser;
use app\models\acca\AgentService;
use app\models\travelpassport\TpApRel;
use app\models\lst\UserDetail;
use app\tools\Helper;
use yii\db\Query;
use yii\base\Model;

class FilledProfileSearch extends Model {

    public $cityIds;
    public $userId;
    public $isAddress;

    /** @var User */
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cityIds'], 'each', 'rule' => ['integer']],
            [['userId'], 'integer', 'min' => 1, 'max' => 2147483647],
            [['isAddress'], 'in', 'range' => [0, 1, 'all']],
            [['isAddress'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'cityIds' => 'Город',
        ];
    }

    /**
     * @param $params
     * @return BaseDataProvider
     */
    public function search($params) {
        $this->load($params) || $this->load($params, '');

        $serviceQuery = AgentService::find()
            ->select(['tp_user_id', 'date_to' => 'MIN(date_to)'])
            ->andWhere('current_date <= agent_service.date_to')
            ->andWhere('agent_service.trash = FALSE')
            ->groupBy('tp_user_id');

        $query = User::find()
            ->with(['agentServices' => function ($query) {
                /** @var Query $query */
                return $query
                    ->andWhere('current_date <= agent_service.date_to')
                    ->andWhere('agent_service.trash = FALSE');
            }])
            ->with(['avatar','departureCity', 'specialization', 'thUser.taUserStatistics', 'thUser.taAllocationStatuses', 'thUser.userDop', 'client.crmWorkTime'])
            ->joinWith('client.crmAddress')
            ->join('LEFT JOIN', ['service' => $serviceQuery], 'service.tp_user_id = tp_user.id')
            ->andWhere('service.tp_user_id IS NOT NULL')
            ->andFilterWhere(['tp_user.id' => $this->userId])
            ->andFilterWhere(['tp_user.city' => $this->cityIds]);

        if ($this->isAddress != 'all') {
            if ($this->isAddress) {
                $query->andWhere('crm_address.address IS NOT NULL');
            } else {
                $query->andWhere('crm_address.address IS NULL');
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100
            ],

        ]);

        return $dataProvider;
    }


    /**
     * @return User
     */
    public function getUser() {
        if (!$this->_user && $this->userId) {
            $this->_user = User::findOne($this->userId);
        }
        return $this->_user;
    }
}