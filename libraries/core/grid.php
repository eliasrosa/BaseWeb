<?

defined('BW') or die("Acesso negado!");

class bwGrid
{

    private
        $_id = NULL,
        $_dql = NUll,
        $_class = NULL,
        $_cols = array(),
        $_buscas = array(),
        $_limits = array();
    private
        $_orderCol = NULL,
        $_orderDir = NULL;
    // paineis
        var
        $painelBuscar = true,
        $painelLimit = true,
        $painelPaginacao = true,
        $painelResultados = true,
        $limitDefault = 0,
        $orderColDefault = 0,
        $orderDirDefault = 'asc';
    // string / custom
        var
        $strBusca = 'Buscar',
        $strLimits = 'Exibir %s registros por página',
        $strResults = 'Exibindo de %s a %s em %s registro%s',
        $strResultsOff = 'Nenhum registro foi encontrado!';

    // init()
    function __construct($dql = NULL, $id = NULL, $class = 'bwGrid')
    {
        //
        $this->_id = $id;
        $this->_class = $class;
        $this->setLimits(array(10, 25, 50));

        // get var request
        $this->_orderCol = $this->_getVar('ordercol', $this->orderColDefault);
        $this->_orderDir = $this->_getVar('orderdir', $this->orderDirDefault);
        $this->_limit = $this->_getVar('limit', $this->limitDefault);

        //
        if (!is_null($dql))
            $this->setQuery($dql);
    }

    // set limits ex: array(10, 25, 50, 100)
    function setLimits($limits, $painel = true)
    {
        if (is_array($limits))
            $this->_limits = $limits;
        else
            $this->_limits = array($limits);

        $this->painelLimit = $painel;
    }

    // seta query
    function setQuery(Doctrine_Query $dql)
    {
        $this->_dql = $dql;
    }

    // adiciona nova coluna
    function addCol($titulo, $order = NULL, $class = NULL, $width = NULL,
        $style = NULL)
    {
        $this->_cols[] = array(
            'titulo' => $titulo,
            'order' => $order,
            'class' => $class,
            'width' => $width,
            'style' => $style,
        );

        if (!is_null($order))
            $this->_buscas[] = $order;
    }

    // adiciona campo de busca
    function addBusca()
    {
        $args = func_get_args();
        $this->_buscas = array_merge($this->_buscas, $args);
    }

    // remove campo de busca
    function removeBusca($col)
    {
        unset($this->_buscas[$col]);
    }

    // pega var GET
    private function _getVar($var, $default = NULL)
    {
        $value = bwRequest::getVar($this->_getVarName($var), $default);
        return $value;
    }

    // pega nome da var GET
    private function _getVarName($var)
    {
        return "{$this->_id}_{$var}";
    }

    // pega limit
    private function _getLimit()
    {
        $i = (int) $this->_limit;
        $i = (isset($this->_limits[$i])) ? $i : 0;

        return $this->_limits[$i];
    }

    // adiciona a query de buscas
    private function _addBusca()
    {
        $busca = $this->_getVar('buscar', false);
        if (count($this->_buscas) && $this->painelBuscar && $busca) {
            $wheres = $params = array();
            foreach ($this->_buscas as $k => $w) {
                $wheres[] = "{$w} LIKE ?";
                $params[] = "%{$busca}%";
            }
            $this->_dql->addWhere(join(' OR ', $wheres), $params);
        }
    }

    // adiciona a query ORDER BY
    private function _addOrderBy()
    {
        if (isset($this->_cols[$this->_orderCol]['order'])) {
            $dir = ($this->_orderDir == 'asc') ? 'ASC' : 'DESC';
            $ord = $this->_cols[$this->_orderCol]['order'];

            $this->_dql->orderBy("{$ord} {$dir}");
        }
    }

    // executa a query
    private function _executeQuery()
    {
        // modify DQL
        $this->_addBusca();
        $this->_addOrderBy();

        // debug / test
        //echo $this->_dql->getSqlQuery();
        // URL atual
        $url = new bwUrl();
        $url->setVar($this->_getVarName('pagina'), '{%page_number}');

        // pager
        $this->pager = new Doctrine_Pager(
                $this->_dql,
                $this->_getVar('pagina', 1),
                $this->_getLimit()
        );

        // pager layout
        $this->pagerLayout = new Doctrine_Pager_Layout(
                $this->pager,
                new Doctrine_Pager_Range_Sliding(array('chunk' => 5)),
                $url->toString()
        );

        $this->pagerLayout->setTemplate('<a href="{%url}">{%page}</a>');
        $this->pagerLayout->setSelectedTemplate('<a class="active" href="{%url}">{%page}</a>');
    }

