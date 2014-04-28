<?php

defined('BW') or die("Acesso negado!");

class bwForm {

    // dados
    var $html;
    var $attrName = 'dados[%s]';
    var $formAttr = array();
    var $blockEdit = false;
    var $isEdit = false;
    private $_primary = null;

    function __construct($db = false, $action = '', $method = 'post', $blockEdit = false, $primary = 'id') {
        $method = ($method != 'get') ? 'post' : 'get';

        $this->formAttr['action'] = bwRouter::_($action);
        $this->formAttr['method'] = $method;
        $this->formAttr['id'] = 'bwForm_' . rand();

        $url = new bwUrl();

        if ($action == '')
            $this->formAttr['action'] = $url->toString();

        // banco de dados
        $this->db = $db;

        // campo primary
        $this->_primary = $primary;

        //
        if ($db->$primary) {
            // nome da class (component)
            $this->componentName = $db->getTable()->getComponentName();

            // is open
            $this->isEdit = true;
        }

        // add hidden comuns
        $this->addToken();

        if ($blockEdit && $this->isEdit)
            $this->blockEdit = true;
    }

    function createAttrs($attrs) {
        $h = '';
        foreach ($attrs as $k => $v) {
            if ($v != '' && preg_match('/class|title|value|id|rel|style|width|height/', $k))
                $h .= " {$k}=\"$v\"";
        }

        return $h;
    }

    function createTemplate($params = array(), $campo = '') {
        // imput hydden
        if (!$params['template']) {
            $this->html .= $campo;
            return $this;
        }

        // edit
        if ($this->blockEdit || (!$params['edit'] && $this->isEdit)) {
            $campo = "<span class=\"input\">{$params['value']}</span>";
        }

        $this->html .= "
            <div class=\"campo block {$params['campoName']}\">
                <span>{$params['label']}</span>
                {$params['preInput']}
                {$campo}
                {$params['posInput']}
                <br class=\"clearfix\"/>
            </div>
        ";

        return $this;
    }

    function configureParams($name, $type, $attr = array()) {
        $config = array(
            'campoName' => $name,
            'radio' => array(),
            'select' => array(),
            'name' => $name,
            'class' => 'w100',
            'findDB' => true,
            'title' => '',
            'value' => '',
            'id' => '',
            'rel' => '',
            'style' => '',
            'edit' => true,
            'template' => true,
            'dateVerify' => false,
            'width' => '',
            'height' => '',
            'limit' => false,
            'posInput' => '',
            'preInput' => '',
        );

        $config = array_merge($config, $attr);

        // ajuste DB
        if ($config['findDB']) {
            $config['name'] = sprintf($this->attrName, $name);

            if (!isset($config['label']))
                $config['label'] = $this->db->labels[$name] . ':';

            if ($this->isEdit)
                $config['value'] = htmlspecialchars($this->db->$name);
        }

        // verifica data
        if ($config['dateVerify']) {
            $config['value'] = bwUtil::data($config['value']);
        }

        if (!isset($config['label']))
            $config['label'] = '';

        // ajute title
        $config['title'] = ($config['title']) ? $config['title'] : $config['label'];

        // limit
        if ($config['limit'] && preg_match('#text|textarea#', $type)) {
            $config['class'] = ($config['class']) ? $config['class'] . ' limitText' : 'limitText';

            $config['rel'] = $config['limit'];
            $config['posInput'] = "
                <span class=\"limitResult\"></span>
                <script type=\"text/javascript\">
                $(function(){
                    var f = $('#{$this->formAttr['id']}');
                    var c = $('.{$config['campoName']} .limitText', f);
                    
                    c.keyup(function(){
                        var t = \$(this);
                        var text = t.val();
                        var limit = t.attr('rel');
                        
                        if(limit < text.length){
                            var v = text.substr(0, limit);
                            t.val(v);
                                
                            return false;
                        }
                                                                
                        $('span.limitResult', $('.{$config['campoName']}', f)).html('Limite: '+limit+' - Resta: '+ (limit - text.length) );
                        return true;
                    }).trigger('keyup');
                });
                </script>           
            ";
        }

        // get value
        $valueGet = bwRequest::getVar($config['campoName'], false, 'get');
        if ($config['value'] == '' && $valueGet !== false) {
            $config['value'] = $valueGet;
        }

        return $config;
    }

