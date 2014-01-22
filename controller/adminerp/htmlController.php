<?php
if ($temp == 'pub_edit')
{
    if (!is_array($colwidth))
    {
        $colwidth = array(
            '1' => '25%',
            '2' => '50%',
            '3' => '25%');
    }
    $hidden_input = '';
    $inputkey = array_keys($disinputarr);
    $numfor = count($disinputarr);
    for ($i = 0; $i < $numfor; $i++)
    {
        $keyname = $inputkey[$i];
        if ($disinputarr[$keyname]['datatype'] == 'se')
        {
            if (!$disinputarr[$keyname]['datastr'])
            {
                exit('error:no $datastr');
            }
            $disinputarr[$keyname]['disinput'] = $disinputarr[$keyname]['datastr'];
        } elseif ($disinputarr[$keyname]['datatype'] == 'h')
        {
            $value = $disinputarr[$keyname]['value'];
            $hidden_input .= "<input type='hidden' name=$keyname value=$value>";
            unset($disinputarr[$keyname]);
        }
        //复选框
        elseif ($disinputarr[$keyname]['datatype'] == 'checkbox'){
            $disinputarr[$keyname]['disinput'] = $disinputarr[$keyname]['datastr'];
        }
        //生成图片
        elseif ($disinputarr[$keyname]['datatype'] == 'img'){
            $value = $disinputarr[$keyname]['src'];
            $disinputarr[$keyname]['disinput'] = "<img src=$value alt='' style='width:115px;height:115px;'/>";
        }
         else
        {
            $value = $disinputarr[$keyname]['value'];
            $inextra = isset($disinputarr[$keyname]['inextra']) ? $disinputarr[$keyname]['inextra'] :
                '';
            $disinputarr[$keyname]['disinput'] = "<input type='text' name=$keyname value='" .
                $value . "'" . $inextra . ">";
        }
    }
    $markarr = array(
        'disinputarr' => $disinputarr,
        'hidden_input' => $hidden_input,
        'jslink' => $jslink,
        'conform' => $conform,
        'colwidth' => $colwidth,
        'bannerstr' => $bannerstr);
    $this->V->mark($markarr);
    $this->V->set_tpl('adminweb/commom_input');
    unset($markarr);
    display();
}
if ($temp == 'pub_list')
{
    if ($stypemu)
    {
        $_SESSION[$_REQUEST['action'] . '_' . 'stypemu'] = json_encode($stypemu);
        $search_output = array();
        $serkeys = array_keys($stypemu);
        $search_hidden = '';
        $jumpurl = 'index.php?action=' . $_GET['action'] . '&detail=' . $detail;
        $pageshow = is_array($pageshow) ? $pageshow : array();
        $timeCountSystem = 0;

        for ($s = 0; $s < count($stypemu); $s++)
        {
            $exploarr = explode('-', $serkeys[$s]);
            if ($exploarr[1] == 's')
            {
                $showinput = $stypemu[$serkeys[$s]] . '<input type=text name=' . $exploarr[0] .
                    ' value=' . $$exploarr[0] . '>';
                $search_output[$s] = array('showinput' => $showinput);
                $pageshow["$exploarr[0]"] = $$exploarr[0];
                $bannerstr_extra_parse .= '&' . $exploarr[0] . '=' . $$exploarr[0];
            } elseif ($exploarr[1] == 'h')
            {
                $search_hidden .= '<input type=hidden name=' . $exploarr[0] . ' value=' . $$exploarr[0] .
                    '>';
                $pageshow["$exploarr[0]"] = $$exploarr[0];
                $bannerstr_extra_parse .= '&' . $exploarr[0] . '=' . $$exploarr[0];
            } elseif ($exploarr[1] == 'a')
            {
                if (!is_array($acols = ${"$exploarr[0]arr"}))
                {
                    exit('error:no parsearr($' . $exploarr[0] . ')');
                }
                $acols_keys = array_keys($acols);
                $showinput = $stypemu[$serkeys[$s]] . '<select name=' . $exploarr[0] . '>';
                for ($o = 0; $o < count($acols); $o++)
                {
                    $selected = strval($$exploarr[0]) === strval($acols_keys[$o]) ? 'selected' : '';
                    $showinput .= '<option value="' . $acols_keys[$o] . '" ' . $selected . '>' . $acols[$acols_keys[$o]] .
                        '</option>';
                }
                $showinput .= '</select>';
                $search_output[$s] = array('showinput' => $showinput);
                $pageshow["$exploarr[0]"] = $$exploarr[0];
                $bannerstr_extra_parse .= '&' . $exploarr[0] . '=' . urlencode($$exploarr[0]);
            } elseif ($exploarr[1] == 't')
            {
 				$timeCountSystem++;
				if($timeCountSystem <2){//如果只有一个时间筛选

					/*双时间范围*/
					if($exploarr[2] == 't'){
						$showinput = $stypemu[$serkeys[$s]].'<input type="text" name="startTime"  class="find-T twodate" onClick="WdatePicker()"  value='.$startTime.'>';
						$showinput.= '&nbsp;-&nbsp;<input type="text" name="endTime"  class="find-T twodate" onClick="WdatePicker()"  value='.$endTime.'>';
						$search_output[$s] = array('showinput'=>$showinput);
						$pageshow['startTime'] = $startTime;//增加分页额外参数。
						$pageshow['endTime'] = $endTime;//增加分页额外参数。
						$bannerstr_extra_parse.= '&startTime='.$startTime.'&endTime='.$endTime;//导出按钮链接
					}

					/*仅起始时间*/
					elseif($exploarr[2] == 'l'){
						$showinput 				= $stypemu[$serkeys[$s]].'<input type="text" name="startTime"  class="find-T" onClick="WdatePicker()"  value='.$startTime.'>';
						$search_output[$s] 		= array('showinput'=>$showinput);
						$pageshow['startTime'] 	= $startTime;//增加分页额外参数。
						$bannerstr_extra_parse .= '&startTime='.$startTime;//导出按钮链接
					}

					/*仅截止时间*/
					elseif($exploarr[2] == 'r'){
						$showinput 				= $stypemu[$serkeys[$s]].'<input type="text" name="endTime"  class="find-T" onClick="WdatePicker()"  value='.$endTime.'>';
						$search_output[$s] 		= array('showinput'=>$showinput);
						$pageshow['endTime'] 	= $endTime;//增加分页额外参数。
						$bannerstr_extra_parse .= '&endTime='.$endTime;//导出按钮链接
					}
				}

				else{//如果有多个时间

					/*双时间范围*/
					if($exploarr[2] == 't'){
						$showinput = $stypemu[$serkeys[$s]].'<input type="text" name="'.$exploarr[0].'startTime"  class="find-T twodate" onClick="WdatePicker()"  value='.${$exploarr[0].'startTime'}.'>';
						$showinput.= '&nbsp;-&nbsp;<input type="text" name="'.$exploarr[0].'endTime"  class="find-T twodate" onClick="WdatePicker()"  value='.${$exploarr[0].'endTime'}.'>';
						$search_output[$s] = array('showinput'=>$showinput);
						$pageshow[$exploarr[0].'startTime'] = ${$exploarr[0].'startTime'};//增加分页额外参数。
						$pageshow[$exploarr[0].'endTime'] = ${$exploarr[0].'endTime'};//增加分页额外参数。
						$bannerstr_extra_parse.= '&'.$exploarr[0].'startTime='.${$exploarr[0].'startTime'}.'&'.$exploarr[0].'endTime='.${$exploarr[0].'endTime'};//导出按钮链接
					}

					/*仅起始时间*/
					elseif($exploarr[2] == 'l'){
						$showinput 				= $stypemu[$serkeys[$s]].'<input type="text" name="'.$exploarr[0].'startTime"  class="find-T" onClick="WdatePicker()"  value='.${$exploarr[0].'startTime'}.'>';
						$search_output[$s] 		= array('showinput'=>$showinput);
						$pageshow[$exploarr[0].'startTime'] = ${$exploarr[0].'startTime'};//增加分页额外参数。
						$bannerstr_extra_parse .= '&'.$exploarr[0].'startTime='.${$exploarr[0].'startTime'};//导出按钮链接
					}

					/*仅截止时间*/
					elseif($exploarr[2] == 'r'){
						$showinput 				= $stypemu[$serkeys[$s]].'<input type="text" name="'.$exploarr[0].'endTime"  class="find-T" onClick="WdatePicker()"  value='.${$exploarr[0].'endTime'}.'>';
						$search_output[$s] 		= array('showinput'=>$showinput);
						$pageshow[$exploarr[0].'endTime'] 	= ${$exploarr[0].'endTime'};//增加分页额外参数。
						$bannerstr_extra_parse .= '&'.$exploarr[0].'endTime='.${$exploarr[0].'endTime'};//导出按钮链接
					}
				}
                $jslink .= "<script language='javascript' type='text/javascript' src='./staticment/js/My97DatePicker/WdatePicker.js'></script>\n";
            }
            elseif ($exploarr[1] == 'b')
            {
                $showinput          = $stypemu[$serkeys[$s]].${"$exploarr[0]str"};
                $search_output[$s]  = array('showinput'=>$showinput);
            }
        }
        if (is_array($bannerstrarr))
        {
            for ($ba = 0; $ba < count($bannerstrarr); $ba++)
            {
                $bannerstr .= '<button onclick=window.location="' . $bannerstrarr[$ba]['url'] .
                    $bannerstr_extra_parse . '" class="' . $bannerstrarr[$ba]['class'] . '" ' . $bannerstrarr[$ba]['extra'] .
                    '>' . $bannerstrarr[$ba]['value'] . '</button>';
            }
        }
        $this->V->mark(array(
            'search_output' => $search_output,
            'search_hidden' => $search_hidden,
            'jumpurl' => $jumpurl));
    }
    if (is_array($tab_menu_stypemu))
    {
        if (is_array($pageshow))
        {
            $vr = array_values($pageshow);
            $kr = array_keys($pageshow);
            for ($i = 1; $i < count($pageshow); $i++)
            {
                if ($kr[$i] != 'order')
                    $ttr .= '&' . $kr[$i] . '=' . $vr[$i];
            }
        }
        $tab_menu_output = '';
        $tab_menu_keys = array_keys($tab_menu_stypemu);
        for ($ky = 0; $ky < count($tab_menu_keys); $ky++)
        {
            $name_value = explode('-', $tab_menu_keys[$ky]);
            $tab_selected = ($$name_value['0'] == $name_value['1']) ? 'class=tab_selected' :
                '';
            $tab_menu_output .= '<td width=70 ' . $tab_selected .
                '><a href="index.php?action=' . $_GET['action'] . '&detail=' . $_GET['detail'] .
                '&' . $name_value['0'] . '=' . $name_value['1'] . $ttr . '">' . $tab_menu_stypemu["$tab_menu_keys[$ky]"] .
                '</a></td>';
        }
        $this->V->mark(array('tab_menu' => 1, 'tab_menu_output' => $tab_menu_output));
    }
    $displaykey = array_keys($displayarr);
    $numfor = count($displaykey);
    $datakey = array_keys($datalist[0]);
    if (in_array('edit', $displaykey))
    {
        if (empty($displayarr['edit']['url']))
        {
            exit('error:no modurl(edit)');
        }
        for ($j = 0; $j < count($datalist); $j++)
        {
            $url = $displayarr['edit']['url'];
            for ($c = 0; $c < count($datakey); $c++)
            {
                if (intval($datakey[$c]) == 0)
                {
                    $url = ereg_replace('\{' . $datakey["$c"] . '\}', "{$datalist[$j][$datakey[$c]]}",
                        $url);
                }
            }
            $datalist[$j]['edit'] = '<a href=' . $url .
                ' title=修改><img src="./staticment/images/editbody.gif" border="0"></a>';
        }
    } elseif (in_array('delete', $displaykey))
    {
        if (empty($displayarr['delete']['url']))
        {
            exit('error:no modurl(delete)');
        }
        for ($j = 0; $j < count($datalist); $j++)
        {
            $url = $displayarr['delete']['url'];
            for ($c = 0; $c < count($datakey); $c++)
            {
                if (intval($datakey[$c]) == 0)
                {
                    $url = ereg_replace('\{' . $datakey["$c"] . '\}', "{$datalist[$j][$datakey[$c]]}",
                        $url);
                }
            }
            if ($displayarr['delete']['ajax'] == 1)
            {
                $datalist[$j]['delete'] = '<a href=javascript:void(0);delitem("' . $url . '","' .
                    $displayarr['delete']['confirm'] .
                    '") title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
            } else
            {
                $datalist[$j]['delete'] = '<a href=' . $url .
                    ' title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
            }
        }
    } elseif (in_array('both', $displaykey))
    {
        if (empty($displayarr['both']['url_e']) || empty($displayarr['both']['url_d']))
        {
            exit('error:no modurl(both)');
        }
        for ($j = 0; $j < count($datalist); $j++)
        {
            $url_e = $displayarr['both']['url_e'];
            $url_d = $displayarr['both']['url_d'];
            for ($c = 0; $c < count($datakey); $c++)
            {
                if (intval($datakey[$c]) == 0)
                {
                    $url_e = ereg_replace('\{' . $datakey[$c] . '\}', "{$datalist[$j][$datakey[$c]]}",
                        $url_e);
                    $url_d = ereg_replace('\{' . $datakey[$c] . '\}', "{$datalist[$j][$datakey[$c]]}",
                        $url_d);
                }
            }
            $datalist[$j]['both'] = '<a href=' . $url_e .
                ' title=修改><img src="./staticment/images/editbody.gif" border="0"></a>&nbsp; ';
            $datalist[$j]['both'] .= ($displayarr['both']['ajax'] == 1) ?
                '<a href=javascript:void(0);delitem("' . $url_d . '","' . $displayarr['both']['confirm'] .
                '") title=删除>' : '<a href=' . $url_d . ' title=删除>';
            $datalist[$j]['both'] .=
                '<img src="./staticment/images/deletebody.gif" border="0"></a>';
        }
    }
    if (in_array('sysback', $displaykey))
    {
        if (empty($displayarr['sysback']['url']))
        {
            exit('error:no modurl(sysback)');
        }
        for ($j = 0; $j < count($datalist); $j++)
        {
            $url = $displayarr['sysback']['url'];
            for ($c = 0; $c < count($datakey); $c++)
            {
                if (intval($datakey[$c]) == 0)
                {
                    $url = ereg_replace('\{' . $datakey["$c"] . '\}', "{$datalist[$j][$datakey[$c]]}",
                        $url);
                }
            }
            $displayarr['sysback']['tips'] = empty($displayarr['sysback']['tips']) ? '回退' :
                $displayarr['sysback']['tips'];
            if ($displayarr['sysback']['ajax'] == 1)
            {
                $datalist[$j]['sysback'] = '<a href=javascript:void(0);delitem("' . $url . '","' .
                    $displayarr['sysback']['confirm'] . '") title=' . $displayarr['sysback']['tips'] .
                    '><img src="./staticment/images/sysback.gif" border="0"></a>';
            } else
            {
                $datalist[$j]['sysback'] = '<a href=' . $url . ' title=' . $displayarr['sysback']['tips'] .
                    '><img src="./staticment/images/sysback.gif" border="0"></a>';
            }
        }
    }
    for ($i = $numfor - 1; $i > -1; $i--)
    {
        $keyname = $displaykey[$i];
        if (!empty($displayarr[$keyname]['orderlink_asc']) || !empty($displayarr[$keyname]['orderlink_desc']))
        {
            $orderstring_same = 'border="0"  height="11" style="cursor:pointer"  usemap="#ordersimg_' .
                $i . '"';
            $displayarr[$keyname]['orderoutput'] = '<img src="./staticment/images/' . $displayarr[$keyname]['order_type'] .
                '.gif" ' . $orderstring_same . '/>';
            $displayarr[$keyname]['orderoutput'] .= '<map name="ordersimg_' . $i .
                '"><area shape="rect" href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .
                $bannerstr_extra_parse . $displayarr[$keyname]['orderlink_asc'] .
                '" coords="0,0,9,6"><area shape="rect" href="http://' . $_SERVER['HTTP_HOST'] .
                $_SERVER['REQUEST_URI'] . $bannerstr_extra_parse . $displayarr[$keyname]['orderlink_desc'] .
                '" coords="0,0,9,12"></map>';
        }
        if ($displayarr[$keyname]['clickedit'])
        {
            $cid = $displayarr[$keyname]['clickedit'];
            foreach ($datalist as &$val)
            {
                $val[$keyname] = "<span id='span_" . $keyname . '_' . $val[$cid] .
                    "' onclick=goput('" . $val[$cid] . "','" . $keyname . "') title='点击编辑'>" . $val["$keyname"] .
                    "</span>";
                $val[$keyname] .= "<input type='text' style='display:none;width:100px;'   id='input_" .
                    $keyname . '_' . $val[$cid] . "' onblur=backput('" . $val[$cid] . "','" . $keyname .
                    "','" . $_GET['action'] . "','" . $displayarr[$keyname]['detail'] . "') />";
            }
            $jslink .= "<script charset='utf-8' src='./staticment/js/clickedit.js?version=" .
                time() . "'></script>\n";
        }
        if ($displayarr[$keyname]['showname'] == 'checkbox')
        {
            $displayarr[$keyname]['showname'] = '<input type=checkbox id=CheckedAll >';
            foreach ($datalist as &$val)
            {
                $val[$keyname] = '<input type=checkbox  name=checkmod[] title=' . $val[$keyname] .
                    ' value=' . $val[$keyname] . '>';
            }
            $this->V->view['showcheckbox'] = 1;
        }
    }
    if ($displayarr['delete']['ajax'] == 1)
    {
        $delajax = 1;
    }
    $tablewidth = empty($tablewidth) ? '100%' : $tablewidth;
    $markarr = array(
        'displayarr' => $displayarr,
        'displaykey' => $displaykey,
        'datalist' => $datalist,
        'jslink' => $jslink,
        'bannerstr' => $bannerstr,
        'search_output' => $search_output,
        'delajax' => $delajax,
        'tablewidth' => $tablewidth);
    $this->V->mark($markarr);
    $this->V->set_tpl('adminweb/commom_datalist');
    unset($markarr);
    display();
}

?>