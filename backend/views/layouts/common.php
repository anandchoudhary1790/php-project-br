<?php
/**
 * @var $this yii\web\View
 */
use backend\assets\BackendAsset;
use backend\widgets\Menu;
use common\models\TimelineEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$bundle = BackendAsset::register($this);
$baseUrl= Yii::getAlias('@backendUrl');

?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
    <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
        <header class="main-header">
            <a href="<?php echo Yii::getAlias('@backendUrl') ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <?php echo Yii::$app->name ?>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only"><?php echo Yii::t('backend', 'Toggle navigation') ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li id="timeline-notifications" class="notifications-menu">
                            <a href="<?php echo Url::to(['/timeline-event/index']) ?>">
                                <i class="fa fa-bell"></i>
                                <span class="label label-success">
                                    <?php echo TimelineEvent::find()->today()->count() ?>
                                </span>
                            </a>
                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
                        
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="user-image">
                                <span><?php echo Yii::$app->user->identity->username ?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header light-blue">
                                    <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle" alt="User Image" />
                                    <p>
                                        <?php echo Yii::$app->user->identity->username ?>
                                        <small>
                                            <?php echo Yii::t('backend', 'Member since {0, date, short}', Yii::$app->user->identity->created_at) ?>
                                        </small>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <?php echo Html::a(Yii::t('backend', 'Profile'), ['/sign-in/profile'], ['class'=>'btn btn-default btn-flat']) ?>
                                    </div>
                                    <div class="pull-left">
                                        <?php echo Html::a(Yii::t('backend', 'Account'), ['/sign-in/account'], ['class'=>'btn btn-default btn-flat']) ?>
                                    </div>
                                    <div class="pull-right">
                                        <?php echo Html::a(Yii::t('backend', 'Logout'), ['/sign-in/logout'], ['class'=>'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <?php //echo Html::a('<i class="fa fa-cogs"></i>', ['/site/settings'])?>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle" />
                    </div>
                    <div class="pull-left info">
                        <p><?php echo Yii::t('backend', 'Hello, {username}', ['username'=>Yii::$app->user->identity->getPublicIdentity()]) ?></p>
                        <a href="<?php echo Url::to(['/sign-in/profile']) ?>">
                            <i class="fa fa-circle text-success"></i>
                            <?php echo Yii::$app->formatter->asDatetime(time()) ?>
                        </a>
                    </div>
                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?php echo Menu::widget([
                    'options'=>['class'=>'sidebar-menu'],
                    'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
                    'submenuTemplate'=>"\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                    'activateParents'=>true,
                    'items'=>[
                        [
                            'label'=>Yii::t('backend', 'Main'),
                            'options' => ['class' => 'header']
                        ],
                        [
                            'label'=>Yii::t('backend', 'Timeline'),
                            'icon'=>'<i class="fa fa-bar-chart-o"></i>',
                            'url'=>['/timeline-event/index'],
                            'badge'=> TimelineEvent::find()->today()->count(),
                            'badgeBgClass'=>'label-success',
                        ],
                        [
                            'label'=>Yii::t('backend', 'Content'),
                            'url' => '#',
                            'icon'=>'<i class="fa fa-edit"></i>',
                            'options'=>['class'=>'treeview'],
                            'items'=>[
                                ['label'=>Yii::t('backend', 'Static pages'), 'url'=>['/page/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                                 ['label'=>Yii::t('backend', 'Home pages'), 'url'=>['/popular/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                                [
                                    'label'=>Yii::t('backend', 'Slider Images'),
                                    'icon'=>'<i class="fa fa-th"></i>',
                                    'url'=>['/slider-image/index'],
                                    // 'active' => Yii::$app->controller->id == 'meta-values',
                                    'visible'=>Yii::$app->user->can('administrator')
                                ],
                            ]
                        ],

                         [
                            'label'=>Yii::t('backend', 'Users'),
                            'url' => '#',
                            'icon'=>'<i class="fa fa-cogs"></i>',
                            'options'=>['class'=>'treeview'],
                            'items'=>[
                                [
                                    'label'=>Yii::t('backend', 'Admin Users'),
                                    'icon'=>'<i class="fa fa-user-plus"></i>',
                                    'url'=>['/user/index'],
                                    'visible'=>Yii::$app->user->can('administrator')
                                ], 
                            ],
                        ],
                        [
                            'label'=>Yii::t('backend', 'Patients'),
                            'icon'=>'<i class="fa fa-wheelchair"></i>',
                            'url'=>['/patient/index'],
                            'active' => Yii::$app->controller->id == 'patient',
                            'visible'=>Yii::$app->user->can('administrator')
                        ],
                        [
                            'label'=>Yii::t('backend', 'Doctors'),
                            'icon'=>'<i class="fa fa-user-md"></i>',
                            'url'=>['/doctor/index'],
                            'active' => Yii::$app->controller->id == 'doctor',
                            'visible'=>Yii::$app->user->can('administrator')
                        ],
                        [
                            'label'=>Yii::t('backend', 'Hospitals'),
                            'icon'=>'<i class="fa fa-user-md"></i>',
                            'url'=>['/hospital/index'],
                            'active' => Yii::$app->controller->id == 'hospital',
                            'visible'=>Yii::$app->user->can('administrator')
                        ],

                        [
                            'label' => Yii::t('backend', 'Advertisement'),
                            'icon' => '<i class="fa fa-adn"></i>',
                            'url' => ['/advertisement/index'],
                            'visible' => Yii::$app->user->can('administrator')
                        ],

                        [
                            'label' => Yii::t('backend', 'Contact Us'),
                            'icon' => '<i class="fa fa-edit"></i>',
                            'url' => ['/contactus/index'],
                            'visible' => Yii::$app->user->can('administrator')
                        ],

                        [
                            'label'=>Yii::t('backend', 'Settings'),
                            'url' => '#',
                            'icon'=>'<i class="fa fa-cogs"></i>',
                            'options'=>['class'=>'treeview'],
                            'items'=>[


                                        [
                                            'label'=>Yii::t('backend', 'Speciality'),
                                            'icon'=>'<i class="fa fa-plus-circle"></i>',
                                            'url'=>['/speciality/index'],
                                            'active' => Yii::$app->controller->id == 'speciality',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Treatments'),
                                            'icon'=>'<i class="fa fa-th"></i>',
                                            'url'=>['/meta-values/treatment'],
                                            'active' => Yii::$app->controller->id == 'meta-values',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Services'),
                                            'icon'=>'<i class="fa fa-server"></i>',
                                            'url'=>['/services/index'],
                                            'active' => Yii::$app->controller->id == 'services',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Degree'),
                                            'icon'=>'<i class="fa fa-graduation-cap"></i>',
                                            'url'=>['/degree/index'],
                                            'active' => Yii::$app->controller->id == 'degree',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                       /* [
                                            'label'=>Yii::t('backend', 'Blood Group'),
                                            'icon'=>'<i class="fa fa-server"></i>',
                                            'url'=>['/blood-group/index'],
                                            'active' => Yii::$app->controller->id == 'blood-group',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Address Types'),
                                            'icon'=>'<i class="fa fa-address-book-o"></i>',
                                            'url'=>['/address-type/index'],
                                            'active' => Yii::$app->controller->id == 'address-type',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],*/
                                       /* [
                                            'label'=>Yii::t('backend', 'Meta Keys'),
                                            'icon'=>'<i class="fa fa-th"></i>',
                                            'url'=>['/meta-keys/index'],
                                            'active' => Yii::$app->controller->id == 'meta-keys',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Meta Values'),
                                            'icon'=>'<i class="fa fa-th"></i>',
                                            'url'=>['/meta-values/index'],
                                            'active' => Yii::$app->controller->id == 'meta-values',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],*/


                                        [
                                            'label'=>Yii::t('backend', 'States'),
                                            'icon'=>'<i class="fa fa-th"></i>',
                                            'url'=>['/states/index'],
                                             'active' => Yii::$app->controller->id == 'states',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Cities'),
                                            'icon'=>'<i class="fa fa-th"></i>',
                                            'url'=>['/cities/index'],
                                             'active' => Yii::$app->controller->id == 'cities',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Areas'),
                                            'icon'=>'<i class="fa fa-th"></i>',
                                            'url'=>['/areas/index'],
                                            'active' => Yii::$app->controller->id == 'areas',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],
                                        [
                                            'label'=>Yii::t('backend', 'Default Settings'),
                                            'icon'=>'<i class="fa fa-gear"></i>',
                                            'url'=>['/settings/index'],
                                            'active' => Yii::$app->controller->id == 'settings',
                                            'visible'=>Yii::$app->user->can('administrator')
                                        ],

                                ],

                        ]
                    ]
                ]) ?>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php echo $this->title ?>
                    <?php if (isset($this->params['subtitle'])): ?>
                        <small><?php echo $this->params['subtitle'] ?></small>
                    <?php endif; ?>
                </h1>

                <?php echo Breadcrumbs::widget([
                    'tag'=>'ol',
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </section>

            <!-- Main content -->
            <section class="content">
                <?php if (Yii::$app->session->hasFlash('alert')):?>
                    <?= $this->render('_flash_alert'); ?>
                <?php endif; ?>
                <?php echo $content ?>
            </section><!-- /.content -->
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

<!-- Model confirm message Sow -->
<div id="ConfirmModalShow" class="modal fade " role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
  <div class="modal-body" id="ConfirmModalContent">

  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-default" id="confirm_ok">Ok</button>
    <button type="button" data-dismiss="modal" class="btn btn-primary">Cancel</button>
  </div>
  </div>
  </div>
  
</div>

<!-- Model confirm message Sow -->
<div id="FileModalShow" class="modal fade " role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" id="FileModalContent">

  </div>
  </div> 
</div>
  <input type="hidden" name="uribase" id="uribase" value="<?php echo $baseUrl; ?>"/>
<?php $this->endContent(); ?>