    function addInput($name, $type = 'text', $attr = array()) {
        //radio|button|checkbox|hidden|password|reset|submit|text
        if (preg_match('/radio|hidden|text|select|password|imagem|file|textarea|EditorHTML/', $type)) {
            $params = $this->configureParams($name, $type, $attr);
            $attr = $this->createAttrs($params);
            $input = '';

            if (preg_match('/hidden|^text$/', $type)) {
                $input = "<input type=\"{$type}\" name=\"{$params['name']}\" value=\"{$params['value']}\"{$attr}/>\n";
            } elseif (preg_match('/password/', $type)) {
                $input = "<input type=\"{$type}\" name=\"{$params['name']}\" value=\"\"{$attr}/>\n";
            } elseif ($type == 'imagem') {
                $rand = rand();
                $input = "<img src=\"{$params['resizeImage']}?&rand={$rand}\"{$attr}/>\n";

                if ($params['linkFile'])
                    $input = "<a href=\"{$params['src']}\">{$input}</a>\n";
            }


            elseif ($type == 'file') {
                $input = "<input type=\"{$type}\" name=\"{$params['name']}\" />\n";
            } elseif ($type == 'radio') {
                foreach ($params['radio']['opcoes'] as $value => $label) {
                    $checked = ($value == $params['value']) ? 'checked="checked"' : '';
                    $class = ($params['radio']['class'] != '') ? " class=\"{$params['radio']['class']}\"" : '';

                    $input .= "<label{$class}>
                                    <input type=\"radio\" name=\"{$params['name']}\" value=\"{$value}\" {$checked}{$attr}/>
                                    {$label}
                             </label>\n";
                }
            } elseif ($type == 'select') {
                $input = "<select name=\"{$params['name']}\"{$attr}>\n";
                $input .= "<option value=\"\">-- Selecione --</option>\n";

                foreach ($params['opcoes'] as $value => $label) {
                    $selected = '';
                    if ($value == $params['value']) {
                        $params['value'] = $label;
                        $selected = 'selected="selected"';
                    }


                    $input .= "<option value=\"{$value}\" {$selected}>{$label}</option>\n";
                }
                $input .= "</select>\n";
            } elseif ($type == 'textarea') {
                if ($params['autoresize']) {
                    bwHtml::js(BW_URL_JAVASCRIPTS . '/autoresize.js');
                    bwHtml::js(BW_URL_JAVASCRIPTS . '/autoresize.textarea.js');
                }

                $input = "<textarea name=\"{$params['name']}\"{$attr} wrap=\"off\">{$params['value']}</textarea>\n";
            } elseif ($type == 'EditorHTML') {
                $input = "<textarea name=\"{$params['name']}\"{$attr}>{$params['value']}</textarea>\n";
            }

            $this->createTemplate($params, $input);
            return $this;
        } else
            die("Tipo inválido!");
    }

    function addInputFile($name = 'file', $label = 'Arquivo:', $attr = array()) {
        $params = array_merge(array(
            'findDB' => false,
            'name' => $name,
            'label' => $label,
                ), $attr);
        $this->addInput($name, 'file', $params);

        return $this;
    }

    function addTextArea($name, $attr = array()) {
        $params = array_merge(array(
            'autoresize' => true,
            'class' => 'w100'
                ), $attr);

        if ($params['autoresize']) {
            $params['class'] = isset($params['class']) ? "{$params['class']} autoresize" : "autoresize";
        }

        $this->addInput($name, 'textarea', $params);

        return $this;
    }

