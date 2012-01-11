<?php

defined('BW') or die("Acesso negado!");

/*
 * Class/Core para controle/administração das imagens dos
 * componetes. A ideia é centralizar todas as imagens e
 * caches de imagens numa único lugar.
 *
 * Todos os arquivos de imagens enviados(updados) ao servidor,
 * deverão estar com a seguinte estrutura de pastas:
 * 
 * /[componente]/media/[subpasta]/[id].jpg
 * /cache/[componente]/[subpasta]/[id]/files.jpg
 * 
 */

class bwImagem extends bwObject
{
    // basico
    private $_com = null;
    private $_sub = null;
    private $_id = null;
    private $_ext = '.jpg';
    // url
    private $_url = null;
    private $_path = null;
    private $_urlFolder = null;
    private $_pathFolder = null;
    // cache
    private $_urlCacheFolder = null;
    private $_pathCacheFolder = null;
    // 404
    private $_erro404 = false;

    public function __construct($com, $sub, $id)
    {
        parent::__construct();

        // configura as variaveis inicias
        $this->_com = $com;
        $this->_sub = $sub;
        $this->_id = $id;

        // configura caminhos
        $this->configurarCaminhos($com, $sub, $id);

        // se não existir a imagem, set 404
        $this->_checkExists();
    }

    public function configurarCaminhos($com, $sub, $id)
    {
        // path arquivo original
        $this->_pathFolder = BW_PATH_COMPONENTS . DS . $com . DS . 'media' . DS . $sub;
        $this->_path = $this->_pathFolder . DS . $id . $this->_ext;

        // url arquivo original
        $this->_urlFolder = BW_URL_COMPONENTS . '/' . $com . '/media/' . $sub;
        $this->_url = $this->_urlFolder . '/' . $id . $this->_ext;

        // path arquivo cache
        $this->_pathCacheFolder = BW_PATH_CACHE . DS . $com . DS . $sub . DS . $id;
        $this->_urlCacheFolder = BW_URL_CACHE . '/' . $com . '/' . $sub . '/' . $id;
    }

    function getInstance404()
    {
        $img = bwImagem::getInstance('', '', 0);
        return $img;
    }

    private function _checkExists()
    {
        if (!bwFile::exists($this->getPath()))
        {
            $this->_erro404 = true;
            $this->configurarCaminhos('sistema', 'imagens', 404);
        }
    }

    function isError404()
    {
        return $this->_erro404;
    }

    function getInstance($com, $sub, $id)
    {
        $className = "bwImagem_" . substr(sha1("$com/$sub/$id"), 0, 10);

        if (is_object(bwObject::$_instances[$className]))
            $instance = bwObject::$_instances[$className];
        else
            $instance = bwObject::$_instances[$className] = new bwImagem($com, $sub, $id);

        return $instance;
    }


    function getUrlResize($params)
    {
        parse_str($params, $dados);
        $dados = array_merge(array(
            'height' => null,
            'width' => null,
            'fit' => 'inside',
            'scale' => 'any',
        ), $dados);

        $tag = sprintf("[image src='%s' height='%s' width='%s' fit='%s' scale='%s']",
                $this->getUrl(),
                $dados['height'],
                $dados['width'],
                $dados['fit'],
                $dados['scale']
        );

        return bwUtil::resizeImage($tag);
    }

    function getUrl()
    {
        return $this->_url;
    }

    function getUrlFolder()
    {
        return $this->_urlFolder;
    }

    function getPath()
    {
        return $this->_path;
    }

    function getPathFolder()
    {
        return $this->_pathFolder;
    }

    function getUrlCacheFolder()
    {
        return $this->_urlCacheFolder;
    }

    function getPathCacheFolder()
    {
        return $this->_pathCacheFolder;
    }

    function upload($nameInputFile = 'file', $ext = '/jpg/')
    {
        // remove o erro
        $this->_erro404 = false;

        // configura/reseta com caminho orginais
        $this->configurarCaminhos($this->_com, $this->_sub, $this->_id);
        
        // verifica se existe novo upload
        if(!$_FILES[$nameInputFile]['size'] > 0)
            return;

        // apaga o arquivo anterior e o cache
        $this->remover();

        // envia o arquivo
        bwFile::upload($nameInputFile, $this->getPath(), array('ext.permitidas' => $ext));

    }

    function remover()
    {
        // verifica se a imagem e existe e se não é a imagem 404
        if(bwFile::exists($this->getPath()) && !$this->isError404())
            bwFile::remove($this->getPath());

        // limpar cache
        $this->limparCache();
    }

    function limparCache()
    {
        // remove pasta cache do arquivo
        bwFolder::remove($this->getPathCacheFolder());

        // limpa o cache da sessão
        bwSession::set('resizeimage', false, 'cache');
    }

}
?>
