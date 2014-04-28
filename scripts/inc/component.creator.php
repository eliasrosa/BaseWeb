<?php

bwLoader::import('spyc-yaml.Spyc');

class ComponentCreator {

    private $file_config_path = 'config.yml';
    private $path_script = null;
    private $path_folder = null;
    private $path_folder_adm_views = null;
    private $path_file_sql = null;
    private $path_file_menu = null;
    private $config = array();
    private $models = array();
    private $routers = array();
    private $images = array();
    private $galleries = array();
    private $relationships = array();

    function __construct() {

        //
        $this->loadConfig();

        //
        $this->fixConfig();

        //
        //print_r($this->config);
    }

    private function loadConfig() {

        //
        $this->config = Spyc::YAMLLoad($this->file_config_path);

        //
        $this->path_script = BW_PATH_SCRIPTS . DS . 'inc' . DS . 'component';
        $this->path_folder = BW_PATH_COMPONENTS . DS . $this->config['component']['folder'];
        $this->path_folder_adm_views = $this->path_folder . DS . 'adm' . DS . 'views';
        $this->path_file_sql = $this->path_folder . DS . 'sql' . DS . '1.sql.php';
        $this->path_file_menu = $this->path_folder . DS . 'adm' . DS . 'menu.php';
        $this->path_file_router = $this->path_folder . DS . 'router.php';

        return;
    }

    //
    private function fixConfig() {

        $alias = array(
            'id' => array(
                'type' => 'int',
                'length' => 11,
                'primary' => true,
                'auto_incriment' => true,
                'label' => 'ID',
                'null' => false
            ),
            'html' => array(
                'type' => 'longtext',
                'form' => 'addEditorHTML'
            ),
            'moeda' => array(
                'type' => 'float',
                'form' => 'addInputMoeda'
            ),
            'password' => array(
                'type' => 'text',
                'label' => 'Senha',
                'form' => 'addInputsPassword'
            ),
            'status' => array(
                'type' => 'boolean',
                'label' => 'Status',
                'null' => false,
                'form' => 'addStatus'
            )
        );

        //
        foreach ($this->config['tables'] as $table_name => $columns) {
            foreach ($columns as $col_name => $col_value) {

                $type = $col_value['type'];
                if (isset($col_value['type']) && isset($alias[$type])) {

                    $alias[$type]['alias'] = $type;
                    $new_opt = array_merge($alias[$type], $col_value);
                    $new_opt['type'] = $alias[$type]['type'];
                    $this->config['tables'][$table_name][$col_name] = $new_opt;
                }

                // label
                $label = $this->config['tables'][$table_name][$col_name]['label'];
                if ($label == '') {
                    $label = ucfirst(strtolower($col_name));
                    $this->config['tables'][$table_name][$col_name]['label'] = $label;
                }

                if ($col_name === 'bwSetup') {
                    //
                    $this->relationships[$table_name] = $col_value['relationships'];
                    $this->images[$table_name] = $col_value['images'];
                    $this->galleries[$table_name] = $col_value['galleries'];


                    //
                    unset($this->config['tables'][$table_name][$col_name]);
                }
            }
        }

        return;
    }

    //
    function createFile($file, $conteudo) {
        $fp = fopen($file, "w+");
        $w = fwrite($fp, $conteudo);
        fclose($fp);

        return;
    }

    private function replaceContentFile($file, $replace_vars, $symbol_left = '%', $symbol_right = '%') {

        $content = bwFile::getConteudo($file);

        foreach ($replace_vars as $k => $v) {
            $content = str_replace($symbol_left . $k . $symbol_right, $v, $content);
        }

        $this->createFile($file, $content);
    }

    public function create() {

        //
        bwFolder::xcopy($this->path_script, $this->path_folder, 0755, $log);
        foreach ($log as $f) {
            console_log('Criado ' . $f);
        }

        //
        $this->replaceContentFile($this->path_folder . DS . 'router.php', $this->config['component']);
        $this->replaceContentFile($this->path_folder . DS . 'api.php', $this->config['component']);
        $this->replaceContentFile($this->path_folder . DS . 'adm' . DS . 'views' . DS . 'index.php', $this->config['component']);

        //
        return;
    }