    function addEditorHTML($name) {
        bwHtml::js(BW_URL_JAVASCRIPTS . '/tiny_mce/jquery.tinymce.js');

        $class = 'editor_' . rand();

        $config = new bwConfigDB();
        $json = $config->getValue('plugins.tinymce.parametros');

        $this->html .= "
            <script type=\"text/javascript\">
                $(function() {
                
                    $('textarea.$class').tinymce($.extend($json, { 
                        script_url : '" . BW_URL_JAVASCRIPTS . "/tiny_mce/tiny_mce.js',
                        mode: 'exact',
                        theme: 'advanced'
                    }));

                });
            </script>";

        $this->addInput($name, 'EditorHTML', array(
            'autoresize' => false,
            'class' => "editorHTML $class"
                //'template' => false
        ));

        return $this;
    }

    function addImg($name, $attr = array()) {
        $params = array_merge(array(
            'linkFile' => true,
            'width' => '320',
            'height' => '320',
            'src' => '',
            'resizeImage' => '',
                ), $attr);

        $params['resizeImage'] = bwUtil::resizeImage("[image src='{$params['src']}' width='{$params['width']}' height='{$params['width']}']");
        unset($params['width'], $params['height']);

        // remove a class
        $params['class'] = '';

        $this->addInput($name, 'imagem', $params);
        return $this;
    }

    function addCustonFile($file, $vars = array()) {
        $com = bwRequest::getVar('com');

        $vars = array_merge(array(
            'i' => $this->db,
            'form' => $this,
                ), $vars);


        $this->html .= bwUtil::execPHP($file, $vars);

        return $this;
    }

    function addInputFileImg($name = 'default', $label = 'Imagem') {
        $name_input = $this->db->getComponentName() . '-imagem-' . $name;
        $params = array();

        if ($this->isEdit) {

            if (!isset($this->db->bwImagem->$name)) {
                return;
            }

            $img = $this->db->bwImagem->$name;
            $html = '<p style="display: block; margin: 5px 0 5px 155px;">';

            if (!$img->isError404()) {

                $html .= sprintf('<img src="%s" /><br/>', $img->resize(320, 320));

                $html .= sprintf(
                        'Remover imagem acima ao salvar: <input type="checkbox" name="remover-bwimagem[]" value="%s"/><br/>', $name_input
                );
            }

            $html .= sprintf(
                    'Tamanho máximo permitido pelo servidor: %s<br/>Tamanho máximo recomendado: 1M<br/>'
                    , ini_get('upload_max_filesize')
            );

            if (!$img->isError404()) {
                $html .= sprintf(
                        '<a href="%s" target="_blank">Visualizar imagem original</a><br/>'
                        , $img->getUrl()
                );
            }

            $html .= '</p>';

            $params['posInput'] = $html;
        }

        $this->addInputFile($name_input, $label . ':', $params);

        return $this;
    }

    function addH2($text, $class = '') {
        $this->html .= "<h2 class=\"{$class}\">{$text}</h2>";

        return $this;
    }

    function addHTML($html) {
        $this->html .= $html;

        return $this;
    }

    function addHR($class = 'botton') {
        $this->html .= "<hr class=\"{$class}\" />";

        return $this;
    }

    function addBottonSalvar($task, $label = 'Salvar', $class = '') {
        if (!$this->blockEdit) {
            $id = bwbutton::getId();

            $this->addTask();

            $this->html .= "
                <a class=\"button submit salvar {$class}\" id=\"{$id}\">{$label}</a>
                <script type=\"text/javascript\">
                    $(function() {
                        var form = $('#{$this->formAttr['id']}');

                        $('#{$id}').click(function(){                                                   
                            $('.task:input', form).val('{$task}');
                            form.submit();
                            $('.clearValOnSubimit', form).val('');
                        }).button();

                    });
                </script>\n";
        }

        return $this;
    }

    function addSubmit($label = 'Salvar', $class = '') {
        if (!$this->blockEdit) {
            $class = $class == '' ? '' : ' ' . $class;
            $this->html .= sprintf('<input value="%s" class="submit%s" type="submit" />', $label, $class);
        }

        return $this;
    }

    function addStatus($name = 'status', $attr = array()) {
        $params = array_merge(array(
            'label' => 'Status:',
                ), $attr);

        $this->addInputRadio($name, array(
            '1' => 'Ativado',
            '0' => 'Desativado'
                ), $params);

        return $this;
    }

