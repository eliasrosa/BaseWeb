<?

defined('BW') or die("Acesso negado!");

class bwPluginResizeImageHelper
{

    function buscaTags()
    {
        $buffer = bwBuffer::getInstance();

        if (preg_match_all('#<img .+?>|<a.*rel="?.*lightbox?.*".*>|\[image .*\]#', $buffer->getHtml(), $tags)) {
            foreach ($tags[0] as $tag) {
                $new = bwPluginResizeImageHelper::_($tag);
                $buffer->setHtml(str_replace($tag, $new, $buffer->getHtml()));
            }
        }
    }

    function getAttr($tag, $attr, $default = NULL)
    {
        preg_match('#(' . $attr . ')=\'([^\']*)\'#', $tag, $out);

        if (isset($out[2]))
            return $out[2];
        else {
            preg_match('#(' . $attr . ')="([^"]*)"#', $tag, $out);

            if (isset($out[2]))
                return $out[2];
            else
                return $default;
        }
    }

    function _($tag)
    {
        $file = bwPluginResizeImageHelper::getAttr($tag, 'href|src', false);
        $w = bwPluginResizeImageHelper::getAttr($tag, 'width', NULL);
        $h = bwPluginResizeImageHelper::getAttr($tag, 'height', NULL);
        $f = bwPluginResizeImageHelper::getAttr($tag, 'fit', 'inside');
        $s = bwPluginResizeImageHelper::getAttr($tag, 'scale', 'any');
        $rule = bwPluginResizeImageHelper::getAttr($tag, 'rule', false);
        $rule_params = bwPluginResizeImageHelper::getAttr($tag, 'rule-params', NULL);

        $ext = bwFile::getExt($file);
        if ($file && ($w || $h) && preg_match('#jpg|gif#', $ext)) {
            $newFile = bwPluginResizeImageHelper::resize($file, $w, $h, $f, $s, $rule, $rule_params);

            if ($newFile) {
                $newTag = $tag;
                $newTag = str_replace($file, $newFile, $newTag);

                if ($w)
                    $newTag = str_replace("width=\"$w\"", '', $newTag);

                if ($h)
                    $newTag = str_replace("height=\"$h\"", '', $newTag);

                if ($f)
                    $newTag = str_replace("fit=\"$f\"", '', $newTag);

                if ($s)
                    $newTag = str_replace("scale=\"$s\"", '', $newTag);

                if ($rule)
                    $newTag = str_replace("rule=\"$rule\"", '', $newTag);

                if ($rule_params)
                    $newTag = str_replace("rule-params=\"$rule_params\"", '', $newTag);

                // se for [image]
                if (substr($tag, 0, 6) == '[image') {
                    $newTag = $newFile;
                }

                // se existir [image
                if (preg_match('#\[image .*\]#', $newTag, $images)) {
                    foreach ($images as $i) {
                        $path = bwPluginResizeImageHelper::_($i);
                        $newTag = str_replace($i, $path, $newTag);
                    }
                }

                return $newTag;
            }
        }else
            return $tag;
    }

    function resize($file, $w, $h, $f = 'inside', $s = 'any', $rule = false,
        $rule_params = NULL)
    {
        $file_original = $file;
        $sha1 = substr(sha1(print_r(func_get_args(), 1)), 0, 10);

        //
        if (bwCore::getConfig()->getValue('core.cache.resizeimage')) {
            $cache = bwCache::get($sha1, false);

            if ($cache !== false) {
                return $cache;
            }
        }

        // limpa o caminho, absoluto ou relativo
        $file = preg_replace('#' . BW_URL_BASE2 . '#', '', $file, 1);
        $file = preg_replace('#' . BW_URL_BASE . '#', '', $file, 1);
        $file = str_replace('/', DS, BW_PATH . DS . $file);
        $file = str_replace(DS . DS, DS, $file);

        // nome e tipo do arquivo
        $file_nameFull = basename($file);
        $file_type = strtolower(array_pop(explode('.', $file_nameFull)));
        $file_name = str_replace(".$file_type", '', $file_nameFull);
        $fileMedia = str_replace(BW_PATH_MEDIA . DS, '', $file);
        $fileMedia = str_replace(BW_PATH_COMPONENTS . DS, '', $fileMedia);
        $fileMedia = str_replace(':', '', $fileMedia);

        // se a imagem original não existir ou for menor/igual que zero o seu tamanho
        if (!file_exists($file) || !filesize($file))
            return $file_original;

        // nome do arquivo de cache
        $cache_file_name = $sha1 . '.' . $file_type;

        //return count(explode(DS, $fileMedia));
        // se a imagem for de algum componente/media
        if (count(explode(DS, $fileMedia)) == 4) {
            // instacia com a class/core
            list($com, $media, $name, $file_nameFull) = explode(DS, $fileMedia);
            $img = bwImagem::getInstance($com, $name, bwFile::getName($file, false));

            // dados do cache
            $cache_file_url = $img->getCacheUrl() . '/' . $cache_file_name;
            $cache_file_path = $img->getCachePath() . DS . $cache_file_name;
            $cache_folder_path = $img->getCachePath();
        } else {
            // dados do cache
            $cache_folder_name = substr(sha1_file($file), 0, 10);
            $cache_folder_path = BW_PATH_CACHE . DS . 'baseweb' . DS . 'imagens' . DS . $cache_folder_name;
            $cache_file_url = BW_URL_CACHE . '/baseweb/imagens/' . $cache_folder_name . '/' . $cache_file_name;
            $cache_file_path = $cache_folder_path . DS . $cache_file_name;
        }

        // se a não existir, cria a pasta cache do arquivo
        if (!bwFolder::is($cache_folder_path))
            bwFolder::create($cache_folder_path, 0777, true);

        // se o arquivo cache não existir
        if (!bwFile::exists($cache_file_path)) {
            bwLoader::import('wideimage.WideImage');

            $img = WideImage::load($file);

            $wn = $img->getWidth();
            $hn = $img->getHeight();

            if (is_null($w) && is_null($h)) {
                $w = $wn;
                $h = $hn;
            }

            if ($w > $wn)
                $w = $wn;

            if ($h > $hn)
                $h = $hn;

            // redimenciona a imagem
            $img = $img->resize($w, $h, $f, $s);

            // verifica se existe uma regra custimizada para esta imagem
            if ($rule !== false) {
                $rule_class = 'bwPluginResizeImageRule' . ucfirst(strtolower($rule));
                $rule_file = bwTemplate::getInstance()->getPath() . DS . 'rules' . DS . 'resize-image' . DS . strtolower($rule) . '.php';
                if (bwFile::exists($rule_file)) {
                    require_once($rule_file);
                    $rule_object = new $rule_class();
                    $img = $rule_object->exec($img, $w, $h, $rule_params);
                }
                else
                    bwError::show("O arquivo $rule_file não foi encontrado!", 'Error class: bwPluginResizeImageRule');
            }

            // save a imagem
            $img->saveToFile($cache_file_path, 90);
        }

        if (bwCore::getConfig()->getValue('core.cache.resizeimage'))
            bwCache::set($sha1, $cache_file_url);

        return $cache_file_url;
    }

}

?>
