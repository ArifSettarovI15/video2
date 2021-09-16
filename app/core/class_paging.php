<?php

class ClassPaging
{
    /**
     * @var MainClass
     */
    var $registry;
    var $status;
    var $current_page = 1;
    var $per_page;
    var $pager_middle = 3;
    var $sql_start = 0;
    var $total = 0;
    var $data = array();
    var $pages = array();
    var $show_per_page = false;
    var $page_link_main;
    var $skip_per_page_c;
    var $no_empty;
    var $template = 'parts/paging.html.twig';
    var $template2 = 'parts/paging.html.twig';
    var $template3 = 'parts/paging.html.twig';


    function __construct($registry, $per_page = 10, $skip_per_page_c = false, $no_empty = true)
    {
        $this->registry =& $registry;
        $this->per_page = $per_page;
        $this->skip_per_page_c = $skip_per_page_c;
        $this->no_empty = $no_empty;

        $this->GetPagesRequests();
        $this->MakePageLinkMain();
    }

    function MakePageLinkMain()
    {
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('page/', $url);
        $bb = explode('?', $parts[0]);
        $this->page_link_main = BASE_URL . $bb[0];
    }

    function GetPagesOptions()
    {
        $next = false;
        $prev = false;
        if (($this->current_page - 1) > 0) {
            $prev = $this->current_page - 1;
        }

        if (($this->current_page * $this->per_page) < $this->total) {
            $next = $this->current_page + 1;
        }

        $to_show = $this->per_page;
        if ($this->total < ($this->per_page * 2 + $this->sql_start)) {
            $to_show = $this->total - $this->sql_start - $this->per_page;
        }


        return array(
            'current_page' => $this->current_page,
            'per_page' => $this->per_page,
            'total' => $this->total,

            'pages' => $this->pages,
            'show_per_page' => $this->show_per_page,
            'page_link_main' => $this->page_link_main,
            'next' => $next,
            'prev' => $prev,
            'to_show' => $to_show,
            'status' =>true,
        );
    }

    function Display($template_name, $variables = array())
    {
        $this->pages = $this->GetPages();

        if ($this->registry->GPC['ajax'] == 1) {

            $this->DisplayAjaxResponse($template_name, $variables);

        } else {
            $this->DisplayDataList($variables);


        }
    }

    function DisplayDataList($variables)
    {
        $error = '';
        if (count($this->data) == 0 && $this->no_empty) {
            $error = $this->registry->error->EmptyResult();
        }
        $this->registry->template->Display(array(
                'list' => $this->data,
                'list_error' => $error,
                'paging_data' => $this->GetPagesOptions(),
                'variables' => $variables
            )
        );
    }

    function DisplayAjaxResponse($table_template, $variables)
    {
        $html = $this->registry->template->Render($table_template,
            array(
                'list' => $this->data,
                'input_name' => $this->registry->GPC['input_name'],
                'variables' => $variables,
                'total' => $this->total,

            )
        );

        $array = array();
        $array['html'] = $html;
        $array['paging'] = $this->GetPagingTemplate();
        $array['paging_old'] = $this->GetPagingTemplate3();
        $array['status'] = true;

        if ($variables['type'] == 'numbers') {
            $array['paging'] = $this->GetPagingTemplate2();
        }

        $array['total'] = $this->total;
        if ($variables['hash']) {
            $array['hash'] = $variables['hash'];
        }
        if ($variables['filters_html']) {
            $array['filters'] = $variables['filters_html'];
        }
        if ($variables['insert_filters']) {
            $array['insert_filters'] = $variables['insert_filters'];
        }

        if ($variables['total_before']) {
            $array['total_before'] = $variables['total_before'];
        }
        if ($variables['total_after']) {
            $array['total_after'] = $variables['total_after'];
        }
        $this->registry->template->DisplayJson($array);
    }

    function GetPagesRequests()
    {
        $this->registry->input->clean_array_gpc('r', array(
            'per_page' => TYPE_UINT,
            'sort' => TYPE_STR,
            'sort_way' => TYPE_STR
        ));


        if ($this->registry->GPC['page'] and $this->registry->GPC['ajax'] != 1) {

        } else {
            $this->registry->input->clean_array_gpc('p', array(
                'page' => TYPE_UINT
            ));
            if ($this->registry->GPC['page']) {

            } else {
                $this->registry->input->clean_array_gpc('g', array(
                    'page' => TYPE_UINT
                ));
            }
        }

        if ($this->skip_per_page_c == false) {
            $this->registry->input->clean_array_gpc('c', array(
                'c_per_page' => TYPE_UINT
            ));
            if ($this->registry->GPC['c_per_page'] > 0) {
                $this->per_page = $this->registry->GPC['c_per_page'];
            }
        }

        if ($this->registry->GPC['per_page'] > 0) {
            $this->per_page = $this->registry->GPC['per_page'];
        }

        if ($this->registry->GPC['page'] > 0) {
            $this->current_page = $this->registry->GPC['page'];
        }
        $this->sql_start = ($this->current_page - 1) * $this->per_page;
    }

    function GetPages()
    {
        $max_page = ceil($this->total / $this->per_page);
        if ($this->current_page > $max_page && $max_page != 0) {
            $this->current_page = $max_page;
            $this->registry->error->PageNotFound();
        }
        $dop_right = 0;

        $list_array = array();

        if (1 != $max_page && $max_page > 0) {
            // prev
            if (($this->current_page - 1) > 0) {
                $disabled = false;

            } else {
                $disabled = true;
            }
            $list_array[] = array(
                'name' => 'prev',
                'value' => $this->current_page - 1,
                'disabled' => $disabled
            );


            // first
            $list_array[] = array(
                'name' => 'page',
                'value' => 1
            );

            //middle left
            if (($this->current_page - 1) > $this->pager_middle) {
                $list_array[] = array(
                    'name' => 'middle',
                    'value' => '...'
                );
                $min = $this->current_page - $this->pager_middle + 1;
            } else {
                $min = 2;
            }

            if (($max_page - $this->current_page) > $this->pager_middle) {
                $dop_right = 1;
                $max = $this->current_page + $this->pager_middle;
            } else {
                $max = $max_page;
            }

            for ($i = $min; $i < $max; $i++) {
                $list_array[] = array(
                    'name' => 'page',
                    'value' => $i
                );
            }

            if ($dop_right == 1) {
                $list_array[] = array(
                    'name' => 'middle',
                    'value' => '...'
                );
            }

            if ($max != 1) {
                $list_array[] = array(
                    'name' => 'page',
                    'value' => $max_page
                );
            }


            // next
            if (($this->current_page + 1) <= $max_page) {
                $disabled = false;

            } else {
                $disabled = true;
            }
            $list_array[] = array(
                'name' => 'next',
                'value' => $this->current_page + 1,
                'disabled' => $disabled
            );

        }

        return $list_array;
    }

    function GetPagingTemplate()
    {
        return $this->registry->template->Render($this->template,
            array(
                'paging_data' => $this->GetPagesOptions()
            )
        );
    }

    function GetPagingTemplate2()
    {
        return $this->registry->template->Render($this->template2,
            array(
                'paging_data' => $this->GetPagesOptions()
            )
        );
    }

    function GetPagingTemplate3()
    {
        return $this->registry->template->Render($this->template3,
            array(
                'paging_data' => $this->GetPagesOptions()
            )
        );
    }

    function GetLimitSql($count, $start_page)
    {
        if ($count == 'all') {
            $sql_limit = "LIMIT " . $start_page;
        } else {
            $sql_limit = "LIMIT " . $start_page . "," . $count;
        }
        return $sql_limit;
    }
}