    function addBoolean($name) {
        $this->addInputRadio($name, array(
            '1' => 'Sim',
            '0' => 'Não'
        ));

        return $this;
    }

    function addInputRadio($name, $opcoes = array(), $attr = array()) {
        $params = array_merge(array(
            'radio' => array(
                'opcoes' => $opcoes,
                'class' => '',
            ),
            'class' => 'radio'
                ), $attr);

        $this->addInput($name, 'radio', $params);

        return $this;
    }

    function addInputInteger($name, $attr = array()) {
        $params = array_merge(array(
            'rel' => 'int',
            'value' => '0',
            'class' => 'w30',
                ), $attr);

        $this->addInput($name, 'text', $params);

        return $this;
    }

    function addInputMoeda($name, $attr = array()) {
        $params = array_merge(array(
            'rel' => 'moeda',
            'value' => '0,00',
            'class' => 'w30',
                ), $attr);

        $this->addInput($name, 'text', $params);

        return $this;
    }

    function addInputDataHora($name, $attr = array()) {
        $params = array_merge(array(
            'class' => 'w30',
            'rel' => 'datetime',
            'dateVerify' => true,
                ), $attr);

        $this->addInput($name, 'text', $params);

        return $this;
    }

    function addInputData($name, $attr = array()) {
        $params = array_merge(array(
            'rel' => 'date',
            'class' => 'w30',
            'dateVerify' => true,
                ), $attr);

        $this->addInput($name, 'text', $params);

        return $this;
    }

    function addSelect($name, $opcoes = array(), $attr = array()) {
        $params = array_merge(array(
            'opcoes' => $opcoes,
            'style' => 'width: 358px;',
                ), $attr);

        $this->addInput($name, 'select', $params);

        return $this;
    }

    function addSelectDB($name, $tabela, $attr = array()) {

        $params = array_merge(array(
            'opcoes' => array(),
            'order' => '',
            'db.key' => 'id',
            'db.value' => 'nome',
            'where' => '',
                ), $attr);

        $order = $params['order'] != '' ? $params['order'] : "{$params['db.value']} ASC";

        $query = Doctrine_Query::create()
                ->from($tabela);

        if ($params['where'] != '')
            $query->where($params['where']);


        $query = $query
                ->orderBy($order)
                ->execute();

        $key = $params['db.key'];
        $val = $params['db.value'];

        foreach ($query as $db) {
            $value = $db->$key;
            $label = $db->$val;

            $params['opcoes'][$value] = $label;
        }

        $this->addInput($name, 'select', $params);

        return $this;
    }

    function addInputsPassword($name = 'senha') {
        bwHtml::js(BW_URL_JAVASCRIPTS . '/passwordStrengthMeter.js');

        $this->html .= "
            <script type=\"text/javascript\">
                $(function() {
                    $('.{$name} :input').keyup(function(){
                        var p = passwordStrengthMeter($(this).val());
                        $('.{$name} span.pass').html(p);

                        $('.{$name}2 :input').val('');
                        $('.{$name}2 span.pass').removeClass('green').removeClass('red').html('');
                    });

                    $('.{$name}2 :input').keyup(function(){
                        if($(this).val() == $('.{$name} :input').val())
                            $('.{$name}2 span.pass').addClass('green').removeClass('red').html('Ok!');
                        else
                            $('.{$name}2 span.pass').addClass('red').removeClass('green').html('As senhas não estão iguais!');
                    });
                    
                });
            </script>";

        $this->addInput($name, 'password', array(
            'findDB' => false,
            'label' => 'Senha:',
            'class' => 'w60 clearValOnSubimit',
            'posInput' => "<span class=\"pass\"></span>"
        ));


        $this->addInput($name . '2', 'password', array(
            'findDB' => false,
            'class' => 'w60 clearValOnSubimit',
            'label' => 'Repita novamente:',
            'posInput' => "<span class=\"pass\"></span>"
        ));

        return $this;
    }

