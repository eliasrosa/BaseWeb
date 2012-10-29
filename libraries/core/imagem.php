<?php

defined('BW') or die("Acesso negado!");

class bwImagem
{

    private
        $_id,
        $_name,
        $_component,
        $_file_url,
        $_file_path,
        $_cache_url,
        $_cache_path,
        $_folder_url,
        $_folder_path,
        $_is404;

    public function __construct($component, $name, $id)
    {
        // configura as variaveis iniciais
        $this->_component = $component;
        $this->_name = $name;
        $this->_id = $id;

        //
        $this->setPath();

        // exists?
        $this->isError404();
    }

    function getInstance($component, $name, $id)
    {
        $class = "bw.imagem." . substr(sha1("$component, $name, $id"), 0, 10);

        if (is_object(bwObject::$_instances[$class])) {
            $instance = bwObject::$_instances[$class];
        } else {
            $instance = new bwImagem($component, $name, $id);
            bwObject::$_instances[$class] = $instance;
        }

        return $instance;
    }

    public function __toString()
    {
        return $this->_file_url;
    }

    public function setPath()
    {
        $this->_is404 = false;

        $folder_url = BW_URL_COMPONENTS . '/' . $this->_component . '/media';
        $folder_path = BW_PATH_COMPONENTS . DS . $this->_component . DS . 'media';
        $cache_url = BW_URL_CACHE . '/' . $this->_component;
        $cache_path = BW_PATH_CACHE . DS . $this->_component;

        $this->_folder_url = $folder_url . '/' . $this->_name;
        $this->_folder_path = $folder_path . DS . $this->_name;
        $this->_cache_url = $cache_url . '/' . $this->_name . '/' . $this->_id;
        $this->_cache_path = $cache_path . DS . $this->_name . DS . $this->_id;
        $this->_file_url = $this->_folder_url . '/' . $this->_id . '.jpg';
        $this->_file_path = $this->_folder_path . DS . $this->_id . '.jpg';
    }

    function isError404()
    {
        if (bwFile::exists($this->_file_path) && $this->_is404 == false) {
            $this->_is404 = false;
        } else {
            $this->_folder_url = BW_URL_MEDIA;
            $this->_folder_path = BW_PATH_MEDIA;
            $this->_folder_cache = '';
            $this->_file_url = $this->_folder_url . '/404.jpg';
            $this->_file_path = $this->_folder_path . DS . '404.jpg';
            $this->_is404 = true;
        }

        return $this->_is404;
    }

    function getComponent()
    {
        return $this->_component;
    }

    function getUrl()
    {
        return $this->_file_url;
    }

    function getFolderUrl()
    {
        return $this->_folder_url;
    }

    function getCacheUrl()
    {
        return $this->_cache_url;
    }

    function getPath()
    {
        return $this->_file_path;
    }

    function getFolderPath()
    {
        return $this->_folder_path;
    }

    function getCachePath()
    {
        return $this->_cache_path;
    }

    function resize($w, $h, $f = 'inside', $s = 'any', $rule = false,
        $rule_params = NULL)
    {
        bwLoader::import('plugins.resizeimage.helper');
        return bwPluginResizeImageHelper::resize($this->_file_url, $w, $h, $f, $s, $rule, $rule_params);
    }

    function upload()
    {
        //
        $name = $this->_component . '-' . $this->_name;

        if (!$_FILES[$name]['size'] > 0) {
            return;
        }

        // reset original path
        $this->setPath();

        // validate
        $r = array(
            'retorno' => true,
            'msg' => "Imagem inválida!\n\n"
        );

        // somente jpg
        if ($_FILES[$name]['type'] != 'image/jpeg') {
            $r['retorno'] = false;
            $r['msg'] .= "-Somente imagens .JPG\n";
        }

        // resolução
        bwLoader::import('wideimage.WideImage');
        $img = WideImage::load($_FILES[$name]['tmp_name']);
        if ($img->getWidth() > 2000 || $img->getHeight() > 2000) {
            $r['retorno'] = false;
            $r['msg'] .= "- Resolução da imagem é muito grande!\n";
        }

        // retorno
        if ($r['retorno'] == false) {
            die(json_encode($r));
        }

        // apaga o arquivo anterior e o cache
        $this->remove();

        // envia o arquivo
        bwFile::upload($name, $this->getPath());
    }

    function remove()
    {
        // verifica se a imagem e existe e se não é a imagem 404
        if (bwFile::exists($this->_file_path) && !$this->isError404())
            bwFile::remove($this->_file_path);

        // limpar cache
        $this->clearCache();
    }

    function clearCache()
    {
        // remove pasta cache do arquivo
        bwFolder::remove($this->_cache_path);

        // limpa o cache
        bwCache::destroy();
    }

}