    public function createSql() {

        //
        $sql = "-- <?php defined('BW') or die('Acesso negado!'); ?>\n\n";
        $sql .= "\n--\n";
        $sql .= "ALTER TABLE `bw_versao` ADD `com_" . $this->config['component']['folder'] . "_1` INT(1) NOT NULL;\n\n";

        //
        foreach ($this->config['tables'] as $table_name => $columns) {

            //
            $cols = array();
            $primary_keys = array();
            $primary_sql = null;
            $table_name = 'bw_' . $table_name;

            //
            foreach ($columns as $col_name => $col_opt) {

                //
                $col = '  `' . $col_name . '`';
                $type = $col_opt['type'];
                $length = $col_opt['length'];

                // tipos	
                if (preg_match('/int|integer/', $type)) {
                    $col .= ' int(' . (($length) ? $length : '11') . ')';
                } elseif (preg_match('/text|varchar|string/', $type)) {
                    $col .= ' varchar(' . (($length) ? $length : '255') . ')';
                } elseif (preg_match('/decimal/', $type)) {
                    $col .= ' decimal(' . (($length) ? $length : '10,2') . ')';
                } elseif (preg_match('/float/', $type)) {
                    $col .= ' float';
                } elseif (preg_match('/longtext/', $type)) {
                    $col .= ' longtext';
                } elseif (preg_match('/datetime/', $type)) {
                    $col .= ' datetime';
                } elseif (preg_match('/boolean|bool/', $type)) {
                    $col .= ' boolean';
                } elseif (preg_match('/date/', $type)) {
                    $col .= ' date';
                } else {
                    console_log("ERRO: Tipo de coluna não encontrado");
                    console_log("TIPOS: int|integer|text|varchar|decimal|float|longtext|datetime|date|boolean\n");
                    console_log('ERROR_TABLE: ' . $table_name);
                    console_log('ERROR_COLUNA: ' . $col_name);
                    die();
                }

                // is null || not null
                $col .= ($col_opt['null']) ? ' NULL' : ' NOT NULL';

                // auto_increment
                $col .= ($col_opt['auto_incriment']) ? ' AUTO_INCREMENT' : '';

                // primary_keys
                if ($col_opt['primary']) {
                    $primary_keys[] = '`' . $col_name . '`';
                    $primary_sql = "  PRIMARY KEY (" . join(',', $primary_keys) . ")";
                }

                //
                $cols[] = $col;
            }

            //
            $sql .= "\n";
            $sql .= "--\n";
            $sql .= "-- Criando tabela " . $table_name . " \n";
            $sql .= "--\n\n";
            $sql .= "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (\n";

            // primary_keys
            if (!is_null($primary_sql)) {
                $cols[] = $primary_sql;
            }

            $sql .= join(",\n", $cols) . "\n";
            $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;\n\n\n\n";
        }

        //
        $this->createFile($this->path_file_sql, $sql);

        //
        //console_log($sql);
        console_log("Criado " . $this->path_file_sql);
    }

