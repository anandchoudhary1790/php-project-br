<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\MetaValues;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MetaValuesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Speciality';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="meta-values-index">
            <p>
                <?= Html::a('Add New Speciality', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'label',
                'value:ntext',
                [
                'class' => EnumColumn::className(),
                'attribute' => 'status',
                'enum' => MetaValues::statuses(),
                'filter' => MetaValues::statuses()
                ],
                     // 'updated_at',
               /* [
                'attribute'=>'popular',
                'format' => 'raw',
                'value'=>function($obj){
                    $link=Html::a(($obj->popular)?'Yes':'No','javascript:void(0)');
                    return "<div class=update-this data-id=$obj->id data-value=$obj->popular id=fea_$obj->id>$link</div>";
                }
                ],*/
                [
                'content' => function ($model, $key, $index, $column) {
                    $link =Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['aria-label'=>'Edit', 'title'=>'Edit']);

                    return $link;
                }
                ],        ],
                ]); ?>
            </div>
        </div>
    </div>



    <?php 
    $this->registerJs('
       $(".update-this").on("click", function() { 

        id=$(this).attr("data-id");
        is_featured=$(this).attr("data-value");
        $.ajax({
          method: "POST",
          url: "featured-update",
          data: { id: id, is_featured: is_featured, }
      })
      .done(function( msg ) { 
        if(msg){ 
            is_featured=(is_featured==1)?0:1;
            $("#fea_"+id).attr("data-value",is_featured);
            $("#fea_"+id).html("");
            $("#fea_"+id).html(msg);
            
        }
        
    });


});', \yii\web\VIEW::POS_END);

?>