    // retorna o html
    function show($retorno = false)
    {
        // monta o head
        $h = '';
        foreach ($this->_cols as $k => $c) {
            $titulo = $c['titulo'];
            $class = !is_null($c['class']) ? " {$c['class']}" : "";
            $width = !is_null($c['width']) ? " width=\"{$c['width']}\"" : "";
            $style = !is_null($c['style']) ? " style=\"{$c['style']}\"" : "";

            if (!is_null($c['order'])) {
                $dir = $this->_orderDir;
                $dirInvertido = ($dir == 'asc') ? 'desc' : 'asc';

                $url = new bwUrl();
                $url->delVar($this->_getVarName('pagina'));
                $url->setVar($this->_getVarName('ordercol'), $k);
                $url->setVar($this->_getVarName('orderdir'), $dirInvertido);

                $titulo = sprintf('<a href="%s">%s</a>', $url->toString(), $titulo
                );

                if ($this->_orderCol == $k)
                    $class = " {$dir}{$class}";
            }

            $attr = "";
            $attr = "class=\"col{$k}{$class}\"{$style}{$width}";

            $h .= sprintf('<th %s>%s</th>', $attr, $titulo
            );
        }
        $corpo = sprintf('<thead>%s</thead>', $h);


        // monta o tbody
        $corpo .= '<tbody>';
        $this->_executeQuery();
        foreach ($this->pager->execute() as $r) {
            $corpo .= '<tr>';
            foreach ($this->_cols as $k => $c) {
                $class = !is_null($c['class']) ? " {$c['class']}" : "";
                $width = !is_null($c['width']) ? " width=\"{$c['width']}\"" : "";
                $style = !is_null($c['style']) ? " style=\"{$c['style']}\"" : "";

                $attr = "";
                $attr = "class=\"col{$k}{$class}\"{$style}{$width}";

                $func_name = "col{$k}";

                if (method_exists($this, $func_name))
                    $conteudo = call_user_func(array($this, $func_name), $r);
                else {
                    $id = is_null($this->_id) ? 'grid' : $this->_id;
                    $conteudo = call_user_func("{$id}_{$func_name}", $r);
                }

                $corpo .= sprintf('<td %s>%s</td>', $attr, $conteudo
                );
            }
            $corpo .= '</tr>';
        }
        $corpo .= '</tbody>';


        if ($this->painelLimit) {
            // monta top
            $select = '<select onchange="window.location.href=this.options[this.selectedIndex].value">';
            foreach ($this->_limits as $k => $l) {
                $url = new bwUrl();
                $url->setVar($this->_getVarName('pagina'), 1);
                $url->setVar($this->_getVarName('limit'), $k);

                $selected = ($this->_getVar('limit', 0) == $k) ? ' selected="selected"' : '';
                $select .= sprintf('<option value="%s"%s>%s</option>', $url->toString(), $selected, $l);
            }
            $select .= '</select>';
            $top .= sprintf('<div class="limit">' . $this->strLimits . '</div>', $select);
        }

        // cria form de busca
        if (count($this->_buscas) && $this->painelBuscar) {
            $url = new bwUrl();
            $top .= sprintf('<form class="busca" method="get" action="%s">', $url->toString());
            $top .= sprintf('<input class="text" type="text" name="%s" value="%s" />', $this->_getVarName('buscar'), $this->_getVar('buscar'));

            // apaga, para não repitir
            $url->delVar($this->_getVarName('buscar'));

            if (count($url->getQuery(true))) {
                foreach ($url->getQuery(true) as $k => $v) {
                    $top .= sprintf('<input type="hidden" name="%s" value="%s" />', $k, $v);
                }
            }

            $top .= sprintf('<input class="submit" type="submit" value="%s"/>', $this->strBusca);
            $top .= sprintf('</form>');
        }

        // monta rodape
        $rodape = '';
        if ($this->pager->getNumResults()) {
            $rodape .= '<div class="resultados">';
            $rodape .= sprintf($this->strResults, $this->pager->getFirstIndice(), $this->pager->getLastIndice(), $this->pager->getNumResults(), ($this->pager->getNumResults() > 1) ? 's' : ''
            );
            $rodape .= '</div>';

            // paginação
            if ($this->pager->getLastPage() != 1) {
                $rodape .= '<div class="paginacao">';

                //
                $url = new bwUrl();
                $url->setVar($this->_getVarName('pagina'), $this->pager->getPreviousPage());
                if ($this->pager->getPage() > 1)
                    $rodape .= sprintf('<a href="%s"><< Página anterior</a>', $url->toString());

                ob_start();
                $this->pagerLayout->display();
                $rodape .= ob_get_clean();

                $url->setVar($this->_getVarName('pagina'), $this->pager->getNextPage());
                if ($this->pager->getPage() != $this->pager->getLastPage())
                    $rodape .= sprintf('<a href="%s">Próxima página >></a>', $url->toString());

                $rodape .= '</div>';
            }
        }
        else {
            $rodape = $this->strResultsOff;
        }

        // monta table
        $class = !is_null($this->_class) ? " class=\"{$this->_class}\"" : '';
        $html = sprintf('<div%s id="%s">%s<br class="clearfix" /><table>%s</table>%s<br class="clearfix" /></div>', $class, $this->_id, $top, $corpo, $rodape
        );

        if ($retorno)
            return $html;

        echo $html;
    }

}

?>