    public function createModels() {

        //
        $model_path = $this->path_folder . DS . 'models';

        //
        try {

            // limpa a pasta
            foreach (bwFolder::listarConteudo($model_path, true, false) as $file) {
                if (bwFile::exists($model_path . DS . $file) && $file != 'index.php') {
                    bwFile::remove($model_path . DS . $file);
                }
            }

            //
            Doctrine::generateModelsFromDb(
                    $model_path, array(), array(
                'packagesPrefix' => 'Package',
                'packagesPath' => '',
                'packagesFolderName' => 'packages',
                'suffix' => '.php',
                'generateBaseClasses' => false,
                'generateTableClasses' => false,
                'generateAccessors' => false,
                'baseClassPrefix' => '',
                'baseClassName' => 'bwRecord'
                    )
            );
        } catch (Doctrine_Import_Builder_Exception $e) {
            die($e->getMessage());
        }


        //
        foreach (bwFolder::listarConteudo($model_path, true, false) as $file) {

            if ($file == 'index.php') {
                continue;
            }

            // remove Bw(.*)\.php
            $class_name = substr($file, 2, -4);


            foreach ($this->config['tables'] as $tabela => $colunas) {

                //
                if (preg_match('/^' . str_replace('_', '', $tabela) . '$/i', $class_name)) {

                    //
                    $labels = array();
                    $this->models[$tabela] = $class_name;
                    $new_file = $model_path . DS . $class_name . '.php';

                    //
                    bwFile::rename($model_path . DS . $file, $new_file);
                    $func_set = '';

                    //
                    $content = bwFile::getConteudo($new_file);

                    //
                    $content_new = "<?php\n\n";
                    $content_new .= "defined('BW') or die(\"Acesso negado!\");\n\n";
                    $content_new .= "class {$class_name} extends bwRecord\n{\n";

                    //
                    $content_new .= "    var \$labels = array(\n";

                    //
                    $code_setup = "";

                    if (isset($this->relationships[$tabela])) {
                        foreach ($this->relationships[$tabela] as $options) {


                            $alias = $options['model'];
                            if (isset($options['alias'])) {
                                $alias = "{$options['model']} as {$options['alias']}";
                            } else {
                                $options['alias'] = $options['model'];
                            }

                            $refClass = '';
                            if (isset($options['refClass'])) {
                                $refClass = "            'refClass' => '{$options['refClass']}'\n";
                            }

                            $code_setup .= "\n\n"
                                    . "        \$this->{$options['type']}('{$alias}', array(\n"
                                    . "            'local' => '{$options['local']}',\n"
                                    . "            'foreign' => '{$options['foreign']}'\n"
                                    . "{$refClass}\n"
                                    . "        ));";


                            if (isset($options['label'])) {
                                $options['label'] = $options['alias'];
                            }

                            $options['label'] = (isset($options['label'])) ? $options['alias'] : $options['label'];
                            $labels[] = "        '{$options['alias']}' => '{$options['label']}'";
                        }
                    }


                    if (isset($this->images[$tabela])) {
                        $code_setup .= "\n\n        //\n";
                        foreach ($this->images[$tabela] as $folder) {

                            //
                            $code_setup .= "        \$this->addImagem('{$folder}');";

                            //
                            $f = $this->path_folder . DS . 'media' . DS . 'imagem-' . $folder;
                            if (!bwFolder::is($f)) {
                                bwFolder::create($f);
                                console_log("Criado {$f}");

                                //
                                $file = $f . DS . 'index.php';
                                $this->createFile($file, "<?php\ndefined('BW') or die('Acesso negado!');\n");
                                console_log("Criado {$file}");
                            }
                        }
                    }

                    if (isset($this->galleries[$tabela])) {
                        $code_setup .= "\n\n        //\n";
                        foreach ($this->galleries[$tabela] as $folder) {

                            // 
                            $code_setup .= "        \$this->addGaleria('{$folder}');";

                            //
                            $f = $this->path_folder . DS . 'media' . DS . 'galeria-' . $folder;
                            if (!bwFolder::is($f)) {
                                bwFolder::create($f);
                                console_log("Criado {$f}");

                                //
                                $file = $f . DS . 'index.php';
                                $this->createFile($file, "<?php\ndefined('BW') or die('Acesso negado!');\n");
                                console_log("Criado {$file}");
                            }
                        }
                    }

                    //
                    $func_code = '';
                    foreach ($colunas as $coluna_nome => $coluna_options) {
                        $labels[] = "        '{$coluna_nome}' => '{$coluna_options['label']}'";

                        $type = $coluna_options['type'];
                        $alias = $coluna_options['alias'];
                        $func_nome = ucfirst(strtolower($coluna_nome));
                        

                        if (preg_match('/datetime/', $type)) {
                            $func_code .= "\n"
                                    . "    public function set{$func_nome}(\$v)\n"
                                    . "    {\n"
                                    . "        if (preg_match('#^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$#', \$v)){\n"
                                    . "            return \$this->_set('{$coluna_nome}', bwUtil::data(\$v));\n"
                                    . "        }\n"
                                    . "        return \$v;\n"
                                    . "    }\n";
                        } elseif (preg_match('/date/', $type)) {
                            $func_code .= "\n"
                                    . "    public function set{$func_nome}(\$v)\n"
                                    . "    {\n"
                                    . "        if (preg_match('#^\d{2}\/\d{2}\/\d{4}$#', \$v)){\n"
                                    . "            return \$this->_set('{$coluna_nome}', bwUtil::data(\$v));\n"
                                    . "        }\n"
                                    . "        return \$v;\n"
                                    . "    }\n";
                        } elseif (preg_match('/moeda/', $alias)) {
                            $func_code .= "\n"
                                    . "    public function set{$func_nome}(\$v)\n"
                                    . "    {\n"
                                    . "        \$v = floatval(str_replace(',', '.', str_replace('.', '', \$v)));\n"
                                    . "        return \$this->_set('{$coluna_nome}', \$v);\n"
                                    . "    }\n";
                        } else {
                            continue;
                        }


                    }

                    //
                    $code_setup .= "\n    }\n";


                    //
                    $content_new .= join(",\n", $labels);
                    $content_new .= "\n    );\n";

                    //
                    $content_new .= preg_replace('/^.*bwRecord..?\{/s', '', $content);
                    $content_new = trim(substr($content_new, 0, -4));
                    $content_new = $content_new . $code_setup;


                    $task = "\n"
                            . "    public function salvar(\$dados)\n"
                            . "    {\n"
                            . "        \$db = bwComponent::save('{$this->models[$tabela]}', \$dados);\n"
                            . "        \$r = bwComponent::retorno(\$db);\n"
                            . "        \n"
                            . "        return \$r;\n"
                            . "    }\n\n"
                            . "    public function remover(\$dados)\n"
                            . "    {\n"
                            . "        \$db = bwComponent::remover('{$this->models[$tabela]}', \$dados);\n"
                            . "        \$r = bwComponent::retorno(\$db);\n"
                            . "        \n"
                            . "        return \$r;\n"
                            . "    }\n";


                    $content_new = $content_new . $task . $func_code;




                    //
                    $content_new .= "\n}";

                    //
                    $this->createFile($new_file, $content_new);

                    //
                    console_log('Criado ' . $new_file);
                }
            }

            if (bwFile::exists($model_path . DS . $file) && $file != 'index.php') {
                bwFile::remove($model_path . DS . $file);
            }
        }

        return;
    }

