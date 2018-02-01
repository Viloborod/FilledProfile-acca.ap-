<?php
/**
 * Created by PhpStorm.
 * User: Viloborod
 * Date: 20.01.2018
 * Time: 23:45
 */

use app\components\GridView;
use app\models\travelpassport\User;
use yii\helpers\Html;
use app\components\helpers\HtmlHelper;
use app\models\th\TaAllocationStatus;
use yii\widgets\Pjax;
use dosamigos\selectize\SelectizeDropDownList;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var app\models\finders\FilledProfileSearch $searchModel
 * @var array $cities
 */

?>


<div class="filter__block filter__block--grey mt20">
    <div class="section section--grey">
        <div class="filter__inp-block">
            <input type="text" class="filter__inp " value="" data-toggle="dropdown" data-target="#filled-filter-dd"
                   id="filled-filter-input" placeholder="Поиск по городу или по имени">
            <div class="dropdown-menu" id="filled-filter-dd" style="width: 100%"></div>
            <a href="javascript:void(0)" class="filter__filter-open js-filter-open">
                <i class="fa fa-sliders filter-icon"></i>
                <span class="">Фильтр</span>
            </a>
        </div>
    </div>
</div>

<div class="section section-tag mt20" id="agent-list-tags"></div>

<div class="filter-pp__container filled-profile" style="display: none;">
    <div class="filter-pp">
        <div class=" filter-pp__top">
            <div class="filter-pp__close js-filter-close"></div>
            <h2 class="filter-pp__ttl2">Параметры</h2>
        </div>

        <?php $form = \yii\widgets\ActiveForm::begin([
            'method' => 'get',
            'action' => '/logs/filled-profile',
            'id' => 'filled-filter',
            'options' => [
                'data-tag-list' => '#agent-list-tags',
            ]
        ]) ?>

        <div class="filter-pp__section mt20">
            <?= $form->field($searchModel, 'cityIds', [
                'labelOptions' => [
                    'class' => 'filter-pp__ttl-block',
                ],
                'inputOptions' => [
                    'class' => 'filter__inp',
                ],
                'template' => "{label}\n{input}\n{hint}\n{error}",
            ])->widget(SelectizeDropDownList::className(), [
                'items' => $cities,
                'options' => [
                    'name' => 'cityIds',
                    'multiple' => true,
                ],
                'clientOptions' => [
                    'plugins' => ['remove_button'],
                ]
            ]) ?>
        </div>

        <div class="filter-pp__section mt20">
            <?= Html::activeHiddenInput($searchModel, 'userId', [
                'name' => 'userId',
                'data-value' => $searchModel->getUser() ? $searchModel->getUser()->getFullName() : null,
            ]) ?>
        </div>
        <div class="filter-pp__section mt20">
            <?= $form->field($searchModel, 'isAddress', [
                'options' => [
                    'class' => 'rbt-pp-block mb10',
                ],
            ])->radioList(['1' => 'Есть', '0' => 'Нет', 'all' => 'Не важно'], [
                'unselect' => null,
                'item' => function ($index, $label, $name, $checked, $value) {
                    $id = rand();
                    $default = '';
                    if ($value == 'all') {
                        $default = 'default';
                    }
                    $check = $checked ? 'checked' : '';
                    $dataValue = ['1' => 'Адрес', '0' => 'Без адреса', 'all' => ''];
                    return '
                    <div class="rbt-pp-block mb10">
                        <input ' . $default . ' ' . $check . ' data-value="' . ($dataValue[$value]) . '" name="'. 'isAddress' . '" class="rbt-pp" id="address-' . $id . '" type="radio" value="'. $value .'">
                        <label class="label-rbt-pp" for="address-' . $id . '">
                            <div class="label-rbt-pp-cnt">' . $label . '</div>
                        </label>
                    </div>';
                }
            ])->label('Адрес', ['class' => 'filter-pp__ttl-block']); ?>
        </div>


        <div class="filter-pp__section mt20">
            <button class="filter-pp__btn mr10">Найти</button>
            <a href="#" class="filter-pp__link-del">Сбросить</a>
        </div>
    </div>
</div>

<?php \yii\widgets\ActiveForm::end() ?>

