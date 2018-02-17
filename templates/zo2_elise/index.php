<?php
/**
 * Zo2 (http://www.zootemplate.com/zo2)
 * A powerful Joomla template framework
 *
 * @version     1.4.4
 * @link        http://www.zootemplate.com/zo2
 * @link        https://github.com/cleversoft/zo2
 * @author      ZooTemplate <http://zootemplate.com>
 * @copyright   Copyright (c) 2015 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted Access');

require_once __DIR__ . '/includes/bootstrap.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $this->zo2->template->getLanguage(); ?>" dir="<?php echo $this->zo2->template->getDirection(); ?>">
    <head>
        <?php unset($this->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']); ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="yandex-verification" content="ad75c1e4a413ec7b" />
        <meta name="description" content="Почему Мягкая Постель? Потому что ассортимент коллекций на нашем сайте тщательно подобран исходя из разумного и взвешенного подхода к выбору постельного белья и текстиля для дома, основанного на трех основных принципах: Качество, Стоимость, Стиль. Мы тщательно изучили технологию производства тканей и изготовления текстильных изделий. Мы не предлагаем Вам как изделия низкой ценовой категории из-за их невысокого качества, так и неоправданно дорогие изделия, цена на которые очевидно включает в себя стоимость громкого бренда. Мы руководствуемся принципом золотой середины и предлагаем Вам разумное соотношение цены и качества. О вкусах не спорят. Мы и не спорим. Мы предлагаем Вам только то, что нам самим нравится, то, что мы с радостью купили бы себе или в подарок свои близким." />

        <!-- Enable responsive -->
        <?php if (!$this->zo2->framework->get('non_responsive_layout')) : ?>
            <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php endif; ?> 
    <jdoc:include type="head" />
    <script type="text/javascript">
        var root = "<?php echo JURI::root(); ?>";
    </script>
</head>
<?php
    $url_arr = explode('/' ,JURI::current());
?>
<body class="<?php echo $this->zo2->layout->getBodyClass(); ?> <?php echo $this->zo2->template->getDirection(); ?> <?php echo $this->zo2->framework->isBoxed() ? 'boxed' : ''; ?> <?php echo $url_arr[count($url_arr) -1]; ?>">
    <div class="se-pre-con"></div>
    <style>
        #zo2-none{
            display: none !important;
        }
    </style>
    <?php echo $this->zo2->template->fetch('html://layouts/css.condition.php'); ?>
    <!-- Main wrapper -->
    <section class="zo2-wrapper<?php echo $this->zo2->framework->isBoxed() ? ' boxed container' : ''; ?>">        
        <?php echo $this->zo2->layout->render(); ?>               
    </section>    
    <?php echo $this->zo2->template->fetch('html://layouts/joomla.debug.php'); ?>
    <script type="text/javascript">
		<?php echo $this->zo2->utilities->bottomscript->render(); ?>
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/yandex-metrica-watch/watch.js" type="text/javascript"></script>
<script type="text/javascript" >
try {
    var yaCounter46119231 = new Ya.Metrika({
        id:46119231,
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        trackHash:true,
        ecommerce:"dataLayer"
    });
} catch(e) { }
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/46119231" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<script type="text/javascript">
jQuery(document).ready(function($) {
      jQuery('.btn-addtocart').on('click', function(e) {
               yaCounter46119231.reachGoal('addtocard');
      });
});  
</script>
<!-- BEGIN JIVOSITE CODE -->
<script type='text/javascript'>
(function(){ var widget_id = '4vaJPyNAHw';var d=document;var w=window;function l(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- END JIVOSITE CODE -->
</body>
</html>
