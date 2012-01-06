<?php

defined('BW') or die("Acesso negado!");

class bwPluginAnalytics
{
    function beforeDisplay()
    {
        $config = new bwConfigDB();
        $id = $config->getValue('plugins.analytics.code');

        if (!BW_ADM && $_SERVER['HTTP_HOST'] != 'localhost' && $id != '')
        {
            $html = "

                    <script type=\"text/javascript\">

                      var _gaq = _gaq || [];
                      _gaq.push(['_setAccount', '{$id}']);
                      _gaq.push(['_trackPageview']);

                      (function() {
                            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                      })();

                    </script>

            </body>";

            $buffer = bwBuffer::getInstance();
            $buffer->setHtml(str_replace('</body>', $html, $buffer->getHtml()));
        }
    }

}
?>
