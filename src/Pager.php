<?php

namespace yishuixm\data;

class Pager{
    private $total;         //总记录
    private $pagesize=25;   //每页显示多少条
    private $page;          //当前页数
    private $pagenum;       //总页数
    private $shownum=5;     //前后显示多少页
    private $sign='p';      //页码
    private $template = ""; //显示模板

    public function __construct($total, $pagesize=25){
        $this->total = $total;
        $this->pagesize = $pagesize;
        $this->init();
    }

    // 初始化
    private function init(){
        $this->pagenum = ceil($this->total / $this->pagesize);
        $this->page = isset($_GET[$this->sign])?$_GET[$this->sign]:1;
        $this->template = [
            "theme"     => "<div class=\"page\">[first] [prev] [link] [next] [last] [page][pagenum][pagesize]</div>",
            "first"     => "<a href=\"[href]\" class=\"page-first\">First</a>",
            "last"      => "<a href=\"[href]\" class=\"page-last\">Last</a>",
            "prev"      => "<a href=\"[href]\" class=\"page-prev\">Prev</a>",
            "next"      => "<a href=\"[href]\" class=\"page-next\">Next</a>",
            "link"      => "<a href=\"[href]\" class=\"page-link [cur]\">[link]</a>",
        ];
    }

    public function setTemplate($template){
        $this->template = $template;
    }

    // 得到分页
    public function limit(){
        $start = $this->page * $this->pagesize - $this->pagesize;
        $step = $this->pagesize;
        if($start<0) $start = 0;
        return [$start, $step];
    }

    // 得到URL
    private function getUrl($page){

        if(intval($page)<1){
            $page=1;
        }elseif(intval($page)>$this->pagenum){
            $page=$this->pagenum;
        }

        $_url = $_SERVER["REQUEST_URI"];
        $_par = parse_url($_url);
        if (isset($_par['query'])) {
            parse_str($_par['query'],$_query);
            $_query[$this->sign] = $page;
            $_url = $_par['path'].'?'.http_build_query($_query);
        }else{
            $_query[$this->sign] = $page;
            $_url = $_par['path'].'?'.http_build_query($_query);
        }
        return $_url;
    }


    //首页
    private function first() {
        return str_replace([
            '[href]'
        ],[
            $this->getUrl(1)
        ],$this->template['first']);
    }

    //上一页
    private function prev() {
        return str_replace([
            '[href]'
        ],[
            $this->getUrl($this->page-1)
        ],$this->template['prev']);
    }

    //下一页
    private function next() {
        return str_replace([
            '[href]'
        ],[
            $this->getUrl($this->page+1)
        ],$this->template['next']);
    }

    //尾页
    private function last() {
        return str_replace([
            '[href]'
        ],[
            $this->getUrl($this->pagenum)
        ],$this->template['last']);
    }

    // 数字页
    private function link(){
        $s_b = 0;//左偏移
        $e_b = 0;//右偏移
        $start = $this->page - $this->shownum;
        $end = $this->page + $this->shownum;


        $s_b = abs(1 - $this->page - $this->shownum);
        $e_b = abs($this->page + $this->shownum - $this->pagenum);

        $start-=$e_b;
        $end+=$s_b;

        if($start<1){
            $start=1;
        }

        if($end>$this->pagenum) {
            $end=$this->pagenum;
        }


        $link = '';
        for ($i=$start;$i<=$end;$i++){
            $link .= str_replace([
                '[href]',
                '[link]',
                '[cur]'
            ],[
                $this->getUrl($i),
                $i,
                $this->page==$i?'class="active"':''
            ],$this->template['link']);
        }

        return $link;
    }

    // 生成分面代码
    public function show(){
        return str_replace([
            '[first]',
            '[prev]',
            '[link]',
            '[next]',
            '[last]',
            '[page]',
            '[pagenum]',
            '[pagesize]'
        ],[
            $this->first(),
            $this->prev(),
            $this->link(),
            $this->next(),
            $this->last(),
            $this->page,
            $this->pagenum,
            $this->pagesize
        ],$this->template['theme']);
    }
}