    public function createMenu() {

        //
        $content = "<?php\n\n";
        $content .= "defined('BW') or die('Acesso negado!');\n\n";
        $content .= "\$tituloPage = 'Administração de " . $this->config['component']['titulo'] . "';\n\n";
        $content .= "\$menu = array(\n";

        $i = 1;
        $a = array();
        foreach ($this->config['tables'] as $k => $v) {

            $folder = str_replace('_', '/', $k);
            $name = ucwords(str_replace('_', ' ', $k));

            //
            $this->routers[$k] = '/' . $folder;

            //
            $t = "    '" . $i . "' => array(\n";
            $t .= "        'url' => '/" . $folder . "/lista',\n";
            $t .= "        'tit' => '" . $name . "'\n";
            $t .= "    )";
            $a[$i] = $t;

            $i++;
        }

        $content .= join(",\n", $a) . "\n);\n\n";

        //
        $this->createFile($this->path_file_menu, $content);

        //
        console_log('Criado ' . $this->path_file_menu);

        return;
    }

    public function createRouter() {

        //
        $content = "<?php\n\n";
        $content .= "defined('BW') or die('Acesso negado!');\n\n";
        $content .= "//ADM\n";
        $content .= "bwRouter::addUrl('/{$this->config['component']['folder']}');\n";


        foreach ($this->config['tables'] as $k => $v) {

            $folder = str_replace('_', '/', $k);

            //
            $content .= "bwRouter::addUrl('/{$folder}/lista');\n";
            $content .= "bwRouter::addUrl('/{$folder}/cadastro/:id', array('fields' => array('id')));\n";
            $content .= "bwRouter::addUrl('/{$folder}/task', array('type' => 'task'));\n";
        }

        //
        $this->createFile($this->path_file_router, $content);

        //
        console_log('Criado ' . $this->path_file_router);

        return;
    }