    function addInputID() {
        if ($this->isEdit)
            $this->addHidden($this->_primary);

        $this->addInput($this->_primary, 'hidden', array(
            'edit' => false,
            'label' => 'ID:',
        ));

        return $this;
    }

    function addSeo() {
        $this->addH2('SEO - Otimização para motores de busca' . $info);

        //$this->addInput('metatagalias', 'text', array(
        //    'label' => 'Alias:'
        //));

        $this->addInput('metatagkeywords', 'text', array(
            'label' => 'Metatag (keywords):'
        ));

        $this->addTextArea('metatagdescription', array(
            'limit' => '160',
            'label' => 'Metatag (description):'
        ));

        return $this;
    }

    function addHidden($name, $class = '', $params = array()) {
        $params = array_merge(array(
            'template' => false,
            'class' => $class
                ), $params);

        $this->addInput($name, 'hidden', $params);

        return $this;
    }

    function addToken() {
        $this->addInput(bwRequest::getToken(), 'hidden', array(
            'template' => false,
            'class' => 'token',
            'findDB' => false,
            'value' => '1',
        ));

        return $this;
    }

    function addTask($task = 'null') {
        $this->addInput('task', 'hidden', array(
            'template' => false,
            'class' => 'task',
            'findDB' => false,
            'value' => $task,
        ));

        return $this;
    }

    function show($returnHtml = false) {
        $attr = '';
        foreach ($this->formAttr as $a => $v) {
            $attr .= " {$a}=\"{$v}\"";
        }

        $this->html = "<form class=\"validaForm\"{$attr}>"
                . "{$this->html}"
                . "</form>"
                . "<br class=\"clearfix\" />";

        $this->createValidaForm();

        if ($returnHtml)
            return $this->html;
        else
            echo $this->html;
    }

    public function addBottonRemover($task = 'remover', $nome = 'Remover') {
        if ($this->isEdit && !$this->blockEdit) {
            $id = bwbutton::getId();

            $this->addTask();

            $this->html .= "
                <a class=\"button remover\" id=\"{$id}\">{$nome}</a>
                <script type=\"text/javascript\">
                    $(function() {
                        $('#{$id}').click(function()
                        {
                            if(confirm('Tem certeza que deseja REMOVER esse(s) registro(s)?'))
                            {
                                if(confirm('Esse procedimento não poderá ser desfeito, deseja continuar?'))
                                {
                                    var form = $('#{$this->formAttr['id']}');           
                                    $('.task:input', form).val('{$task}');
                                    
                                    $(form).trigger('validaFormDisable').submit();
                                }                               
                                
                            }
                        }).button();
                    });
                </script>\n";
        }

        return $this;
    }

    public function addBottonRedirect($label, $url) {
        $id = bwbutton::getId();

        $this->html .= "
            <a class=\"button redirect\" id=\"{$id}\">{$label}</a>
            <script type=\"text/javascript\">
                    $(function() {
                        $('#{$id}').click(function(){
                            window.location = '{$url}';
                        }).button();
                    });
                </script>";

        return $this;
    }

    function createValidaForm() {
        $this->html .= "
            <script type=\"text/javascript\">       
                $(function() {
              
                    var urlAtual = '{$this->formAttr['action']}'
                    var form = $('#{$this->formAttr['id']}');
                    $(form).validaForm({
                        upload: true,
                        success: function(retorno){
                            
                            //alert(retorno);
                        
                            var json = eval('('+ retorno +')');
                            alert(json.msg);
                            
                            if(json.retorno){
                                if(json.redirect)
                                    window.location = json.redirect;
                                else
                                    window.location.reload();
                            }                           
                            
                            $('.campo p.erro', form).remove();
                            $('.campo.erro', form).removeClass('erro');
                            
                            $.each(json.camposErros, function(campo, erros){
                                $.each(erros, function(a, msg){
                                    $('.campo.'+campo, form)
                                        .addClass('erro')
                                        .append('<p class=\"erro\">'+ msg +'</p>');
                                });
                            });
                            
                            // seleciona o 1 item com erro
                            $('.campo.erro :input:first', form).focus();
                        }
                    });
                });
            </script>\n";

        return $this;
    }

}

?>
