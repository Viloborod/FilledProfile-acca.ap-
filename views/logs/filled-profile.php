<?php
/**
 * Created by PhpStorm.
 * User: Viloborod
 * Date: 20.01.2018
 * Time: 23:02
 */

use app\assets\FilledProfileAsset;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $filledDataProvider
 * @var app\models\finders\FilledProfileSearch $filledModel
 * @var array $cities
 */

FilledProfileAsset::register($this);

?>
<div class="tabs-block">
    <div class="tabs-bar report mt0 ">
        <div id="tab1" data-tab="fill-quality" class="tab active">Качество профилей</div>
        <div id="tab2" data-tab="access-project" class="tab active">Доступы к проектам</div>
        <div class="line fill"></div>
    </div>
</div>

<div class="panel" id="tab1-panel">
<!--    <div style="font-size: 24px; color: red; font-weight: bold; margin-bottom: 10px">В разработке</div>-->
    <?= $this->render('filled-profile/filled', [
        'dataProvider' => $filledDataProvider,
        'searchModel' => $filledModel,
        'cities' => $cities,
    ]) ?>
</div>

<div class="panel" id="tab2-panel" style="display: none">
    <div style="font-size: 24px; color: red; font-weight: bold; margin-bottom: 10px">В разработке</div>
    <div class="filter__block filter__block--grey mt20">
        <div class="section section--grey">
            <div class="filter__inp-block">
                <input type="text" class="filter__inp " value="" placeholder="Поиск по ID">
            </div>

        </div>
        <div class="section section-tag mt20">
            <div class="filter__tag">
                <p class="filter__cnt">Distrib/Booking</p>
                <div class="filter__tag-close"></div>
            </div>
        </div>
    </div>
    <table class="access-project">
        <thead>
        <tr class="table-cell">
            <th>№</th>
            <th>Услуга</th>
            <th>TV Агент</th>
            <th>ПроТурагент</th>
            <th>Консультант</th>
            <th>Спец Агент</th>
        </tr>
        </thead>
        <tbody>

        <tr class="table-title-str">
            <td></td>
            <td>Distrib/Booking</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>1</td>
            <td>Распределитель (новые заявки)</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
        </tr>

        <tr>
            <td>2</td>
            <td>Личный кабинет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-yes">да</td>

        </tr>

        <tr>
            <td>3</td>
            <td>Авто распределение заявок (квота)</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-yes">да</td>

        </tr>

        <tr class="table-title-str">
            <td></td>
            <td>TopHotels</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>1</td>
            <td>Визитка + шилдик ПРО</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-no">нет</td>
        </tr>


        <tr>
            <td>2</td>
            <td>Форма заявки</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-yes">да</td>
        </tr>

        <tr>
            <td>3</td>
            <td>Вывод контактов</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-no">нет</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Вывод в каталоге</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-no">нет</td>
        </tr>
        <tr>
            <td>5</td>
            <td> Реклама в профиле отеля</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-no">нет</td>
            <td class="access-project__service-yes">да</td>
            <td class="access-project__service-no">нет</td>
        </tr>
        </tbody>
    </table>
</div>