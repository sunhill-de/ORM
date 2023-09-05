<?php

namespace Sunhill\InfoMarket\Controllers;

use Illuminate\Routing\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Sunhill\ORM\Facades\InfoMarket;


class AjaxController extends Controller
{

  public function getItem(string $item="", Request $request,Response $response)
  {
      $result = InfoMarket::getItem($item);
      return json_encode($result);
  }
 
  public function getNodes(string $parent="", Request $request, Response $response)
  {
     $nodes = InfoMarket::getNodes(($parent=="!root!")?"":$parent, 'object','anybody');
     $result = [];
     foreach ($nodes as $node) {
        $node_data = [];
        if ($parent == '!root!') {
          $node_data['type'] = 'root';
          $node_data['id'] = $node->name;
          $node_data['parent'] = "#";
        } else {
          $node_data['id'] = $parent.'.'.$node->name;
          $node_data['parent'] = $parent;
        }
        $node_data['text'] = $node->name;
        if ($node->semantic == 'Branch') {
          $node_data["icon"] = "fa fa-folder icon-lg";
          $node_data["children"] = true;
        } else {
          $node_data["icon"] = "fa fa-file fa-large kt-font-default";        
          $node_data["children"] = false;     
        }  
        $result[] = $node_data;
     } 
     
     return response()->json($result);
  }
  
}
