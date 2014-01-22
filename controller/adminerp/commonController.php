<?php

class commonController extends C
{
    public function index($action)
    {
        if (!is_file($action))
            exit('error:no such action file!');
        global $InitPHP_conf;
        function display()
        {
            global $InitPHP_conf;
            $InitPHP_conf['display'] = 'set';
        }
        $token_get = 1;
        if ($_GET && $token_get)
        {
            $arraykeys = $_GET;
        }
        array_walk($arraykeys, array(controllerInit, 'trim_value'));
        extract($arraykeys, EXTR_PREFIX_SAME, "pre");
        unset($arraykeys);
        if ($_POST && $token_get)
        {
            $arraykeys = $_POST;
        }
        array_walk($arraykeys, array(controllerInit, 'trim_value'));
        extract($arraykeys, EXTR_PREFIX_SAME, "pre");
        unset($arraykeys);
        $tag_erp_projectURL = $this->getLibrary('basefuns')->check_local();
        if (($_SESSION['logined'] != $tag_erp_projectURL || !$_SESSION['username']) && $detail !=
            'login')
        {
            $this->C->redirect('login.php');
        }
        if ($_SESSION[$_REQUEST['action'] . '_' . 'stypemu'])
            require 'searchController.php';
        require ('check_menuRightsController.php');
        require ($action);
        if ($temp == 'pub_list' || $temp == 'pub_edit')
        {
            require ('htmlController.php');
        }
        if ($this->V->view['title'])
        {
            $v_title = $this->V->view['title'];
            $new_dis_title = '';
            $new_title = explode('-', $v_title);
            if (count($new_title) == 1)
            {
                $new_dis_title .= "<a href='index.php?action=" . $_GET['action'] . "&detail=" .
                    $detail . "' title='刷新'>" . $v_title . "</a>";
            } else
            {
                for ($i = count($new_title) - 1; $i >= 0; $i--)
                {
                    if ($i != 0)
                    {
                        $kuo_l_p = strpos($new_title[$i], '(');
                        if (preg_match('/\(.*\)/', $new_title[$i], $pre_t_detail))
                        {
                            $pre_t_detail = strtr($pre_t_detail['0'], array('(' => '', ')' => ''));
                        }
                        $new_dis_title .= "<a href='index.php?action=" . $_GET['action'] . "&detail=" .
                            $pre_t_detail . "' >" . substr($new_title[$i], 0, $kuo_l_p) . "</a> &raquo; ";
                    } else
                    {
                        $new_dis_title .= $new_title[$i];
                    }
                }
            }
            $this->V->view['title'] = $new_dis_title;
        }
        if ($InitPHP_conf['display'] == 'set')
        {
            if (isset($InitPHP_conf['pageval']) && $InitPHP_conf['sums'] > $InitPHP_conf['pageval'])
            {
                $ar = '';
                if (is_array($pageshow))
                {
                    $vr = array_values($pageshow);
                    $kr = array_keys($pageshow);
                    for ($i = 0; $i < count($pageshow); $i++)
                    {
                        $ar .= '&' . $kr[$i] . '=' . $vr[$i];
                    }
                }
                $page_html = $this->load('pager', 'l')->pager($InitPHP_conf['sums'], $InitPHP_conf['pageval'],
                    'index.php?action=' . $_GET['action'] . '&detail=' . $detail . $ar);
                $this->V->assign('page_html', $page_html);
                $ajax_page_html = $this->load('ajaxpager', 'l')->pager($InitPHP_conf['sums'], $InitPHP_conf['pageval'],
                    'index.php?action=' . $_GET['action'] . '&detail=' . $detail . $ar);
                $this->V->assign('ajax_page_html', $ajax_page_html);
            }
            $this->V->display();
        }
    }
}

?>