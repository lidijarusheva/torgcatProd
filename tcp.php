public function getTorgCatProducts($arFilter,$arSelect=array("*"),$arSort=array("ID"=>"ASC"),$pageParams = false)
	{
		if(!CModule::IncludeModule("catalog")) return false;
        if(empty($arFilter)) return false;
        $arResult = false;

        // $obCache = new CPHPCache;
        // $life_time = 3600;
        // $cache_params = $arFilter;
        // $cache_params['key'] = $key;
        // $cache_params['func']='getTorgCatProduct';
        // $cache_params['arSelect']=$arSelect;
        // $cache_params['sort']=$arSort;
        // $cache_params['pageParams']=$pageParams;
        // $cache_id = md5(serialize($cache_params));
        // if($obCache->InitCache($life_time, $cache_id, "/")) :
        //     $arResult = $obCache->GetVars();
        // else :
			$extra = $this->getExtra();
			$measure = $this->getMeasure();

			$db_res = CCatalogProduct::GetList($arSort, $arFilter, false, $pageParams, $arSelect);
			while ($ar_res = $db_res->Fetch())
			{
				$ar_res['MEASURE'] = $measure[$ar_res['MEASURE']];
				$arResult[$ar_res['ID']] = $ar_res;
			}

			$rsRatios = CCatalogMeasureRatio::getList(array(), array('PRODUCT_ID' => $arFilter['ID']), false, false, array('PRODUCT_ID', 'RATIO'));
	        while ($arRatio = $rsRatios->Fetch()) {
	            $arResult[$arRatio['PRODUCT_ID']]['MEASURE_RATIO'] = $arRatio;
	        }

	      	$rsPrices = CPrice::GetListEx(array(), array('PRODUCT_ID' => $arFilter['ID']), false, false, array('*'));

	        while ($arPrice = $rsPrices->Fetch()) {
	        	$arPrice['EXTRA_ID'] = $extra[$arPrice['EXTRA_ID']]['NAME'];
	        	if($arPrice['CATALOG_GROUP_CODE'] == 'BASE'){
	            	$arResult[$arPrice['PRODUCT_ID']]['PRICE'][1] = $arPrice;
	    		}elseif($arPrice['CATALOG_GROUP_CODE'] == 'RETAIL'){
	    			$arResult[$arPrice['PRODUCT_ID']]['PRICE'][2] = $arPrice;
	    		}

	        }

        // endif;
 
        // if($obCache->StartDataCache()):
        //     $obCache->EndDataCache($arResult);
        // endif;
 
        return $arResult;
	}
