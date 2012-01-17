<?
defined('BW') or die("Acesso negado!");

class bwGrid
{
    // parâmetro id
    private $id = NULL; 

    // parâmetro class
    private $class = NULL; 

    // colunas 
    private $cols = array();

    // buscas 
    private $wheres = array();

    // DQL Principal
    private $dql = NUll;
 
    // limits permitidos
    private $limits = array(10, 25, 50);

    //
    function __construct($id = NULL, $class = 'bwGrid')
    {
        $this->id = (is_null($id)) ? 'grid' : $id;
        $this->class = $class;
    }
    
    // pega var GET
    function getVar($var, $default = NULL)
    {
        return bwRequest::getVar($this->getVarName($var), $default);
    }

    // pega nome da var GET
    function getVarName($var)
    {
        return "{$this->id}_{$var}";
    }
    
    // pega limit
    function getLimit()
    {
        $i = (int) $this->getVar('limit', 0);
        $i = (isset($this->limits[$i])) ? $i : 0;
        
        return $this->limits[$i];
    }
    
    // seta limits ex: array(10, 25, 50, 100)
    function setLimit($array)
    {
        $this->limits = $array;
    }

    // seta query
    function setQuery(Doctrine_Query $dql)
    {
        $this->dql = $dql;
    }

    // adiciona nova coluna
    function addCol($titulo, $order = NULL, $class = NULL, $width = NULL, $style = NULL)
    {
        $this->cols[] = array(
            'titulo' => $titulo,
            'order' => $order,
            'class' => $class,
            'width' => $width,
            'style' => $style,
        );
        
        if(!is_null($order))
            $this->wheres[] = $order;
    }

    // adiciona campo de busca
    function addBusca()
    {
        $args = func_get_args(); 
        $this->wheres = array_merge($this->wheres, $args);
    }