<?php Pjax::begin([
    'id' => 'pjax-filed',
    'timeout' => 10000,
    'linkSelector' => '#pjax-filed .pages-nav-block a',
    'formSelector' => '#filled-filter',
]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'pager' => [
        'class' => 'app\components\LinkPager',
    ],
    'tableOptions' => ['class' => 'profile'],
    'headerRowOptions' => [
        'class' => 'table-cell',
    ],
    'layout' => "{summary}\n{items}\n{pager}",
//    'layout' => "{items}\n{pager}",
    'columns' => [
        [
            'label' => 'Профиль',
            'format' => 'raw',
            'headerOptions' => ['class' => 'big-cell'],
            'value' => function ($user) {
                /** @var User $user */
                return '<div class="user-block">
                        <div class="user-block__ava">'
                    . Html::a(
                        Html::img($user->getAvatarUrl('50x50')),
                        $user->getUrl(),
                        ['target' => '_blank']
                    ) . '
                        </div>
                        <div class="user-block__info">'
                    . Html::a(
                        $user->getFullName(),
                        $user->getUrl(),
                        ['class' => 'bold-link', 'target' => '_blank']
                    ) . '
                            <p class="user-block__info-place">' . trim($user->getSpecializationName()) . ($user->departureCity ? '<br>' . $user->departureCity->name : '') . '</p>
                        </div>
                    </div>';
            },
        ],
        [
            'label' => 'Оценки',
            'format' => 'raw',
            'headerOptions' => ['class' => 'normal-big-cell'],
            'value' => function ($user) {
                /** @var User $user */
                $out = '';
                if ($user->thUser && $user->thUser->taUserStatistics) {
                    $raiting = $user->thUser->taUserStatistics->tourbook_rating;
                    $raitingColor = ($raiting < 3) ? 'red' : (($raiting < 4) ? 'yellow' : 'green');
                    $reccomend = $user->thUser->userRecommendsCount;
                    $mark = $user->thUser->taUserStatistics->tourbook_feedbacks_count;
                    $out = '
                            <div class="profile__rating">
                                <span class="bold ' . $raitingColor . ' profile__reccomend__mark">' . number_format(floor($raiting * 10) / 10, 1, ',', ' ') . '</span>
                                <span>Рейтинг</span>
                            </div>'
                    ;
                    $out .= '
                            <div class="profile__reccomend">
                                <span class="bold yellow profile__reccomend__mark">' . $reccomend . '</span>
                                <span>Рекомендаций</span>
                            </div>
                    ';
                    $out .= '
                            <div class="profile__mark">
                                <span class="bold red profile__reccomend__mark">'. $mark .'</span>
                                <span>Оценок</span>
                            </div>
                    ';

                }
                return $out;
            },
        ],
        [
            'label' => 'Стаж',
            'format' => 'raw',
            'headerOptions' => ['class' => 'small-cell'],
            'value' => function ($user) {
                /** @var User $user */
                $out = '';
                if ($user->thUser && $user->thUser->stage && ($user->thUser->stage > 0)) {
                    $out = HtmlHelper::plural('# год|# года|# лет', date('Y') - $user->thUser->stage);
                }
                return $out;
            },
        ],
        [
            'label' => 'Блок контактов',
            'format' => 'raw',
            'headerOptions' => ['class' => 'normal-big-cell'],
            'contentOptions' => ['class' => 'contacts'],
            'value' => function ($user) {
                /** @var User $user */
                $out = '';
                if ($user->thUser) {
                    $out .= '<div class="contacts__street"><span class="' . (($user->client && $user->client->crmAddress && $user->client->crmAddress->address) ? 'contacts__plus' : 'contacts__minus') . ' pl20">Адрес</span></div>';
                    $out .= '<div class="contacts__metro"><span class="' . (($user->client && $user->client->crmAddress && $user->client->crmAddress->subway_ids) ? 'contacts__plus' : 'contacts__minus') . ' pl20">Метро</span></div>';
                    $out .= '<div class="contacts__phone"><span class="' . (($user->thUser->phone) ? 'contacts__plus' : 'contacts__minus') . ' pl20">Телефон</span></div>';
                    $out .= '<div class="contacts__plus"><span class="' . (($user->client && $user->client->crmWorkTime) ? 'contacts__plus' : 'contacts__minus') . ' pl20">Время работы</span></div>';
                    $out .= '<div class="contacts__skype"><span class="' . (($user->thUser->userDop && $user->thUser->userDop->isSocialNetwork()) ? 'contacts__plus' : 'contacts__minus') . ' pl20">Соцсети</span></div>';
                    $out .= '<div class="contacts__cite"><span class="' . (($user->thUser->www) ? 'contacts__plus' : 'contacts__minus') . ' pl20">Сайт</span></div>';
                }
                return $out;
            },
        ],
        [
            'label' => 'Доп. инф-ия',
            'format' => 'raw',
            'headerOptions' => ['class' => 'mid-cell'],
            'value' => function ($user) {
                /** @var User $user */
                $out = '';
                if ($user->thUser) {
                    $out = '<div><span class="' . (($user->thUser->interests) ? 'contacts__plus' : 'contacts__minus') . ' pl20">Интересы</span></div>';
                    $out .= '<div><span class="' . (($user->thUser->description) ? 'contacts__plus' : 'contacts__minus') . ' pl20">О себе</span></div>';
                }
                return $out;
            },
        ],
        [
            'label' => 'Сертификаты',
            'format' => 'raw',
            'headerOptions' => ['class' => 'sertificate'],
            'value' => function ($user) {
                /** @var User $user */
                $out = '';
                if ($user->thUser && $user->thUser->taAllocationStatuses) {
                    $certificates = [];
                    foreach ($user->thUser->taAllocationStatuses as $taAllocationStatus) {
                        if (!isset($certificates[$taAllocationStatus->status])) {
                            $certificates[$taAllocationStatus->status] = 0;
                        }
                        $certificates[$taAllocationStatus->status]++;
                    }
                    foreach ($certificates as $type => $count) {
                        $out .= '
                            <div>
                                <span class="bold sertificate__total">' . $count  . '</span>
                                <span>' . TaAllocationStatus::$typesCertificateTitle[$type] . '</span>
                            </div>
                        ';
                    }
                }
                return $out;
            },
        ],
        [
            'label' => '% запол-ти',
            'format' => 'raw',
            'headerOptions' => ['class' => 'mid-cell'],
            'contentOptions' => function ($user) {
                /** @var User $user */
                $color = '';
                if ($user->thUser) {
                    $profileFill = $user->thUser->getProfileFill();
                    if ($profileFill < 40) {
                        $color = 'red';
                    } elseif ($profileFill < 75) {
                        $color = 'yellow';
                    } else {
                        $color = 'green';
                    }
                }
                return ['class' => $color . ' bold'];
            },
            'value' => function ($user) {
                /** @var User $user */
                $out = '';
                if ($user->thUser) {
                    $out = $user->thUser->getProfileFill() .  '%';
                }
                return $out;
            },
        ],
    ]
]) ?>

<?php Pjax::end() ?>
