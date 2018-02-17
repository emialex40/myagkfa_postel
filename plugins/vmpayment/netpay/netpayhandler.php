<?php

    header('location: http://'.$_SERVER['SERVER_NAME']
                .'/index.php?option=com_virtuemart+&view=vmplg&task=pluginnotification&pm=netpay'
                .($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : ''));

?>