    public function createViews() {

        //
        $content = "<?php\n\n";
        $content .= "defined('BW') or die('Acesso negado!');\n\n";


        $menu_index = 0;
        foreach ($this->config['tables'] as $table => $v) {

            //
            $folder = $this->path_folder_adm_views . str_replace(DS . $this->config['component']['folder'], '', DS . str_replace('_', DS, $table));

            if (!bwFolder::is($folder)) {
                //
                bwFolder::create($folder, 0644, true);

                //
                console_log('Criado ' . $folder);
            }

            //
            $this->createViewList($folder . DS . 'lista.php', $table, $menu_index + 1);
            $this->createViewForm($folder . DS . 'cadastro.php', $table, $menu_index + 1);
            $this->createTask($folder . DS . 'task.php', $table);

            //
            $menu_index++;
        }

        return;
    }

    private function createViewList($file, $table, $menu) {

        //
        $model = $this->models[$table];

        //
        $content = "<?php\n\n";
        $content .= "defined('BW') or die('Acesso negado!');\n\n";
        $content .= "class bwGrid" . $model . " extends bwGrid\n{\n";

        $i = 0;
        foreach ($this->config['tables'][$table] as $col => $options) {
            $content .= "    function col" . $i . "(\$i)\n";
            $content .= "    {\n        ";

            if ($options['alias'] == 'id') {
                $content .= "return sprintf('"
                        . "<a href=\"%s\">%s</a>', "
                        . "\$i->getUrl('{$this->routers[$table]}/cadastro'), "
                        . "\$i->id);\n";
            } elseif ($options['type'] == 'datetime') {
                $content .= "return bwUtil::data(\$i->{$col});\n";
            } elseif ($options['type'] == 'date') {
                $content .= "return bwUtil::data(\$i->{$col});\n";
            } elseif ($options['alias'] == 'moeda') {
                $content .= "return 'R$ ' . number_format(\$i->{$col}, 2, ',', '.');\n";
            } else {
                $content .= "return \$i->{$col};\n";
            }

            $content .= "    }\n\n";

            $i++;
        }

        if (isset($this->galleries[$table])) {
            foreach ($this->galleries[$table] as $folder) {
                $content .= ""
                        . "    function col" . $i . "(\$i)\n"
                        . "    {\n"
                        . "        return '<a href=\"' .\$i->bwGaleria->getAdmUrl('{$folder}') . '\">Galeria de imagens</a>';"
                        . "    }\n\n";
                $i++;
            }
        }

        if (isset($this->images[$table])) {
            foreach ($this->images[$table] as $folder) {
                $content .= ""
                        . "    function col" . $i . "(\$i)\n"
                        . "    {\n"
                        . "        \$src = \$i->bwImagem->{$folder}->resize(100, 100);\n"
                        . "        return sprintf('<img src=\"%s\" />', \$src);\n"
                        . "    }\n\n";
                $i++;
            }
        }


        $content .= "    function __construct()\n"
                . "    {\n\n"
                . "        //\n"
                . "        \$sql = Doctrine_Query::create()\n"
                . "                ->from('" . $model . "');\n\n"
                . "        //\n"
                . "        parent::__construct(\$sql);\n\n"
                . "        //\n";

        foreach ($this->config['tables'][$table] as $col => $options) {
            $content .= "        \$this->addCol('{$options['label']}', '{$col}');\n";
        }

        if (isset($this->images[$table])) {
            foreach ($this->images[$table] as $folder) {
                $content .= "        \$this->addCol('Galeria', NULL, 'tac', 100);\n";
            }
        }

        if (isset($this->images[$table])) {
            foreach ($this->images[$table] as $folder) {
                $content .= "        \$this->addCol('Imagem', NULL, 'tac', 100);\n";
            }
        }

        $content .= "\n        //\n"
                . "        \$this->show();\n\n"
                . "    }\n\n";


        $content .= "}\n\n";
        $content .= "echo bwAdm::createHtmlSubMenu(" . $menu . ");\n";
        $content .= "echo bwButton::redirect('Criar novo', '" . $this->routers[$table] . "/cadastro/0');\n\n";
        $content .= "new bwGrid" . $model . "();\n\n";

        //
        $this->createFile($file, $content);

        //
        console_log('Criado ' . $file);
    }