    //
    private function executeDql()
    {
        $query = $this->dql;
        
        // cria where
        if(count($this->wheres))
        {
            $where = $this->wheres;
            foreach($where as $k=>$w)
                $where[$k] = "{$w} LIKE :busca";
            
            $busca = $this->getVar('buscar', '');
            $query->addWhere(join(' OR ', $where), array(':busca' => "%{$busca}%"));
        }
        
        $limit = $this->getLimit();
        $paginaAtual = $this->getVar('pagina', 1);    
    
        $ordercol = $this->getVar('ordercol', 0);
        if(isset($this->cols[$ordercol]['order']))
        {
            $dir = $this->getVar('orderdir');
            $dir = ($dir == 'asc') ? 'ASC' : 'DESC';
            $ord = $this->cols[$ordercol]['order'];
            
            $query->orderBy("{$ord} {$dir}");
        }

        $url = new bwUrl();
        $url->setVar($this->getVarName('pagina'), '{%page_number}');

        $this->pager = new Doctrine_Pager($query, $paginaAtual, $limit);
        $this->pagerLayout = new Doctrine_Pager_Layout(
            $this->pager,
            new Doctrine_Pager_Range_Sliding(array(
                'chunk' => 5
            )),
            $url->toString()
        );

        $this->pagerLayout->setTemplate('<a href="{%url}">{%page}</a>');
        $this->pagerLayout->setSelectedTemplate('<a class="active" href="{%url}">{%page}</a>');
    }
    
    
    //
    function show($retorno = false)
    {
        // monta o head
        $h = '';
        foreach($this->cols as $k=>$c)
        {
            $titulo = $c['titulo'];
            $class = !is_null($c['class']) ? " {$c['class']}" : "";
            $width = !is_null($c['width']) ? " width=\"{$c['width']}\"" : "";
            $style = !is_null($c['style']) ? " style=\"{$c['style']}\"" : "";
            
            if(!is_null($c['order']))
            {
                $dir = $this->getVar('orderdir');
                $dirInvertido = ($dir == 'asc') ? 'desc' : 'asc';
             
                $url = new bwUrl();
                $url->delVar($this->getVarName('pagina'));
                $url->setVar($this->getVarName('ordercol'), $k);
                $url->setVar($this->getVarName('orderdir'), $dirInvertido);
                
                $titulo = sprintf('<a href="%s">%s</a>',
                    $url->toString(),
                    $titulo
                );

                if($this->getVar('ordercol') == $k)
                    $class = " {$dir}{$class}";
            }
            
            $attr = "";
            $attr = "class=\"col{$k}{$class}\"{$style}{$width}";
         
            $h .= sprintf('<th %s>%s</th>', 
                $attr,
                $titulo
            );
        }
        $corpo = sprintf('<thead>%s</thead>', $h);
        
        
        // monta o tbody
        $corpo .= '<tbody>';
        $this->executeDql();
        foreach($this->pager->execute() as $r)
        {
            $corpo .= '<tr>';
            foreach($this->cols as $k => $c)
            {
                $class = !is_null($c['class']) ? " {$c['class']}" : "";
                $width = !is_null($c['width']) ? " width=\"{$c['width']}\"" : "";
                $style = !is_null($c['style']) ? " style=\"{$c['style']}\"" : "";
                
                $attr = "";
                $attr = "class=\"col{$k}{$class}\"{$style}{$width}";
            
                $corpo .= sprintf('<td %s>%s</td>',
                    $attr,
                    call_user_func("{$this->id}_col{$k}", $r)
                );
            }            
            $corpo .= '</tr>';
        }
        $corpo .= '</tbody>';
        
        
        // monta top
        $select = '<select onchange="window.location.href=this.options[this.selectedIndex].value">';
        foreach($this->limits as $k=>$l)
        {
            $url = new bwUrl();
            $url->setVar($this->getVarName('pagina'), 1);
            $url->setVar($this->getVarName('limit'), $k);
            
            $selected = ($this->getVar('limit', 0) == $k) ? ' selected="selected"' : '';
            $select .= sprintf('<option value="%s"%s>%s</option>', $url->toString(), $selected, $l); 
        }
        $select .= '</select>';
        $top .= sprintf('<div class="limit">Exibir %s registros por página</div>', $select);
        
        // cria form de busca
        if(count($this->wheres))
        {
            $url = new bwUrl();
            $top .= sprintf('<form class="busca" method="get" action="%s">', $url->toString());
            $top .= sprintf('<input class="text" type="text" name="%s" value="%s" />', $this->getVarName('buscar'), $this->getVar('buscar'));
            $top .= sprintf('<input type="hidden" name="%s" value="%s" />', $this->getVarName('limit'), $this->getVar('limit', 0));
            $top .= sprintf('<input type="hidden" name="%s" value="%s" />', $this->getVarName('ordercol'), $this->getVar('ordercol', 0));
            $top .= sprintf('<input type="hidden" name="%s" value="%s" />', $this->getVarName('orderdir'), $this->getVar('orderdir', 'asc'));
            $top .= '<input class="submit" type="submit" value="Buscar"/>';
            $top .= '</form>';
        }

        // monta rodape
        $rodape = '<div class="resultados">';
        $rodape .= sprintf('Exibindo %s de %s em %s registro%s',
            $this->pager->getFirstIndice(),
            $this->pager->getLastIndice(),
            $this->pager->getNumResults(),
            ($this->pager->getNumResults() > 1) ? 's' : ''
        );
        $rodape .= '</div>';
        $rodape .= '<div class="paginacao">';
        
        $url = new bwUrl();
        
        $url->setVar($this->getVarName('pagina'), $this->pager->getPreviousPage());
        if($this->pager->getPage() > 1)
            $rodape .= sprintf('<a href="%s"><< Página anterior</a>', $url->toString());
        
        ob_start();
        $this->pagerLayout->display();
        $rodape .= ob_get_clean();
        
        $url->setVar($this->getVarName('pagina'), $this->pager->getNextPage());
        if($this->pager->getPage() != $this->pager->getLastPage())
            $rodape .= sprintf('<a href="%s">Próxima página >></a>', $url->toString());
        
        $rodape .= '</div>';

        // monta table
        $class = !is_null($this->class) ? " class=\"{$this->class}\"" : '';
        $html = sprintf('<div%s id="%s">%s<br class="clearfix" /><table>%s</table>%s<br class="clearfix" /></div>',
            $class,
            $this->id,
            $top,
            $corpo,
            $rodape
        );
        
        if($retorno)
            return $html;
        
        echo $html;
    }

}
?>
