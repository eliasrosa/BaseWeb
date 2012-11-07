<?

class bwPager
{

    /**
     *  Frase no plural para os resultados
     * 
     * @var string
     *  
     */
    public $string1 = 'Encontrado %s registros em %s página(s)';

    /**
     *  Frase no singular para os resultados
     * 
     * @var string
     *  
     */
    public $string2 = 'Encontrado 1 registro';

    /*
     * Private vars 
     */
    private $dql;
    private $records_per_page;
    private $name;
    private $pager;
    private $pager_layout;
    private $pager_range;

    /**
     * __construct
     * 
     * Exemplo:
     * $dql = Doctrine_Query::create()
     *      ->from('Produto')
     *      ->where('status = 1 AND destaque = 1');
     * 
     * $pager = new bwPager($dql, NULL, 3);
     * 
     * foreach ($pager->execute() as $i) {
     *      echo $i->nome . '<br>';
     * }
     * 
     * $pager->display();
     * 
     * @param Doctrine_Query $dql 
     * @param string $name 
     * @param int $records_per_pager
     * @return void
     * 
     */
    public function __construct(Doctrine_Query $dql, $name = NULL,
        $records_per_page = 12, $range_chunk = 15)
    {
        $this->dql = $dql;
        $this->name = $name;
        $this->records_per_page = $records_per_page;
        $this->pager_range = new Doctrine_Pager_Range_Sliding(array('chunk' => $range_chunk));
    }

    /**
     * _getRequestName
     * 
     * @param stirng $var
     * @return string 
     */
    private function _getRequestName($var)
    {
        $name = $this->name . $var;

        return $name;
    }

    /**
     * _getRequestValue
     * 
     * @param string $var
     * @param string $default
     * @return string 
     */
    private function _getRequestValue($var, $default = NULL)
    {
        $name = $this->_getRequestName($var);
        $val = bwRequest::getVar($name, $default);

        return $val;
    }

    /**
     * execute
     *
     * Executa a query, dando inicio a paginação
     *
     * @param $params               Optional parameters to Doctrine_Query::execute
     * @param $hydrationMode        Hydration Mode of Doctrine_Query::execute returned ResultSet.
     * @return Doctrine_Collection  The root collection
     */
    public function execute($params = array(), $hydrationMode = NULL, $createSeo = true)
    {
        $this->pager = new Doctrine_Pager($this->dql, $this->_getRequestValue('pagina', 1), $this->records_per_page);

        // url template
        $url = new bwUrl();
        $url->setVar($this->_getRequestName('pagina'), '{%page_number}');

        $this->pager_layout = new Doctrine_Pager_Layout($this->pager, $this->pager_range, $url->toString(true));
        $this->pager_layout->setTemplate('<a class="pag" href="{%url}">{%page}</a>');
        $this->pager_layout->setSelectedTemplate('<a href="{%url}" class="pag active">{%page}</a>');

        $result = $this->pager->execute($params, $hydrationMode);
        
        if($createSeo === true){
            $this->createSeo();
        }
        
        return $result;
    }

    /**
     * display
     * 
     * Exibe ou retorna HTML com a paginação caso precise
     * 
     * @param boolean $return Caso verdadeiro, retorna em vez de exibir
     * @return string or void 
     */
    public function display($return = false)
    {
        ob_start();

        echo '<!-- Inicio paginação -->';
        echo sprintf('<div class="paginacao %s" class="result">', $this->name);

        if ($this->pager->getNumResults() > 1) {
            echo sprintf('<p>' . $this->string1 . '</p>', $this->pager->getNumResults(), $this->pager->getLastPage());
        } elseif ($this->pager->getNumResults() == 1) {
            echo '<p>' . $this->string2 . '</p>';
        }

        if ($this->pager->haveToPaginate()) {
            echo '<div class="paginas">';
            $this->pager_layout->display();
            echo '</div>';
        }

        echo '</div><!-- Fim paginação -->';

        if ($return)
            return ob_get_clean();
        else
            echo ob_get_clean();
    }

    /**
     * getNumResults
     * 
     * Retorna o total de registro encontrados
     * 
     * @return int
     */
    public function getNumResults()
    {
        return $this->pager->getNumResults();
    }

    /**
     * @return void
     */
    private function createSeo()
    {
        if (!$this->pager)
            die("bwPager não executado!");

        // url atual
        $url = new bwUrl();


        //
        if ($this->pager->haveToPaginate()) {
            $page = $this->pager->getPage();
            $last = $this->pager->getLastPage();

            for ($i = 1; $i <= $last; $i++) {

                if($i == $page){
                    continue;
                }
                
                $url->setVar($this->_getRequestName('pagina'), $i);
                $href = $url->toString();

                if ($i < $page) {
                    $rel = 'prev';
                }

                if ($i > $page) {
                    $rel = 'next';
                }

                if (isset($rel)) {
                    $head = sprintf('<link rel="%s" href="%s" />', $rel, $href);
                    $GLOBALS['bw.html.link'][] = $head;
                }
            }
        }
    }

}

?>