    private function createViewForm($file, $table, $menu) {

        //
        $model = $this->models[$table];
        $router = $this->routers[$table];

        //
        $content = "<?php\n\n"
                . "defined('BW') or die('Acesso negado!');\n\n"
                . "echo bwAdm::createHtmlSubMenu({$menu});\n\n";


        $content .= "//\n"
                . "\$i = bwComponent::openById('{$model}', bwRequest::getInt('id', 0));\n"
                . "\$form = new bwForm(\$i, '{$router}/task');\n\n"
                . "\$form->addH2('Cadastro');\n\n"
                . "";


        foreach ($this->config['tables'][$table] as $col_name => $col_opt) {

            //
            $function_name = '';

            // tipos	
            $type = $col_opt['type'];
            $form = $col_opt['form'];
            $alias = $col_opt['alias'];

            //
            if ($form == '') {

                if ($alias == 'id') {
                    $form .= 'addInputID';
                } elseif (preg_match('/int|integer/', $type)) {
                    $form .= 'addInputInteger';
                } elseif (preg_match('/longtext/', $type)) {
                    $form .= 'addTextArea';
                } elseif (preg_match('/datetime/', $type)) {
                    $form .= 'addInputDataHora';
                } elseif (preg_match('/date/', $type)) {
                    $form .= 'addInputData';
                } elseif (preg_match('/boolean|bool/', $type)) {
                    $form .= 'addBoolean';
                } else {
                    $form .= 'addInput';
                }
            }

            //
            $content .= "\$form->{$form}('{$col_name}');\n";
        }

        if (isset($this->images[$table])) {
            foreach ($this->images[$table] as $folder) {
                $content .= "\$form->addH2('Imagem');\n";
                $content .= "\$form->addInputFileImg('{$folder}');\n";
            }
        }


        $content .= "\n\n//\n"
                . "\$form->addBottonSalvar('salvar');\n"
                . "\$form->addBottonRemover('remover');\n"
                . "\$form->show();\n";


        //
        $this->createFile($file, $content);

        //
        console_log('Criado ' . $file);
    }

    private function createTask($file, $table) {

        //
        $model = $this->models[$table];
        $router = $this->routers[$table];

        //
        $content = "<?php\n\n"
                . "defined('BW') or die('Acesso negado!');\n\n"
                . "\$task = bwRequest::getVar('task');\n\n"
                . "if (\$task == 'salvar') {\n"
                . "    \$r = {$model}::salvar(bwRequest::getVar('dados', array()));\n"
                . "}\n\n"
                . "if (\$task == 'remover'){\n"
                . "    \$r = {$model}::remover(bwRequest::getVar('dados', array()));\n"
                . "    \$r['redirect'] = bwRouter::_('{$router}/lista');\n"
                . "}\n"
                . "\n"
                . "die(json_encode(\$r));";

        //
        $this->createFile($file, $content);

        //
        console_log('Criado ' . $file);
    }

}
